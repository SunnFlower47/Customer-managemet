import React, { useState, useEffect, useRef } from 'react';
import { Link, useLocation, useNavigate } from 'react-router-dom';
import Swal from 'sweetalert2';
import { 
  HomeIcon, 
  CreditCardIcon, 
  TicketIcon, 
  UserIcon,
  ArrowRightOnRectangleIcon,
  BellIcon
} from '@heroicons/react/24/outline';
import { paymentService } from '../services/api';
import { useAuth } from '../contexts/AuthContext';

interface LayoutProps {
  children: React.ReactNode;
}

const Layout: React.FC<LayoutProps> = ({ children }) => {
  const location = useLocation();
  const navigate = useNavigate();
  const { customer, isAuthenticated, logout: authLogout } = useAuth();
  const [unpaidBills, setUnpaidBills] = useState<any[]>([]);
  const [showNotifications, setShowNotifications] = useState(false);
  const [loadingNotifications, setLoadingNotifications] = useState(false);
  const [showProfileMenu, setShowProfileMenu] = useState(false);
  const notificationRef = useRef<HTMLDivElement>(null);
  const profileRef = useRef<HTMLDivElement>(null);

  // Get user data from customer context or localStorage
  const userData = customer || JSON.parse(localStorage.getItem('customer') || '{}');

  // Fetch unpaid bills for notifications (with cache, this won't duplicate API calls)
  useEffect(() => {
    if (isAuthenticated) {
      const fetchUnpaidBills = async () => {
        try {
          setLoadingNotifications(true);
          // Use cache - if DashboardPage already fetched, this will use cached data
          const response = await paymentService.getUnpaidBills();
          if (response.success && response.data) {
            const bills = Array.isArray(response.data) ? response.data : [];
            setUnpaidBills(bills);
          }
        } catch (error) {
          console.error('Error fetching unpaid bills:', error);
          setUnpaidBills([]);
        } finally {
          setLoadingNotifications(false);
        }
      };

      fetchUnpaidBills();
      
      // Refresh every 5 minutes (cache will prevent unnecessary API calls)
      const interval = setInterval(() => {
        // Force refresh every 5 minutes to get latest data
        paymentService.getUnpaidBills(true).then(response => {
          if (response.success && response.data) {
            const bills = Array.isArray(response.data) ? response.data : [];
            setUnpaidBills(bills);
          }
        }).catch(error => {
          console.error('Error refreshing unpaid bills:', error);
        });
      }, 5 * 60 * 1000);
      return () => clearInterval(interval);
    }
  }, [isAuthenticated]);

  // Close notification dropdown when clicking outside
  useEffect(() => {
    const handleClickOutside = (event: MouseEvent) => {
      if (notificationRef.current && !notificationRef.current.contains(event.target as Node)) {
        setShowNotifications(false);
      }
      if (profileRef.current && !profileRef.current.contains(event.target as Node)) {
        setShowProfileMenu(false);
      }
    };

    document.addEventListener('mousedown', handleClickOutside);
    return () => document.removeEventListener('mousedown', handleClickOutside);
  }, []);

  const navigation = [
    { name: 'Dashboard', href: '/dashboard', icon: HomeIcon },
    { name: 'Pembayaran', href: '/payments', icon: CreditCardIcon },
    { name: 'Tickets', href: '/tickets', icon: TicketIcon },
    { name: 'Profil', href: '/profile', icon: UserIcon },
  ];

  const handleLogout = async () => {
    const result = await Swal.fire({
      title: 'Konfirmasi Logout',
      text: 'Apakah Anda yakin ingin keluar dari akun?',
      icon: 'question',
      showCancelButton: true,
      confirmButtonColor: '#EF4444',
      cancelButtonColor: '#6B7280',
      confirmButtonText: 'Ya, Logout',
      cancelButtonText: 'Batal'
    });

    if (!result.isConfirmed) return;

    try {
      await authLogout();
      navigate('/login');
      Swal.fire({
        icon: 'success',
        title: 'Logout Berhasil',
        text: 'Anda telah keluar dari akun.',
        showConfirmButton: false,
        timer: 1500,
      });
    } catch (error) {
      console.error('Logout error:', error);
      // Still navigate to login even if API fails
      navigate('/login');
    }
  };

  return (
    <div className="h-screen flex overflow-hidden bg-gray-100">

      {/* Desktop sidebar */}
      <div className="hidden md:flex md:flex-shrink-0">
        <div className="flex flex-col w-64">
          <div className="flex flex-col h-0 flex-1 border-r border-gray-200 bg-white">
            <div className="flex-1 flex flex-col pt-5 pb-4 overflow-y-auto">
              <div className="flex items-center flex-shrink-0 px-4">
                <h1 className="text-xl font-bold text-gray-900">Customer Portal</h1>
              </div>
              <nav className="mt-5 flex-1 px-2 bg-white space-y-1">
                {navigation.map((item) => {
                  const isActive = location.pathname === item.href;
                  return (
                    <Link
                      key={item.name}
                      to={item.href}
                      className={`${
                        isActive
                          ? 'bg-blue-100 text-blue-900'
                          : 'text-gray-600 hover:bg-gray-50 hover:text-gray-900'
                      } group flex items-center px-2 py-2 text-sm font-medium rounded-md`}
                    >
                      <item.icon className="mr-3 h-6 w-6" />
                      {item.name}
                    </Link>
                  );
                })}
              </nav>
            </div>
            <div className="flex-shrink-0 flex border-t border-gray-200 p-4">
              <div className="flex items-center w-full">
                <div className="flex-1">
                  <div className="text-sm font-medium text-gray-800">{userData?.nama}</div>
                  <div className="text-xs text-gray-500">{userData?.no_hp}</div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>

      {/* Main content */}
      <div className="flex flex-col w-0 flex-1 overflow-hidden">
        <div className="relative z-10 flex-shrink-0 flex h-16 bg-white shadow">
          <div className="flex-1 px-4 flex justify-between">
            <div className="flex-1 flex">
              <div className="w-full flex md:ml-0">
                <div className="flex items-center">
                  <h1 className="text-lg font-semibold text-gray-900 md:hidden">Customer Portal</h1>
                </div>
              </div>
            </div>
            <div className="ml-4 flex items-center md:ml-6">
              {/* Notification Bell */}
              <div className="relative" ref={notificationRef}>
                <button
                  type="button"
                  onClick={() => setShowNotifications(!showNotifications)}
                  className="relative bg-white p-1 rounded-full text-gray-400 hover:text-gray-500 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-colors"
                  title="Notifikasi Tagihan"
                  aria-label="Notifikasi Tagihan"
                >
                  <BellIcon className="h-6 w-6" />
                  {unpaidBills.length > 0 && (
                    <span className="absolute -top-1 -right-1 flex h-5 w-5 items-center justify-center rounded-full bg-red-500 text-[10px] font-bold text-white ring-2 ring-white">
                      {unpaidBills.length > 9 ? '9+' : unpaidBills.length}
                      <span className="sr-only">{unpaidBills.length} tagihan belum dibayar</span>
                    </span>
                  )}
                </button>

                {/* Notification Dropdown */}
                {showNotifications && (
                  <div className="absolute right-0 mt-2 w-[280px] sm:w-80 bg-white rounded-lg shadow-lg ring-1 ring-black ring-opacity-5 z-50" style={{ maxWidth: 'calc(100vw - 1rem)' }}>
                    <div className="p-3 border-b border-gray-200">
                      <div className="flex items-center justify-between">
                        <h3 className="text-xs sm:text-sm font-semibold text-gray-900">Notifikasi Tagihan</h3>
                        {unpaidBills.length > 0 && (
                          <span className="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] sm:text-xs font-medium bg-red-100 text-red-800">
                            {unpaidBills.length} tagihan
                          </span>
                        )}
                      </div>
                    </div>
                    
                    <div className="max-h-80 sm:max-h-96 overflow-y-auto">
                      {loadingNotifications ? (
                        <div className="p-3 sm:p-4 text-center">
                          <div className="animate-spin rounded-full h-5 w-5 sm:h-6 sm:w-6 border-b-2 border-blue-600 mx-auto"></div>
                          <p className="mt-2 text-xs sm:text-sm text-gray-500">Memuat...</p>
                        </div>
                      ) : unpaidBills.length > 0 ? (
                        <div className="divide-y divide-gray-200">
                          {unpaidBills.slice(0, 5).map((bill) => (
                            <Link
                              key={bill.id}
                              to="/payments"
                              onClick={() => setShowNotifications(false)}
                              className="block p-2.5 sm:p-3 hover:bg-gray-50 transition-colors"
                            >
                              <div className="flex items-start gap-2 sm:gap-3">
                                <div className="flex-shrink-0">
                                  <div className="h-7 w-7 sm:h-8 sm:w-8 bg-amber-100 rounded-full flex items-center justify-center">
                                    <CreditCardIcon className="h-4 w-4 sm:h-5 sm:w-5 text-amber-600" />
                                  </div>
                                </div>
                                <div className="flex-1 min-w-0">
                                  <p className="text-xs sm:text-sm font-medium text-gray-900 truncate">
                                    {bill.kode_pembayaran}
                                  </p>
                                  <p className="text-[10px] sm:text-xs text-gray-500 mt-0.5 truncate">
                                    {bill.package_info?.nama_paket || 'Paket Tidak Diketahui'}
                                  </p>
                                  <p className="text-[10px] sm:text-xs text-gray-500">
                                    Jatuh tempo: {bill.due_date || '-'}
                                  </p>
                                  <p className="text-xs sm:text-sm font-semibold text-gray-900 mt-0.5">
                                    Rp {Number(bill.package_info?.harga_paket || bill.jumlah || 0).toLocaleString('id-ID')}
                                  </p>
                                </div>
                              </div>
                            </Link>
                          ))}
                          {unpaidBills.length > 5 && (
                            <Link
                              to="/payments"
                              onClick={() => setShowNotifications(false)}
                              className="block p-2.5 sm:p-3 text-center text-xs sm:text-sm font-medium text-blue-600 hover:bg-gray-50 transition-colors"
                            >
                              Lihat semua {unpaidBills.length} tagihan
                            </Link>
                          )}
                        </div>
                      ) : (
                        <div className="p-6 sm:p-8 text-center">
                          <BellIcon className="mx-auto h-10 w-10 sm:h-12 sm:w-12 text-gray-400" />
                          <p className="mt-2 text-xs sm:text-sm text-gray-500">Tidak ada tagihan belum dibayar</p>
                        </div>
                      )}
                    </div>
                  </div>
                )}
              </div>

              {/* Profile Menu */}
              <div className="ml-3 relative" ref={profileRef}>
                <button
                  type="button"
                  onClick={() => setShowProfileMenu(!showProfileMenu)}
                  className="flex items-center space-x-2 bg-white p-1 rounded-full hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-colors"
                  title="Profile Menu"
                  aria-label="Profile Menu"
                >
                  <div className="h-8 w-8 rounded-full bg-blue-600 flex items-center justify-center">
                    <span className="text-sm font-medium text-white">
                      {userData?.nama?.charAt(0)?.toUpperCase() || 'U'}
                    </span>
                  </div>
                  <div className="hidden md:block text-left">
                    <div className="text-sm font-medium text-gray-700">{userData?.nama}</div>
                    <div className="text-xs text-gray-500">{userData?.no_hp}</div>
                  </div>
                </button>

                {/* Profile Dropdown */}
                {showProfileMenu && (
                  <div className="absolute right-0 mt-2 w-56 bg-white rounded-lg shadow-lg ring-1 ring-black ring-opacity-5 z-50">
                    <div className="py-1">
                      <div className="px-4 py-3 border-b border-gray-200">
                        <p className="text-sm font-medium text-gray-900">{userData?.nama}</p>
                        <p className="text-xs text-gray-500 truncate">{userData?.no_hp}</p>
                        <p className="text-xs text-gray-500 truncate">{userData?.pppoe}</p>
                      </div>
                      <Link
                        to="/profile"
                        onClick={() => setShowProfileMenu(false)}
                        className="flex items-center px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 transition-colors"
                      >
                        <UserIcon className="mr-3 h-5 w-5 text-gray-400" />
                        Profil Saya
                      </Link>
                      <button
                        onClick={() => {
                          setShowProfileMenu(false);
                          handleLogout();
                        }}
                        className="w-full flex items-center px-4 py-2 text-sm text-red-600 hover:bg-red-50 transition-colors"
                      >
                        <ArrowRightOnRectangleIcon className="mr-3 h-5 w-5" />
                        Logout
                      </button>
                    </div>
                  </div>
                )}
              </div>
            </div>
          </div>
        </div>

        <main className="flex-1 relative overflow-y-auto focus:outline-none">
          <div className="py-4 pb-20 md:py-6 md:pb-6">
            <div className="max-w-7xl mx-auto px-3 sm:px-4 md:px-8">
              {children}
            </div>
          </div>
        </main>
      </div>

      {/* Bottom Navigation - Mobile */}
      <div className="fixed bottom-0 left-0 right-0 bg-white border-t border-gray-200 px-2 py-1 md:hidden z-30 safe-area-pb">
        <div className="flex justify-around max-w-md mx-auto">
          {navigation.map((item) => {
            const isActive = location.pathname === item.href;
            return (
              <Link
                key={item.name}
                to={item.href}
                className={`flex flex-col items-center py-2 px-2 rounded-lg transition-colors min-w-0 flex-1 ${
                  isActive
                    ? 'text-blue-600 bg-blue-50'
                    : 'text-gray-500 hover:text-gray-700'
                }`}
              >
                <item.icon className="h-5 w-5 mb-1 flex-shrink-0" />
                <span className="text-xs font-medium truncate text-center leading-tight">{item.name}</span>
              </Link>
            );
          })}
        </div>
      </div>
    </div>
  );
};

export default Layout;
