import React, { useState, useEffect } from 'react';
import Swal from 'sweetalert2';
import toast from 'react-hot-toast';
import { SkeletonPayments } from '../components/SkeletonLoader';
import { PageHeader, Card, CardHeader, ActionButton } from '../components/DesignSystem';
import { paymentService, paymentMethodsService } from '../services/api';

const PaymentsPage: React.FC = () => {
  const [activeTab, setActiveTab] = useState('unpaid');
  const [unpaidBills, setUnpaidBills] = useState<any[]>([]);
  const [paymentHistory, setPaymentHistory] = useState<any[]>([]);
  const [loading, setLoading] = useState(true);
  const [showUploadModal, setShowUploadModal] = useState(false);
  const [selectedBill, setSelectedBill] = useState<any>(null);
  const [uploadFile, setUploadFile] = useState<File | null>(null);
  const [uploading, setUploading] = useState(false);
  const [paymentMethods, setPaymentMethods] = useState<any>(null);

  useEffect(() => {
    const fetchData = async () => {
      try {
        setLoading(true);

        // Fetch unpaid bills
        try {
          const billsResponse = await paymentService.getUnpaidBills();
          if (billsResponse.success && billsResponse.data) {
            setUnpaidBills(billsResponse.data);
          }
        } catch (err: any) {
          console.error('Error fetching unpaid bills:', err);
          toast.error(err.message || 'Gagal memuat tagihan belum dibayar');
        }

        // Fetch payment history
        try {
          const historyResponse = await paymentService.getPaymentHistory(1);
          if (historyResponse.success && historyResponse.data) {
            // Handle paginated response
            const historyData = Array.isArray(historyResponse.data) 
              ? historyResponse.data 
              : (historyResponse.data as any).data || [];
            setPaymentHistory(historyData);
          }
        } catch (err: any) {
          console.error('Error fetching payment history:', err);
          // Fallback to empty array if API fails
          setPaymentHistory([]);
        }

        // Fetch payment methods (for WhatsApp number)
        try {
          const methodsResponse = await paymentMethodsService.getPaymentMethods();
          if (methodsResponse.success && methodsResponse.data) {
            setPaymentMethods(methodsResponse.data);
          }
        } catch (err: any) {
          console.error('Error fetching payment methods:', err);
          // Continue without payment methods
        }
      } catch (error: any) {
        console.error('Error fetching payment data:', error);
        toast.error('Gagal memuat data pembayaran');
      } finally {
        setLoading(false);
      }
    };

    fetchData();
  }, []);

  const handleUploadClick = (bill: any) => {
    if (!bill || !bill.id) {
      toast.error('Data tagihan tidak valid');
      return;
    }
    setSelectedBill(bill);
    setShowUploadModal(true);
  };

  const handleWhatsAppClick = (bill: any) => {
    const userData = JSON.parse(localStorage.getItem('customer') || '{}');
    
    // Get WhatsApp number from payment methods API
    let whatsappNumber = '6287726661964'; // Fallback default
    if (paymentMethods?.payment_whatsapp) {
      // Remove any non-numeric characters and format
      whatsappNumber = paymentMethods.payment_whatsapp.replace(/[^0-9]/g, '');
      // Remove leading 0 if present
      if (whatsappNumber.startsWith('0')) {
        whatsappNumber = '62' + whatsappNumber.substring(1);
      }
      // Add 62 if not present
      if (!whatsappNumber.startsWith('62')) {
        whatsappNumber = '62' + whatsappNumber;
      }
    }
    
    const message = `Halo, saya ingin mengirim bukti pembayaran untuk tagihan:

📋 *Detail Tagihan:*
• Kode: ${bill.kode_pembayaran}
• Paket: ${bill?.package_info?.nama_paket || bill?.paket?.nama_paket || '-'}
• Nominal: Rp ${Number(bill?.package_info?.harga_paket ?? bill?.jumlah ?? 0).toLocaleString('id-ID')}
• Jatuh Tempo: ${bill?.due_date || bill?.tanggal_jatuh_tempo || '-'}

👤 *Data Pelanggan:*
• Nama: ${userData.nama}
• No HP: ${userData.no_hp}
• PPPoE: ${userData.pppoe}

Mohon konfirmasi setelah saya kirim bukti pembayaran. Terima kasih!`;

    const whatsappUrl = `https://wa.me/${whatsappNumber}?text=${encodeURIComponent(message)}`;
    window.open(whatsappUrl, '_blank');
  };

  const handleFileChange = (e: React.ChangeEvent<HTMLInputElement>) => {
    if (e.target.files && e.target.files[0]) {
      const file = e.target.files[0];
      // Validate file size (max 5MB)
      if (file.size > 5 * 1024 * 1024) {
        toast.error('Ukuran file maksimal 5MB');
        return;
      }
      // Validate file type
      const allowedTypes = ['image/jpeg', 'image/jpg', 'image/png', 'application/pdf'];
      if (!allowedTypes.includes(file.type)) {
        toast.error('Format file harus JPG, PNG, atau PDF');
        return;
      }
      setUploadFile(file);
    }
  };

  const handleUpload = async () => {
    if (!uploadFile || !selectedBill) return;

    setUploading(true);
    try {
      const token = localStorage.getItem('token');
      if (!token) return;

      const formData = new FormData();
      formData.append('proof_file', uploadFile);
      formData.append('pembayaran_id', selectedBill.id.toString());

      // Use payment service for upload
      const response = await paymentService.uploadPaymentProof(formData);

      if (response.success) {
        // Get WhatsApp number from payment methods
        let whatsappNumber = '+6287726661964'; // Fallback default
        if (paymentMethods?.payment_whatsapp) {
          whatsappNumber = paymentMethods.payment_whatsapp;
        }
        
        await Swal.fire({
          icon: 'success',
          title: 'Upload Berhasil!',
          html: `
            <div class="text-left">
              <p class="mb-3">Bukti pembayaran berhasil diupload!</p>
              <div class="bg-green-50 p-3 rounded-lg border border-green-200 mb-3">
                <p class="text-sm font-medium text-green-800 mb-2">📱 Untuk validasi cepat:</p>
                <p class="text-sm text-green-700">Silakan kirim bukti pembayaran ke WhatsApp admin:</p>
                <p class="text-sm font-bold text-green-800">${whatsappNumber}</p>
              </div>
              <div class="bg-blue-50 p-3 rounded-lg border border-blue-200">
                <p class="text-sm text-blue-800">✅ Pembayaran akan diproses dalam 1x24 jam setelah konfirmasi admin.</p>
              </div>
            </div>
          `,
          confirmButtonText: 'Oke, Mengerti',
          confirmButtonColor: '#10B981',
          width: '400px'
        });
        setShowUploadModal(false);
        setUploadFile(null);
        setSelectedBill(null);
        // Refresh data
        window.location.reload();
      } else {
        throw new Error(response.message || 'Upload gagal');
      }
    } catch (error: any) {
      console.error('Error uploading payment proof:', error);
      const errorMessage = error.message || error.response?.data?.message || 'Terjadi kesalahan saat mengupload bukti pembayaran. Silakan coba lagi.';
      await Swal.fire({
        icon: 'error',
        title: 'Upload Gagal',
        text: errorMessage,
        confirmButtonText: 'Coba Lagi',
        confirmButtonColor: '#EF4444'
      });
    } finally {
      setUploading(false);
    }
  };

  if (loading) {
    return <SkeletonPayments />;
  }

  return (
    <div className="space-y-4 md:space-y-6">
      <PageHeader 
        title="Pembayaran"
        subtitle="Kelola tagihan dan riwayat pembayaran Anda"
      />

      {/* Tabs */}
      <Card>
        <CardHeader>
          <nav className="-mb-px flex space-x-4 sm:space-x-8 overflow-x-auto" aria-label="Tabs">
            <button
              onClick={() => setActiveTab('unpaid')}
              className={`py-2 px-1 border-b-2 font-medium text-sm whitespace-nowrap ${
                activeTab === 'unpaid'
                  ? 'border-blue-500 text-blue-600'
                  : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300'
              }`}
            >
              Tagihan Belum Dibayar
            </button>
            <button
              onClick={() => setActiveTab('history')}
              className={`py-2 px-1 border-b-2 font-medium text-sm whitespace-nowrap ${
                activeTab === 'history'
                  ? 'border-blue-500 text-blue-600'
                  : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300'
              }`}
            >
              Riwayat Pembayaran
            </button>
          </nav>
        </CardHeader>

        <div className="p-6">
          {activeTab === 'unpaid' && (
            <div className="space-y-4">
              <h3 className="text-lg font-medium text-gray-900">Tagihan Belum Dibayar</h3>
              {unpaidBills.length > 0 ? (
                <div className="space-y-4">
                  {unpaidBills.map((bill) => (
                    <div key={bill.id} className="border border-gray-200 rounded-lg p-4">
                      <div className="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
                        <div className="flex-1">
                          <h4 className="text-sm font-medium text-gray-900">{bill.kode_pembayaran}</h4>
                          <p className="text-sm text-gray-500">{bill?.package_info?.nama_paket || bill?.paket?.nama_paket || '-'}</p>
                          <p className="text-sm text-gray-500">
                            Jatuh tempo: {bill?.due_date || bill?.tanggal_jatuh_tempo || '-'}
                          </p>
                        </div>
                        <div className="text-right sm:ml-4">
                          <p className="text-lg font-medium text-gray-900">
                            Rp {Number(bill?.package_info?.harga_paket ?? bill?.jumlah ?? 0).toLocaleString('id-ID')}
                          </p>
                          <div className="flex flex-col sm:flex-row gap-2 mt-2">
                            <ActionButton 
                              onClick={() => handleUploadClick(bill)}
                              size="sm"
                              className="w-full sm:w-auto"
                            >
                              Upload Bukti Bayar
                            </ActionButton>
                            <button
                              onClick={() => handleWhatsAppClick(bill)}
                              className="inline-flex items-center justify-center px-3 py-1.5 border border-transparent text-xs font-medium rounded-md text-white bg-green-600 hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500 transition-colors w-full sm:w-auto"
                            >
                              <svg className="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 24 24">
                                <path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893A11.821 11.821 0 0020.885 3.488"/>
                              </svg>
                              Kirim ke WA
                            </button>
                          </div>
                        </div>
                      </div>
                    </div>
                  ))}
                </div>
              ) : (
                <p className="text-gray-500">Tidak ada tagihan yang belum dibayar</p>
              )}
            </div>
          )}

          {activeTab === 'history' && (
            <div className="space-y-4">
              <h3 className="text-lg font-medium text-gray-900">Riwayat Pembayaran</h3>
              {paymentHistory.length > 0 ? (
                <div className="space-y-4">
                  {paymentHistory.map((payment) => (
                    <div key={payment.id} className="border border-gray-200 rounded-lg p-4">
                      <div className="flex items-center justify-between">
                        <div>
                          <h4 className="text-sm font-medium text-gray-900">{payment.kode_pembayaran}</h4>
                          <p className="text-sm text-gray-500">{payment?.package_info?.nama_paket || payment?.paket?.nama_paket || '-'}</p>
                          <p className="text-sm text-gray-500">
                            Bayar: {payment?.tanggal_bayar || payment?.created_at || '-'}
                          </p>
                        </div>
                        <div className="text-right">
                          <p className="text-lg font-medium text-gray-900">
                            Rp {Number(payment?.package_info?.harga_paket ?? payment?.jumlah ?? 0).toLocaleString('id-ID')}
                          </p>
                          <span className="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                            {payment.status}
                          </span>
                        </div>
                      </div>
                    </div>
                  ))}
                </div>
              ) : (
                <p className="text-gray-500">Tidak ada riwayat pembayaran</p>
              )}
            </div>
          )}
        </div>
      </Card>

      {/* Upload Modal */}
      {showUploadModal && (
        <div className="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50">
          <div className="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
            <div className="mt-3">
              <h3 className="text-lg font-medium text-gray-900 mb-4">
                Upload Bukti Pembayaran
              </h3>
              {selectedBill && (
                <div className="mb-4 p-3 bg-gray-50 rounded-md">
                  <p className="text-sm text-gray-600">
                    <strong>Kode Pembayaran:</strong> {selectedBill.kode_pembayaran}
                  </p>
                  <p className="text-sm text-gray-600">
                    <strong>Paket:</strong> {selectedBill?.package_info?.nama_paket || selectedBill?.paket?.nama_paket || '-'}
                  </p>
                  <p className="text-sm text-gray-600">
                    <strong>Jumlah:</strong> Rp {Number(selectedBill?.package_info?.harga_paket ?? selectedBill?.jumlah ?? 0).toLocaleString('id-ID')}
                  </p>
                </div>
              )}
              
              <div className="mb-4">
                <label className="block text-sm font-medium text-gray-700 mb-2">
                  Pilih File Bukti Pembayaran
                </label>
                <input
                  id="payment-proof-file"
                  name="payment-proof-file"
                  type="file"
                  accept="image/*,.pdf"
                  onChange={handleFileChange}
                  className="block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100"
                />
                {uploadFile && (
                  <p className="mt-2 text-sm text-green-600">
                    File dipilih: {uploadFile.name}
                  </p>
                )}
              </div>

              <div className="mb-4 p-3 bg-green-50 rounded-md border border-green-200">
                <div className="flex items-center">
                  <svg className="w-5 h-5 text-green-600 mr-2" fill="currentColor" viewBox="0 0 24 24">
                    <path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893A11.821 11.821 0 0020.885 3.488"/>
                  </svg>
                  <div>
                    <p className="text-sm font-medium text-green-800">Alternatif: Kirim ke WhatsApp</p>
                    <p className="text-xs text-green-600">Kirim bukti pembayaran langsung ke admin</p>
                  </div>
                </div>
                <button
                  onClick={() => {
                    if (selectedBill) {
                      handleWhatsAppClick(selectedBill);
                    }
                  }}
                  className="mt-2 w-full inline-flex items-center justify-center px-3 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-green-600 hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500"
                >
                  <svg className="w-4 h-4 mr-2" fill="currentColor" viewBox="0 0 24 24">
                    <path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893A11.821 11.821 0 0020.885 3.488"/>
                  </svg>
                  Kirim ke WhatsApp
                </button>
              </div>

              <div className="mb-4 p-3 bg-blue-50 rounded-md border border-blue-200">
                <div className="flex items-start">
                  <svg className="w-5 h-5 text-blue-600 mr-2 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                  </svg>
                  <div>
                    <p className="text-sm font-medium text-blue-800 mb-1">Instruksi Setelah Upload</p>
                    <div className="text-xs text-blue-700 space-y-1">
                      <p>1. Upload bukti pembayaran di atas</p>
                      <p>2. <strong>Kirim juga ke WhatsApp admin</strong> untuk validasi cepat</p>
                      <p>3. Admin akan konfirmasi dalam 1x24 jam</p>
                      <p>4. Status pembayaran akan otomatis terupdate</p>
                    </div>
                  </div>
                </div>
              </div>

              <div className="flex justify-end space-x-3">
                <button
                  onClick={() => {
                    setShowUploadModal(false);
                    setUploadFile(null);
                    setSelectedBill(null);
                  }}
                  className="px-4 py-2 border border-gray-300 rounded-md text-sm font-medium text-gray-700 hover:bg-gray-50"
                >
                  Batal
                </button>
                <button
                  onClick={handleUpload}
                  disabled={!uploadFile || uploading}
                  className="px-4 py-2 bg-blue-600 border border-transparent rounded-md text-sm font-medium text-white hover:bg-blue-700 disabled:opacity-50 disabled:cursor-not-allowed"
                >
                  {uploading ? 'Mengupload...' : 'Upload'}
                </button>
              </div>
            </div>
          </div>
        </div>
      )}
    </div>
  );
};

export default PaymentsPage;
