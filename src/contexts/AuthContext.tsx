import React, { createContext, useContext, useState, useEffect, ReactNode } from 'react';
import { Customer } from '../types';
import { authService } from '../services/api';

interface AuthContextType {
  customer: Customer | null;
  token: string | null;
  login: (username: string, password: string) => Promise<void>;
  logout: () => void;
  updateCustomer: (customerData: Partial<Customer>) => void;
  refreshCustomer: () => Promise<void>;
  loading: boolean;
  isAuthenticated: boolean;
}

const AuthContext = createContext<AuthContextType | undefined>(undefined);

export const useAuth = () => {
  const context = useContext(AuthContext);
  if (context === undefined) {
    throw new Error('useAuth must be used within an AuthProvider');
  }
  return context;
};

interface AuthProviderProps {
  children: ReactNode;
}

export const AuthProvider: React.FC<AuthProviderProps> = ({ children }) => {
  const [customer, setCustomer] = useState<Customer | null>(null);
  const [token, setToken] = useState<string | null>(null);
  const [loading, setLoading] = useState(true);
  const [lastActivity, setLastActivity] = useState<number>(Date.now());
  const isAuth = !!customer && !!token;

  useEffect(() => {
    const initializeAuth = async () => {
      try {
        const storedToken = localStorage.getItem('token');
        const storedCustomer = localStorage.getItem('customer');

        if (storedToken && storedCustomer) {
          try {
            const customerData = JSON.parse(storedCustomer);
            
            // Set token and customer from storage first (optimistic)
            setToken(storedToken);
            setCustomer(customerData);
            
            // Then verify token in background (silently, don't block UI)
            // Use a timeout to prevent hanging
            const verifyToken = async () => {
              try {
                const meResponse = await authService.me();
                
                // If verification succeeds, update with fresh data from API
                if (meResponse.success && meResponse.data) {
                  setCustomer(meResponse.data); // Use fresh data from API
                  // Update localStorage with fresh data
                  localStorage.setItem('customer', JSON.stringify(meResponse.data));
                }
              } catch (error: any) {
                // Only clear if it's a 401 (unauthorized) or 403 (forbidden)
                // Don't clear on network errors, timeouts, or rate limits
                const status = error.response?.status;
                const isNetworkError = !error.response || error.code === 'ECONNABORTED' || error.code === 'ERR_NETWORK';
                
                if (status === 401 || status === 403) {
                  console.warn('Token invalid, clearing auth data');
                  localStorage.removeItem('token');
                  localStorage.removeItem('customer');
                  setToken(null);
                  setCustomer(null);
                } else if (isNetworkError) {
                  // Network error - keep existing token, user can still use the app
                  console.warn('Network error during token verification, keeping existing session');
                  // Don't show error to user, they can still use cached data
                } else {
                  // Other errors (rate limit, etc) - keep existing token
                  console.warn('Token verification failed (non-auth error), keeping existing session');
                }
              }
            };
            
            // Verify in background, don't await (non-blocking)
            verifyToken();
          } catch (parseError) {
            // Invalid JSON, clear storage
            console.error('Invalid customer data in storage:', parseError);
            localStorage.removeItem('token');
            localStorage.removeItem('customer');
            setToken(null);
            setCustomer(null);
          }
        } else {
          // No stored token/customer, ensure clean state
          setToken(null);
          setCustomer(null);
        }
      } catch (error) {
        console.error('Auth initialization error:', error);
        // Don't clear token on initialization error - might be network issue
        // Only clear if we're sure it's invalid
        const storedToken = localStorage.getItem('token');
        const storedCustomer = localStorage.getItem('customer');
        
        if (storedToken && storedCustomer) {
          // Keep existing token, might be network issue
          try {
            const customerData = JSON.parse(storedCustomer);
            setToken(storedToken);
            setCustomer(customerData);
          } catch (parseError) {
            // Only clear if JSON is invalid
            localStorage.removeItem('token');
            localStorage.removeItem('customer');
            setToken(null);
            setCustomer(null);
          }
        } else {
          // No stored data, ensure clean state
          setToken(null);
          setCustomer(null);
        }
      } finally {
        setLoading(false);
      }
    };

    initializeAuth();
  }, []);

  // Session timeout logic
  useEffect(() => {
    if (!isAuth) return;

    const SESSION_TIMEOUT = 30 * 60 * 1000; // 30 minutes
    const checkSessionTimeout = () => {
      const now = Date.now();
      if (now - lastActivity > SESSION_TIMEOUT) {
        console.log('Session timeout - logging out');
        logout();
      }
    };

    // Check every minute
    const interval = setInterval(checkSessionTimeout, 60000);

    // Update last activity on user interaction
    const updateActivity = () => setLastActivity(Date.now());

    document.addEventListener('mousedown', updateActivity);
    document.addEventListener('keypress', updateActivity);
    document.addEventListener('scroll', updateActivity);
    document.addEventListener('touchstart', updateActivity);

    return () => {
      clearInterval(interval);
      document.removeEventListener('mousedown', updateActivity);
      document.removeEventListener('keypress', updateActivity);
      document.removeEventListener('scroll', updateActivity);
      document.removeEventListener('touchstart', updateActivity);
    };
  }, [isAuth, lastActivity]);

  const login = async (username: string, password: string) => {
    try {
      const response = await authService.login({ username, password });

      if (response.success && response.data) {
        const { customer: customerData, token: authToken } = response.data;

        if (!customerData || !authToken) {
          throw new Error('Invalid response from server');
        }

        setCustomer(customerData);
        setToken(authToken);

        localStorage.setItem('token', authToken);
        localStorage.setItem('customer', JSON.stringify(customerData));
      } else {
        throw new Error(response.message || 'Login failed');
      }
    } catch (error: any) {
      const errorMessage = error.response?.data?.message || error.message || 'Login failed';
      throw new Error(errorMessage);
    }
  };

  const logout = async () => {
    try {
      await authService.logout();
    } catch (error) {
      console.error('Logout error:', error);
    } finally {
      setCustomer(null);
      setToken(null);
      localStorage.removeItem('token');
      localStorage.removeItem('customer');
    }
  };

  const updateCustomer = (customerData: Partial<Customer>) => {
    if (customer) {
      const updatedCustomer = { ...customer, ...customerData };
      setCustomer(updatedCustomer);
      localStorage.setItem('customer', JSON.stringify(updatedCustomer));
    }
  };

  const refreshCustomer = async () => {
    try {
      const meResponse = await authService.me();
      if (meResponse.success && meResponse.data) {
        setCustomer(meResponse.data);
        localStorage.setItem('customer', JSON.stringify(meResponse.data));
      }
    } catch (error: any) {
      // Only clear if it's a 401/403
      const status = error.response?.status;
      if (status === 401 || status === 403) {
        console.warn('Token invalid during refresh, logging out');
        logout();
      } else {
        // Network error or other - keep existing customer data
        console.warn('Failed to refresh customer data, keeping existing');
      }
    }
  };

  const value: AuthContextType = {
    customer,
    token,
    login,
    logout,
    updateCustomer,
    refreshCustomer,
    loading,
    isAuthenticated: isAuth,
  };

  return (
    <AuthContext.Provider value={value}>
      {children}
    </AuthContext.Provider>
  );
};
