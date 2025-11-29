import React, { useState, useEffect } from 'react';
import toast from 'react-hot-toast';
import { SkeletonTickets } from '../components/SkeletonLoader';
import { ticketService } from '../services/api';

const TicketsPage: React.FC = () => {
  const [showCreateForm, setShowCreateForm] = useState(false);
  const [tickets, setTickets] = useState<any[]>([]);
  const [loading, setLoading] = useState(true);
  const [creating, setCreating] = useState(false);
  const [newTicket, setNewTicket] = useState({
    judul: '',
    deskripsi: '',
    kategori: '',
    prioritas: 'medium'
  });

  useEffect(() => {
    const fetchTickets = async () => {
      try {
        setLoading(true);
        const response = await ticketService.getTickets();
        if (response.success && response.data) {
          // Handle paginated response
          const ticketsData = Array.isArray(response.data) 
            ? response.data 
            : (response.data as any).data || [];
          setTickets(ticketsData);
        } else {
          setTickets([]);
        }
      } catch (error: any) {
        console.error('Error fetching tickets:', error);
        // Don't show toast for 404 errors (no tickets yet)
        if (error.response?.status !== 404) {
          toast.error(error.message || 'Gagal memuat tickets');
        }
        setTickets([]);
      } finally {
        setLoading(false);
      }
    };

    fetchTickets();
  }, []);

  const handleCreateTicket = async (e: React.FormEvent) => {
    e.preventDefault();
    
    if (!newTicket.judul || !newTicket.deskripsi || !newTicket.kategori) {
      toast.error('Harap lengkapi semua field yang wajib');
      return;
    }

    setCreating(true);

    try {
      const response = await ticketService.createTicket(newTicket);
      
      if (response.success) {
        toast.success('Ticket berhasil dibuat');
        
        // Refresh tickets list
        try {
          const ticketsResponse = await ticketService.getTickets();
          if (ticketsResponse.success && ticketsResponse.data) {
            // Handle paginated response
            const ticketsData = Array.isArray(ticketsResponse.data) 
              ? ticketsResponse.data 
              : (ticketsResponse.data as any).data || [];
            setTickets(ticketsData);
          }
        } catch (err) {
          console.error('Error refreshing tickets:', err);
          // Still refresh the list even if there's an error
          const ticketsResponse = await ticketService.getTickets();
          if (ticketsResponse.success && ticketsResponse.data) {
            const ticketsData = Array.isArray(ticketsResponse.data) 
              ? ticketsResponse.data 
              : (ticketsResponse.data as any).data || [];
            setTickets(ticketsData);
          }
        }

        setShowCreateForm(false);
        setNewTicket({ judul: '', deskripsi: '', kategori: '', prioritas: 'medium' });
      } else {
        throw new Error(response.message || 'Gagal membuat ticket');
      }
    } catch (error: any) {
      console.error('Error creating ticket:', error);
      const errorMessage = error.response?.data?.message || error.message || 'Gagal membuat ticket';
      toast.error(errorMessage);
      
      // Show validation errors if any
      if (error.response?.data?.errors) {
        const errors = error.response.data.errors;
        const errorList = Object.values(errors).flat().join(', ');
        toast.error(`Validasi gagal: ${errorList}`);
      }
    } finally {
      setCreating(false);
    }
  };

  if (loading) {
    return <SkeletonTickets />;
  }

  return (
    <div className="space-y-4 md:space-y-6">
      <div className="bg-white shadow">
        <div className="px-4 py-5 sm:px-6">
          <div className="flex flex-col sm:flex-row sm:justify-between sm:items-center gap-4">
            <div>
              <h1 className="text-xl sm:text-2xl font-bold text-gray-900">Support Tickets</h1>
              <p className="mt-1 text-sm text-gray-500">Kelola dan buat support tickets</p>
            </div>
            <button
              onClick={() => setShowCreateForm(true)}
              className="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-md text-sm font-medium w-full sm:w-auto"
            >
              Buat Ticket Baru
            </button>
          </div>
        </div>
      </div>

      {/* Create Ticket Modal */}
      {showCreateForm && (
        <div className="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50">
          <div className="relative top-10 sm:top-20 mx-auto p-5 border w-11/12 sm:w-96 shadow-lg rounded-md bg-white">
            <div className="mt-3">
              <h3 className="text-lg font-medium text-gray-900 mb-4">Buat Ticket Baru</h3>
              <form onSubmit={handleCreateTicket} className="space-y-4">
                <div>
                  <label className="block text-sm font-medium text-gray-700">Judul</label>
                  <input
                    id="ticket-title"
                    name="ticket-title"
                    type="text"
                    required
                    className="mt-1 block w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-blue-500 focus:border-blue-500"
                    value={newTicket.judul}
                    onChange={(e) => setNewTicket({...newTicket, judul: e.target.value})}
                  />
                </div>
                <div>
                  <label className="block text-sm font-medium text-gray-700">Kategori</label>
                  <select
                    required
                    className="mt-1 block w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-blue-500 focus:border-blue-500"
                    value={newTicket.kategori}
                    onChange={(e) => setNewTicket({...newTicket, kategori: e.target.value})}
                  >
                    <option value="">Pilih Kategori</option>
                    <option value="technical">Teknis</option>
                    <option value="billing">Billing</option>
                    <option value="service">Layanan</option>
                    <option value="other">Lainnya</option>
                  </select>
                </div>
                <div>
                  <label className="block text-sm font-medium text-gray-700">Prioritas</label>
                  <select
                    className="mt-1 block w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-blue-500 focus:border-blue-500"
                    value={newTicket.prioritas}
                    onChange={(e) => setNewTicket({...newTicket, prioritas: e.target.value})}
                  >
                    <option value="low">Rendah</option>
                    <option value="medium">Sedang</option>
                    <option value="high">Tinggi</option>
                  </select>
                </div>
                <div>
                  <label className="block text-sm font-medium text-gray-700">Deskripsi</label>
                  <textarea
                    required
                    rows={4}
                    className="mt-1 block w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-blue-500 focus:border-blue-500"
                    value={newTicket.deskripsi}
                    onChange={(e) => setNewTicket({...newTicket, deskripsi: e.target.value})}
                  />
                </div>
                <div className="flex justify-end space-x-3">
                  <button
                    type="button"
                    onClick={() => setShowCreateForm(false)}
                    className="px-4 py-2 border border-gray-300 rounded-md text-sm font-medium text-gray-700 hover:bg-gray-50"
                  >
                    Batal
                  </button>
                  <button
                    type="submit"
                    disabled={creating}
                    className="px-4 py-2 bg-blue-600 border border-transparent rounded-md text-sm font-medium text-white hover:bg-blue-700 disabled:opacity-50 disabled:cursor-not-allowed"
                  >
                    {creating ? 'Membuat...' : 'Buat Ticket'}
                  </button>
                </div>
              </form>
            </div>
          </div>
        </div>
      )}

      {/* Tickets List */}
      <div className="bg-white shadow overflow-hidden sm:rounded-md">
        <div className="px-4 py-5 sm:px-6">
          <h3 className="text-lg leading-6 font-medium text-gray-900">Daftar Tickets</h3>
        </div>
        {tickets.length > 0 ? (
          <ul className="divide-y divide-gray-200">
            {tickets.map((ticket) => (
              <li key={ticket.id}>
                <div className="px-4 py-4 sm:px-6">
                  <div className="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
                    <div className="flex items-start">
                      <div className="flex-shrink-0">
                        <svg className="h-8 w-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                          <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M15 5v2m0 4v2m0 4v2M5 5a2 2 0 00-2 2v3a2 2 0 110 4v3a2 2 0 002 2h14a2 2 0 002-2v-3a2 2 0 110-4V7a2 2 0 00-2-2H5z" />
                        </svg>
                      </div>
                      <div className="ml-4 flex-1 min-w-0">
                        <div className="text-sm font-medium text-gray-900 truncate">{ticket.kode_ticket}</div>
                        <div className="text-sm text-gray-500 truncate">{ticket.judul}</div>
                        <div className="text-sm text-gray-500">{ticket.kategori} • {ticket.prioritas}</div>
                        <div className="text-xs text-gray-400">
                          Dibuat: {new Date(ticket.created_at).toLocaleDateString('id-ID')}
                        </div>
                      </div>
                    </div>
                    <div className="flex flex-col sm:flex-row sm:items-center gap-2 sm:ml-4">
                      <span className={`inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium ${
                        ticket.status === 'open' ? 'bg-red-100 text-red-800' :
                        ticket.status === 'resolved' ? 'bg-green-100 text-green-800' :
                        ticket.status === 'in_progress' ? 'bg-yellow-100 text-yellow-800' :
                        'bg-gray-100 text-gray-800'
                      }`}>
                        {ticket.status === 'open' ? 'Terbuka' :
                         ticket.status === 'resolved' ? 'Selesai' :
                         ticket.status === 'in_progress' ? 'Diproses' :
                         ticket.status}
                      </span>
                      <button className="text-blue-600 hover:text-blue-900 text-sm font-medium w-full sm:w-auto text-left sm:text-center">
                        Lihat Detail
                      </button>
                    </div>
                  </div>
                </div>
              </li>
            ))}
          </ul>
        ) : (
          <div className="px-4 py-8 text-center">
            <svg className="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M15 5v2m0 4v2m0 4v2M5 5a2 2 0 00-2 2v3a2 2 0 110 4v3a2 2 0 002 2h14a2 2 0 002-2v-3a2 2 0 110-4V7a2 2 0 00-2-2H5z" />
            </svg>
            <h3 className="mt-2 text-sm font-medium text-gray-900">Belum ada tickets</h3>
            <p className="mt-1 text-sm text-gray-500">Mulai dengan membuat ticket baru untuk mendapatkan bantuan.</p>
          </div>
        )}
      </div>
    </div>
  );
};

export default TicketsPage;
