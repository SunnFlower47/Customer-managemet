import React, { useState, useEffect } from 'react';
import { Link } from 'react-router-dom';
import { motion } from 'framer-motion';
import { SkeletonCard, SkeletonStats, SkeletonBills, SkeletonQuickActions, SkeletonPaymentMethods } from '../components/SkeletonLoader';
import { useAuth } from '../contexts/AuthContext';
import { paymentService, profileService, paymentMethodsService } from '../services/api';
import { Section, Grid, List, ListItem, Card, CardBody, Icon, ActionButton } from '../components/DesignSystem';

const DashboardPage: React.FC = () => {
  const { customer } = useAuth();
  const [statistics, setStatistics] = useState<any>(null);
  const [unpaidBills, setUnpaidBills] = useState<any[]>([]);
  const [paymentHistory, setPaymentHistory] = useState<any[]>([]);
  const [paymentMethods, setPaymentMethods] = useState<any>(null);
  const [loading, setLoading] = useState(true);

  useEffect(() => {
    const fetchData = async () => {
      try {
        setLoading(true);

        // Fetch all data in parallel (with cache, this is fast and prevents duplicate calls)
        const [statsResponse, billsResponse, historyResponse, methodsResponse] = await Promise.allSettled([
          profileService.getStatistics(),
          paymentService.getUnpaidBills(),
          paymentService.getPaymentHistory(1),
          paymentMethodsService.getPaymentMethods(),
        ]);

        // Handle statistics
        if (statsResponse.status === 'fulfilled' && statsResponse.value.success && statsResponse.value.data) {
          setStatistics(statsResponse.value.data);
        } else {
          setStatistics({
            total_payments: 0,
            paid_payments: 0,
            unpaid_payments: 0,
            total_tickets: 0
          });
        }

        // Handle unpaid bills
        if (billsResponse.status === 'fulfilled' && billsResponse.value.success && billsResponse.value.data) {
          const billsData = Array.isArray(billsResponse.value.data) 
            ? billsResponse.value.data 
            : [];
          setUnpaidBills(billsData);
        } else {
          setUnpaidBills([]);
        }

        // Handle payment history
        if (historyResponse.status === 'fulfilled' && historyResponse.value.success && historyResponse.value.data) {
          const responseData = historyResponse.value.data as any;
          const historyData = Array.isArray(responseData) 
            ? responseData 
            : (responseData?.data && Array.isArray(responseData.data)) 
              ? responseData.data 
              : [];
          setPaymentHistory(historyData.slice(0, 3)); // Show only latest 3 payments
        } else {
          setPaymentHistory([]);
        }

        // Handle payment methods
        if (methodsResponse.status === 'fulfilled' && methodsResponse.value.success && methodsResponse.value.data) {
          setPaymentMethods(methodsResponse.value.data);
        } else {
          setPaymentMethods(null);
        }
      } catch (error: any) {
        console.error('Error fetching dashboard data:', error);
      } finally {
        setLoading(false);
      }
    };

    if (customer) {
      fetchData();
    } else {
      setLoading(false);
    }
  }, [customer]);

  if (loading) {
    return (
      <div className="space-y-6">
        <SkeletonCard />
        <SkeletonStats />
        <SkeletonBills />
        <SkeletonQuickActions />
        <SkeletonPaymentMethods />
      </div>
    );
  }

  return (
    <div className="space-y-4 md:space-y-6">
        {/* Welcome Section */}
        <motion.div 
          initial={{ opacity: 0, y: 20 }}
          animate={{ opacity: 1, y: 0 }}
          transition={{ duration: 0.5 }}
        >
          <Card>
            <CardBody className="p-4 sm:p-6">
              <div className="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 sm:gap-4">
                <div className="flex-1 min-w-0">
                  <h1 className="text-lg sm:text-xl md:text-2xl font-bold text-gray-900 mb-1 sm:mb-2 truncate">
                    Halo, {customer?.nama || 'User'}!
                  </h1>
                  <p className="text-xs sm:text-sm text-gray-600 mb-1">
                    Selamat datang di Customer Portal <strong className="text-gray-900">ANDRI.NET</strong>
                  </p>
                  <p className="text-xs sm:text-sm text-gray-500 truncate">
                    {customer?.paket?.nama_paket || 'No Package'} • {customer?.pppoe || 'No PPPoE'}
                  </p>
                </div>
                <div className="flex-shrink-0">
                  <div className="h-10 w-10 sm:h-12 sm:w-12 bg-gradient-to-br from-blue-500 to-blue-600 rounded-xl flex items-center justify-center shadow-sm">
                    <svg className="h-5 w-5 sm:h-6 sm:w-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                    </svg>
                  </div>
                </div>
              </div>
            </CardBody>
          </Card>
          
          {customer?.is_default_password && (
            <Card className="mt-4">
              <CardBody>
                <div className="flex items-center">
                  <div className="h-6 w-6 bg-amber-100 rounded-full flex items-center justify-center mr-3">
                    <Icon size="sm" color="warning">
                      <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.732 16.5c-.77.833.192 2.5 1.732 2.5z" />
                      </svg>
                    </Icon>
                  </div>
                  <div>
                    <h3 className="text-xs font-medium text-amber-800">
                      Password Default
                    </h3>
                    <p className="text-xs text-amber-700 mt-0.5">
                      Ubah password untuk keamanan
                    </p>
                  </div>
                </div>
              </CardBody>
            </Card>
          )}
        </motion.div>

        {/* Statistics Cards */}
        {statistics && (
          <motion.div 
            initial={{ opacity: 0, y: 20 }}
            animate={{ opacity: 1, y: 0 }}
            transition={{ duration: 0.5, delay: 0.1 }}
          >
            <Grid cols={2} gap="sm">
              <Card>
                <CardBody>
                  <div className="flex items-center justify-between">
                    <div>
                      <p className="text-gray-500 text-xs font-medium">Total Tagihan</p>
                      <p className="text-gray-900 text-xl font-bold">{statistics?.total_payments || 0}</p>
                    </div>
                    <div className="h-8 w-8 bg-blue-50 rounded-lg flex items-center justify-center">
                      <Icon color="primary" size="sm">
                        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                          <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z" />
                        </svg>
                      </Icon>
                    </div>
                  </div>
                </CardBody>
              </Card>

              <Card>
                <CardBody>
                  <div className="flex items-center justify-between">
                    <div>
                      <p className="text-gray-500 text-xs font-medium">Sudah Lunas</p>
                      <p className="text-gray-900 text-xl font-bold">{statistics?.paid_payments || 0}</p>
                    </div>
                    <div className="h-8 w-8 bg-green-50 rounded-lg flex items-center justify-center">
                      <Icon color="success" size="sm">
                        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                          <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                      </Icon>
                    </div>
                  </div>
                </CardBody>
              </Card>

              <Card>
                <CardBody>
                  <div className="flex items-center justify-between">
                    <div>
                      <p className="text-gray-500 text-xs font-medium">Belum Bayar</p>
                      <p className="text-gray-900 text-xl font-bold">{statistics?.unpaid_payments || 0}</p>
                    </div>
                    <div className="h-8 w-8 bg-amber-50 rounded-lg flex items-center justify-center">
                      <Icon color="warning" size="sm">
                        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                          <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                      </Icon>
                    </div>
                  </div>
                </CardBody>
              </Card>

              <Card>
                <CardBody>
                  <div className="flex items-center justify-between">
                    <div>
                      <p className="text-gray-500 text-xs font-medium">Support</p>
                      <p className="text-gray-900 text-xl font-bold">{statistics?.total_tickets || 0}</p>
                    </div>
                    <div className="h-8 w-8 bg-gray-50 rounded-lg flex items-center justify-center">
                      <Icon color="neutral" size="sm">
                        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                          <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M15 5v2m0 4v2m0 4v2M5 5a2 2 0 00-2 2v3a2 2 0 110 4v3a2 2 0 002 2h14a2 2 0 002-2v-3a2 2 0 110-4V7a2 2 0 00-2-2H5z" />
                        </svg>
                      </Icon>
                    </div>
                  </div>
                </CardBody>
              </Card>
            </Grid>
          </motion.div>
        )}

        {/* Recent Payment History */}
        {paymentHistory.length > 0 && (
          <motion.div
            initial={{ opacity: 0, y: 20 }}
            animate={{ opacity: 1, y: 0 }}
            transition={{ duration: 0.5, delay: 0.3 }}
          >
            <Section
              title="Pembayaran Terbaru"
              subtitle={`${paymentHistory.length} pembayaran terbaru`}
              variant="success"
            >
              <List>
                {paymentHistory.map((payment, index) => (
                  <motion.div
                    key={payment.id}
                    initial={{ opacity: 0, x: -20 }}
                    animate={{ opacity: 1, x: 0 }}
                    transition={{ duration: 0.3, delay: 0.4 + (index * 0.1) }}
                  >
                    <ListItem>
                      <CardBody>
                        <div className="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
                          <div className="flex-1">
                            <div className="flex items-center mb-2">
                              <div className="h-8 w-8 bg-green-50 rounded-lg flex items-center justify-center mr-3 flex-shrink-0">
                                <Icon color="success" size="sm">
                                  <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                  </svg>
                                </Icon>
                              </div>
                              <div className="min-w-0 flex-1">
                                <div className="text-sm font-medium text-gray-900 truncate">
                                  {payment.kode_pembayaran}
                                </div>
                                <div className="text-xs text-gray-500 truncate">
                                  {payment.package_info?.nama_paket || payment.paket?.nama_paket || '-'}
                                </div>
                              </div>
                            </div>
                            <div className="text-sm text-gray-600">
                              Dibayar: {payment.tanggal_bayar || payment.created_at || '-'}
                            </div>
                          </div>
                          <div className="text-right sm:ml-4">
                            <div className="text-lg font-bold text-green-600 mb-2">
                              Rp {Number(payment.package_info?.harga_paket || payment.jumlah || payment.harga_paket || 0).toLocaleString('id-ID')}
                            </div>
                            <span className="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                              {payment.status === 'lunas' ? 'Lunas' : payment.status}
                            </span>
                          </div>
                        </div>
                      </CardBody>
                    </ListItem>
                  </motion.div>
                ))}
              </List>
            </Section>
          </motion.div>
        )}

        {/* Unpaid Bills */}
        {unpaidBills.length > 0 && (
          <motion.div 
            initial={{ opacity: 0, y: 20 }}
            animate={{ opacity: 1, y: 0 }}
            transition={{ duration: 0.5, delay: 0.2 }}
          >
            <Section 
              title="Tagihan Belum Dibayar"
              subtitle={`${unpaidBills.length} tagihan perlu dibayar`}
              variant="warning"
            >
              <List>
                {unpaidBills.map((bill, index) => (
                  <motion.div 
                    key={bill.id}
                    initial={{ opacity: 0, x: -20 }}
                    animate={{ opacity: 1, x: 0 }}
                    transition={{ duration: 0.3, delay: 0.3 + (index * 0.1) }}
                  >
                    <ListItem>
                      <CardBody>
                        <div className="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
                          <div className="flex-1">
                            <div className="flex items-center mb-2">
                              <div className="h-8 w-8 bg-amber-50 rounded-lg flex items-center justify-center mr-3 flex-shrink-0">
                                <Icon color="warning" size="sm">
                                  <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z" />
                                  </svg>
                                </Icon>
                              </div>
                              <div className="min-w-0 flex-1">
                                <div className="text-sm font-medium text-gray-900 truncate">
                                  {bill.kode_pembayaran}
                                </div>
                                <div className="text-xs text-gray-500 truncate">
                                  {bill.package_info?.nama_paket || bill.paket?.nama_paket || '-'}
                                </div>
                              </div>
                            </div>
                            <div className="text-sm text-gray-600">
                              Jatuh tempo: {bill.due_date || bill.tanggal_jatuh_tempo || '-'}
                            </div>
                          </div>
                          <div className="text-right sm:ml-4">
                            <div className="text-lg font-bold text-gray-900 mb-2">
                              Rp {Number(bill.package_info?.harga_paket || bill.jumlah || bill.harga_paket || 0).toLocaleString('id-ID')}
                            </div>
                            <ActionButton size="sm" onClick={() => window.location.href = '/payments'} className="w-full sm:w-auto">
                              Bayar
                            </ActionButton>
                          </div>
                        </div>
                      </CardBody>
                    </ListItem>
                  </motion.div>
                ))}
              </List>
            </Section>
          </motion.div>
        )}

        {/* Quick Actions */}
        <motion.div 
          initial={{ opacity: 0, y: 20 }}
          animate={{ opacity: 1, y: 0 }}
          transition={{ duration: 0.5, delay: 0.3 }}
        >
          <Section title="Aksi Cepat" variant="primary">
            <div className="grid grid-cols-1 sm:grid-cols-2 gap-3">
              <Link
                to="/payments"
                className="flex items-center p-4 bg-gray-50 rounded-xl hover:bg-gray-100 transition-colors"
              >
                <div className="h-10 w-10 bg-blue-50 rounded-lg flex items-center justify-center mr-4">
                  <Icon color="primary">
                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z" />
                    </svg>
                  </Icon>
                </div>
                <div className="flex-1">
                  <p className="text-sm font-medium text-gray-900">Lihat Pembayaran</p>
                  <p className="text-xs text-gray-500">Cek tagihan dan riwayat pembayaran</p>
                </div>
                <Icon color="neutral" size="sm">
                  <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M9 5l7 7-7 7" />
                  </svg>
                </Icon>
              </Link>

              <Link
                to="/tickets"
                className="flex items-center p-4 bg-gray-50 rounded-xl hover:bg-gray-100 transition-colors"
              >
                <div className="h-10 w-10 bg-gray-50 rounded-lg flex items-center justify-center mr-4">
                  <Icon color="neutral">
                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M15 5v2m0 4v2m0 4v2M5 5a2 2 0 00-2 2v3a2 2 0 110 4v3a2 2 0 002 2h14a2 2 0 002-2v-3a2 2 0 110-4V7a2 2 0 00-2-2H5z" />
                    </svg>
                  </Icon>
                </div>
                <div className="flex-1">
                  <p className="text-sm font-medium text-gray-900">Buat Ticket</p>
                  <p className="text-xs text-gray-500">Laporkan masalah atau minta bantuan</p>
                </div>
                <Icon color="neutral" size="sm">
                  <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M9 5l7 7-7 7" />
                  </svg>
                </Icon>
              </Link>
            </div>
          </Section>
        </motion.div>

        {/* Metode Pembayaran */}
        <motion.div 
          initial={{ opacity: 0, y: 20 }}
          animate={{ opacity: 1, y: 0 }}
          transition={{ duration: 0.5, delay: 0.4 }}
        >
          <Section 
            title="Metode Pembayaran"
            subtitle="Pilih metode pembayaran yang tersedia"
            variant="success"
          >
                   <Grid cols={2} gap="sm">
                     {/* DANA */}
                     {paymentMethods?.dana_phone && (
                       <div className="bg-gray-50 rounded-xl p-4 hover:bg-gray-100 transition-colors cursor-pointer">
                         <div className="flex flex-col items-center text-center">
                           <div className="h-10 w-10 bg-blue-50 rounded-lg flex items-center justify-center mb-2">
                             <svg viewBox="0 0 200 60" className="w-8 h-6">
                               <defs>
                                 <linearGradient id="dana-gradient-dashboard" x1="0%" y1="0%" x2="100%" y2="100%">
                                   <stop offset="0%" stopColor="#118EEA" />
                                   <stop offset="100%" stopColor="#0D6EFD" />
                                 </linearGradient>
                               </defs>
                               <circle cx="30" cy="30" r="25" fill="url(#dana-gradient-dashboard)"/>
                               <text x="30" y="35" textAnchor="middle" fill="white" fontSize="16" fontWeight="bold">D</text>
                               <text x="70" y="35" fill="url(#dana-gradient-dashboard)" fontSize="18" fontWeight="bold">DANA</text>
                             </svg>
                           </div>
                           <h4 className="text-xs font-medium text-gray-900 mb-1">DANA</h4>
                           <p className="text-xs text-gray-500">{paymentMethods.dana_phone}</p>
                         </div>
                       </div>
                     )}

                     {/* Bank Mandiri */}
                     {paymentMethods?.mandiri_account && (
                       <div className="bg-gray-50 rounded-xl p-4 hover:bg-gray-100 transition-colors cursor-pointer">
                         <div className="flex flex-col items-center text-center">
                           <div className="h-10 w-10 bg-gray-50 rounded-lg flex items-center justify-center mb-2">
                             <svg viewBox="0 0 200 60" className="w-8 h-6">
                               <defs>
                                 <linearGradient id="mandiri-gradient-dashboard" x1="0%" y1="0%" x2="100%" y2="100%">
                                   <stop offset="0%" stopColor="#E53E3E" />
                                   <stop offset="100%" stopColor="#C53030" />
                                 </linearGradient>
                               </defs>
                               <rect x="5" y="10" width="40" height="40" rx="8" fill="url(#mandiri-gradient-dashboard)"/>
                               <text x="25" y="35" textAnchor="middle" fill="white" fontSize="12" fontWeight="bold">M</text>
                               <text x="70" y="35" fill="url(#mandiri-gradient-dashboard)" fontSize="16" fontWeight="bold">MANDIRI</text>
                             </svg>
                           </div>
                           <h4 className="text-xs font-medium text-gray-900 mb-1">Mandiri</h4>
                           <p className="text-xs text-gray-500">
                             {paymentMethods.mandiri_account}
                             {paymentMethods.mandiri_account_name && ` (${paymentMethods.mandiri_account_name})`}
                           </p>
                         </div>
                       </div>
                     )}
                   </Grid>
            
            {/* Info Card */}
            <Card className="mt-6">
              <CardBody>
                <div className="flex items-start">
                  <div className="flex-shrink-0">
                    <div className="h-8 w-8 bg-gray-100 rounded-lg flex items-center justify-center">
                      <Icon color="neutral" size="sm">
                        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                          <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                      </Icon>
                    </div>
                  </div>
                  <div className="ml-3">
                    <h3 className="text-sm font-medium text-gray-900 mb-2">
                      Informasi Pembayaran
                    </h3>
                    <div className="space-y-1 text-xs text-gray-600">
                      <div className="flex items-center">
                        <div className="h-1.5 w-1.5 bg-gray-400 rounded-full mr-2"></div>
                        <span>Transfer sesuai nominal tagihan</span>
                      </div>
                      <div className="flex items-center">
                        <div className="h-1.5 w-1.5 bg-gray-400 rounded-full mr-2"></div>
                        <span>Upload bukti pembayaran setelah transfer</span>
                      </div>
                      <div className="flex items-center">
                        <div className="h-1.5 w-1.5 bg-gray-400 rounded-full mr-2"></div>
                        <span>Diproses dalam 1x24 jam</span>
                      </div>
                    </div>
                  </div>
                </div>
              </CardBody>
            </Card>
          </Section>
        </motion.div>
    </div>
  );
};

export default DashboardPage;







