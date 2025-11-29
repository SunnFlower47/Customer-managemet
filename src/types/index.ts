export interface Customer {
  id: number;
  nama: string;
  no_hp: string;
  pppoe: string;
  alamat: string;
  status: string;
  is_default_password: boolean;
  last_login_at: string | null;
  paket: {
    id: number;
    nama_paket: string;
    harga: number;
    deskripsi: string;
  } | null;
  penagih: {
    id: number;
    nama: string;
    no_hp: string;
  } | null;
}

export interface LoginRequest {
  username: string; // Can be no_hp or pppoe
  password: string;
}

export interface LoginResponse {
  success: boolean;
  message: string;
  data: {
    customer: Customer;
    token: string;
    token_type: string;
  };
}

export interface Payment {
  id: number;
  kode_pembayaran: string;
  harga_paket: number;
  tanggal_jatuh_tempo: string;
  status: string;
  paket: {
    nama_paket: string;
  };
}

export interface PaymentProof {
  id: number;
  status: string;
  file_url: string;
  admin_notes: string | null;
  verified_at: string | null;
}

export interface Ticket {
  id: number;
  kode_ticket: string;
  judul: string;
  deskripsi: string;
  kategori: string;
  prioritas: string;
  status: string;
  created_at: string;
  comments: TicketComment[];
  attachments: TicketAttachment[];
}

export interface TicketComment {
  id: number;
  comment: string;
  created_at: string;
  user: {
    name: string;
  } | null;
}

export interface TicketAttachment {
  id: number;
  filename: string;
  file_url: string;
  file_type: string;
  file_size: number;
}

export interface ApiResponse<T> {
  success: boolean;
  message: string;
  data: T;
  pagination?: {
    current_page: number;
    last_page: number;
    per_page: number;
    total: number;
  };
}
