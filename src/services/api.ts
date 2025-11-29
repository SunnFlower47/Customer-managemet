import axios from 'axios';
import toast from 'react-hot-toast';
import { LoginRequest, LoginResponse, ApiResponse, Payment, PaymentProof, Ticket } from '../types';
import { cacheService, CACHE_KEYS } from './cache';

import { API_CONFIG } from '../config/api';

const API_BASE_URL = API_CONFIG.BASE_URL;

const api = axios.create({
  baseURL: API_BASE_URL,
  headers: {
    'Content-Type': 'application/json',
  },
  timeout: 10000, // 10 seconds timeout
});

// Request interceptor to add auth token
api.interceptors.request.use(
  (config) => {
    const token = localStorage.getItem('token');
    if (token) {
      config.headers.Authorization = `Bearer ${token}`;
    }
    return config;
  },
  (error) => {
    return Promise.reject(error);
  }
);

// Response interceptor to handle auth errors
api.interceptors.response.use(
  (response) => response,
  (error) => {
    const status = error.response?.status;
    const message = error.response?.data?.message || error.message || 'Terjadi kesalahan';
    const url = error.config?.url || '';
    const isAuthEndpoint = url.includes('/auth/') || url.includes('/customer/auth/');

    // Network error (no response) - be more lenient
    if (!error.response) {
      // Only show toast for non-auth endpoints to avoid spam during initialization
      if (!isAuthEndpoint && !url.includes('/me')) {
        toast.error('Tidak dapat terhubung ke server. Periksa koneksi Anda.');
      }
      return Promise.reject(error);
    }

    if (status === 401) {
      // Only auto-logout if not on login page (avoid redirect loop)
      const isLoginPage = window.location.pathname === '/login';
      const isAuthEndpoint = url.includes('/me') || url.includes('/auth/');
      
      if (!isLoginPage && !isAuthEndpoint) {
        // Only show toast and logout for non-auth endpoints
        // Auth endpoints (like /me) are handled in AuthContext
        toast.error('Sesi berakhir, silakan login kembali.');
        localStorage.removeItem('token');
        localStorage.removeItem('customer');
        window.location.href = '/login';
      }
      return Promise.reject(error);
    }

    if (status === 429) {
      const retryAfter = error.response?.headers?.['retry-after'] || error.response?.headers?.['Retry-After'] || 60;
      const message = error.response?.data?.message || `Terlalu banyak permintaan. Coba lagi dalam ${Math.ceil(retryAfter)} detik.`;
      toast.error(message);
      return Promise.reject(error);
    }

    // Only show toast for non-404 errors (404 is often expected)
    if (status && status >= 500) {
      toast.error('Gangguan server. Coba lagi nanti.');
    } else if (status === 404 && !url.includes('/me')) {
      // Don't show toast for 404 on /me endpoint (it's handled in AuthContext)
      toast.error('Data tidak ditemukan.');
    } else if (status !== 404) {
      toast.error(message);
    }

    return Promise.reject(error);
  }
);

export const authService = {
  login: async (credentials: LoginRequest): Promise<LoginResponse> => {
    const response = await api.post('/customer/auth/login', credentials);
    return response.data;
  },

  logout: async (): Promise<ApiResponse<null>> => {
    const response = await api.post('/customer/auth/logout');
    return response.data;
  },

  me: async (): Promise<ApiResponse<any>> => {
    const response = await api.get('/customer/auth/me');
    return response.data;
  },

  changePassword: async (data: {
    current_password: string;
    new_password: string;
    new_password_confirmation: string;
  }): Promise<ApiResponse<null>> => {
    const response = await api.post('/customer/auth/change-password', data);
    return response.data;
  },
};

export const paymentService = {
  getUnpaidBills: async (forceRefresh = false): Promise<ApiResponse<Payment[]>> => {
    const cacheKey = CACHE_KEYS.UNPAID_BILLS;
    
    // Check cache first (unless force refresh)
    if (!forceRefresh) {
      const cached = cacheService.get<ApiResponse<Payment[]>>(cacheKey);
      if (cached) {
        return cached;
      }
    }

    const response = await api.get('/customer/payment/bills');
    const data = response.data;
    
    // Cache for 2 minutes (unpaid bills change frequently)
    cacheService.set(cacheKey, data, 2 * 60 * 1000);
    
    return data;
  },

  getPaymentHistory: async (page = 1, forceRefresh = false): Promise<ApiResponse<Payment[]>> => {
    const cacheKey = CACHE_KEYS.PAYMENT_HISTORY(page);
    
    // Check cache first (unless force refresh)
    if (!forceRefresh) {
      const cached = cacheService.get<ApiResponse<Payment[]>>(cacheKey);
      if (cached) {
        return cached;
      }
    }

    const response = await api.get(`/customer/payment/history?page=${page}`);
    // Handle paginated response
    let data = response.data;
    if (data.data && Array.isArray(data.data)) {
      data = response.data;
    } else if (Array.isArray(data.data)) {
      data = response.data;
    }
    
    // Cache for 5 minutes (payment history doesn't change as frequently)
    cacheService.set(cacheKey, data, 5 * 60 * 1000);
    
    return data;
  },

  uploadPaymentProof: async (formData: FormData): Promise<ApiResponse<any>> => {
    const response = await api.post('/customer/payment/upload-proof', formData, {
      headers: {
        'Content-Type': 'multipart/form-data',
      },
    });
    
    // Invalidate payment-related cache after upload
    cacheService.remove(CACHE_KEYS.UNPAID_BILLS);
    cacheService.clearByPattern('payment:history:*');
    cacheService.remove(CACHE_KEYS.STATISTICS);
    
    return response.data;
  },

  sendToWhatsApp: async (data: {
    pembayaran_id: number;
    message: string;
  }): Promise<ApiResponse<null>> => {
    const response = await api.post('/customer/payment/send-wa', data);
    return response.data;
  },

  getPaymentStatus: async (id: number): Promise<ApiResponse<{
    pembayaran: Payment;
    payment_proofs: PaymentProof[];
  }>> => {
    const response = await api.get(`/customer/payment/status/${id}`);
    return response.data;
  },
};

export const ticketService = {
  getTickets: async (status?: string, page = 1, forceRefresh = false): Promise<ApiResponse<Ticket[]>> => {
    const cacheKey = CACHE_KEYS.TICKETS(status || 'all', page);
    
    // Check cache first (unless force refresh)
    if (!forceRefresh) {
      const cached = cacheService.get<ApiResponse<Ticket[]>>(cacheKey);
      if (cached) {
        return cached;
      }
    }

    const params = new URLSearchParams();
    if (status) params.append('status', status);
    params.append('page', page.toString());
    
    const response = await api.get(`/customer/support/tickets?${params}`);
    const data = response.data;
    
    // Cache for 2 minutes (tickets can change frequently)
    cacheService.set(cacheKey, data, 2 * 60 * 1000);
    
    return data;
  },

  createTicket: async (data: {
    judul: string;
    deskripsi: string;
    kategori: string;
    prioritas: string;
  }): Promise<ApiResponse<Ticket>> => {
    const response = await api.post('/customer/support/tickets', data);
    
    // Invalidate tickets cache after creating new ticket
    cacheService.clearByPattern('tickets:*');
    cacheService.remove(CACHE_KEYS.STATISTICS);
    
    return response.data;
  },

  getTicket: async (id: number): Promise<ApiResponse<Ticket>> => {
    const response = await api.get(`/customer/support/tickets/${id}`);
    return response.data;
  },

  addComment: async (ticketId: number, comment: string): Promise<ApiResponse<null>> => {
    const response = await api.post(`/customer/support/tickets/${ticketId}/comments`, {
      comment,
    });
    
    // Invalidate tickets cache after adding comment
    cacheService.clearByPattern('tickets:*');
    
    return response.data;
  },

  uploadAttachment: async (ticketId: number, file: File): Promise<ApiResponse<any>> => {
    const formData = new FormData();
    formData.append('attachment', file);
    
    const response = await api.post(`/customer/support/tickets/${ticketId}/attachments`, formData, {
      headers: {
        'Content-Type': 'multipart/form-data',
      },
    });
    return response.data;
  },

  rateTicket: async (ticketId: number, data: {
    rating: number;
    customer_feedback: string;
  }): Promise<ApiResponse<null>> => {
    const response = await api.post(`/customer/support/tickets/${ticketId}/rate`, data);
    return response.data;
  },
};

export const paymentMethodsService = {
  getPaymentMethods: async (forceRefresh = false): Promise<ApiResponse<{
    dana_phone: string | null;
    mandiri_account: string | null;
    mandiri_account_name: string | null;
    payment_whatsapp: string | null;
    company_name: string | null;
  }>> => {
    const cacheKey = CACHE_KEYS.PAYMENT_METHODS;
    
    // Check cache first (unless force refresh)
    if (!forceRefresh) {
      const cached = cacheService.get<ApiResponse<any>>(cacheKey);
      if (cached) {
        return cached;
      }
    }

    const response = await api.get('/payment-methods');
    const data = response.data;
    
    // Cache for 60 minutes (payment methods rarely change)
    cacheService.set(cacheKey, data, 60 * 60 * 1000);
    
    return data;
  },
};

export const profileService = {
  getProfile: async (forceRefresh = false): Promise<ApiResponse<any>> => {
    const cacheKey = CACHE_KEYS.PROFILE;
    
    // Check cache first (unless force refresh)
    if (!forceRefresh) {
      const cached = cacheService.get<ApiResponse<any>>(cacheKey);
      if (cached) {
        return cached;
      }
    }

    const response = await api.get('/customer/profile/');
    const data = response.data;
    
    // Cache for 5 minutes
    cacheService.set(cacheKey, data, 5 * 60 * 1000);
    
    return data;
  },

  updateProfile: async (data: {
    nama?: string;
    email?: string;
    alamat?: string;
  }): Promise<ApiResponse<any>> => {
    const response = await api.put('/customer/profile/', data);
    
    // Invalidate profile cache after update
    cacheService.remove(CACHE_KEYS.PROFILE);
    
    return response.data;
  },

  changePassword: async (data: {
    current_password: string;
    new_password: string;
    new_password_confirmation: string;
  }): Promise<ApiResponse<null>> => {
    const response = await api.post('/customer/profile/change-password', data);
    return response.data;
  },

  getStatistics: async (forceRefresh = false): Promise<ApiResponse<{
    total_payments: number;
    paid_payments: number;
    unpaid_payments: number;
    total_tickets: number;
    resolved_tickets: number;
    open_tickets: number;
    average_rating: number;
  }>> => {
    const cacheKey = CACHE_KEYS.STATISTICS;
    
    // Check cache first (unless force refresh)
    if (!forceRefresh) {
      const cached = cacheService.get<ApiResponse<any>>(cacheKey);
      if (cached) {
        return cached;
      }
    }

    const response = await api.get('/customer/profile/statistics');
    const data = response.data;
    
    // Cache for 3 minutes (statistics change moderately)
    cacheService.set(cacheKey, data, 3 * 60 * 1000);
    
    return data;
  },
};

export default api;
