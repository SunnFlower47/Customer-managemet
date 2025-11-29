import React, { useState, useEffect, useRef } from 'react';
import Swal from 'sweetalert2';
import toast from 'react-hot-toast';
import { SkeletonProfile } from '../components/SkeletonLoader';
import { useAuth } from '../contexts/AuthContext';
import { profileService } from '../services/api';

interface Profile {
  nama: string;
  no_hp: string;
  pppoe: string;
  alamat: string;
}


const ProfilePage: React.FC = () => {
  const { customer, updateCustomer, refreshCustomer } = useAuth();
  const passwordFormRef = useRef<HTMLFormElement>(null);

  const [profile, setProfile] = useState<Profile>({
    nama: customer?.nama || '',
    no_hp: customer?.no_hp || '',
    pppoe: customer?.pppoe || '',
    alamat: customer?.alamat || '',
  });

  const [isEditing, setIsEditing] = useState(false);
  const [loading, setLoading] = useState(true);

  // Fetch profile
  useEffect(() => {
    const fetchProfile = async () => {
      try {
        setLoading(true);
        const response = await profileService.getProfile();
        
        if (response.success && response.data) {
          setProfile({
            nama: response.data.nama || '',
            no_hp: response.data.no_hp || '',
            pppoe: response.data.pppoe || '',
            alamat: response.data.alamat || '',
          });
        } else {
          throw new Error(response.message || 'Gagal memuat profil');
        }
      } catch (err: any) {
        console.error(err);
        toast.error(err.message || 'Terjadi kesalahan saat memuat profil');
        if (err.response?.status === 401) {
          localStorage.removeItem('token');
          localStorage.removeItem('customer');
          window.location.href = '/login';
        }
      } finally {
        setLoading(false);
      }
    };

    if (customer) {
      // Initialize from customer context
      setProfile({
        nama: customer.nama || '',
        no_hp: customer.no_hp || '',
        pppoe: customer.pppoe || '',
        alamat: customer.alamat || '',
      });
      // Then fetch latest data
      fetchProfile();
    } else {
      setLoading(false);
    }
  }, [customer]);

  // Save profile
  const handleSave = async () => {
    try {
      const response = await profileService.updateProfile(profile);

      if (response.success && response.data) {
        // Update customer in context
        updateCustomer(response.data);
        
        // Update local state
        setProfile({
          nama: response.data.nama || profile.nama,
          no_hp: response.data.no_hp || profile.no_hp,
          pppoe: response.data.pppoe || profile.pppoe,
          alamat: response.data.alamat || profile.alamat,
        });

        toast.success('Profil berhasil diperbarui');
        setIsEditing(false);
      } else {
        throw new Error(response.message || 'Gagal update profil');
      }
    } catch (err: any) {
      console.error('Profile update error:', err);
      const errorMessage = err.response?.data?.message || err.message || 'Terjadi kesalahan saat update profil.';
      
      // Only logout if it's a 401/403 error
      if (err.response?.status === 401 || err.response?.status === 403) {
        Swal.fire({
          icon: 'error',
          title: 'Sesi Berakhir',
          text: 'Sesi Anda telah berakhir. Silakan login kembali.',
          confirmButtonColor: '#EF4444',
        }).then(() => {
          localStorage.removeItem('token');
          localStorage.removeItem('customer');
          window.location.href = '/login';
        });
      } else {
        Swal.fire({
          icon: 'error',
          title: 'Oops...',
          text: errorMessage,
          confirmButtonColor: '#EF4444',
        });
      }
    }
  };

  // Change password
  const handleChangePassword = async (e: React.FormEvent<HTMLFormElement>) => {
    e.preventDefault();
    const formData = new FormData(e.currentTarget);
    const oldPassword = formData.get('old-password') as string;
    const newPassword = formData.get('new-password') as string;
    const confirmPassword = formData.get('confirm-password') as string;

    if (newPassword !== confirmPassword) {
      toast.error('Password baru dan konfirmasi tidak cocok');
      return;
    }

    if (newPassword.length < 6) {
      toast.error('Password minimal 6 karakter');
      return;
    }

    try {
      const response = await profileService.changePassword({
        current_password: oldPassword,
        new_password: newPassword,
        new_password_confirmation: confirmPassword,
      });

      if (response.success) {
        toast.success('Password berhasil diperbarui');
        
        // Reset form safely
        if (passwordFormRef.current) {
          passwordFormRef.current.reset();
        }
      } else {
        throw new Error(response.message || 'Gagal mengubah password');
      }
    } catch (err: any) {
      const errorMessage = err.response?.data?.message || err.message || 'Terjadi kesalahan saat ubah password';
      toast.error(errorMessage);
      
      if (err.response?.status === 401) {
        localStorage.removeItem('token');
        localStorage.removeItem('customer');
        window.location.href = '/login';
      }
    }
  };

  if (loading) return <SkeletonProfile />;

  return (
    <div className="space-y-4 md:space-y-6">
      {/* Header */}
      <div className="bg-white shadow">
        <div className="px-4 py-5 sm:px-6 flex flex-col sm:flex-row sm:justify-between sm:items-center gap-4">
          <div>
            <h1 className="text-xl sm:text-2xl font-bold text-gray-900">Profil Saya</h1>
            <p className="mt-1 text-sm text-gray-500">Kelola informasi profil Anda</p>
          </div>
          <button
            onClick={() => setIsEditing(!isEditing)}
            className="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-md text-sm font-medium w-full sm:w-auto"
          >
            {isEditing ? 'Batal' : 'Edit Profil'}
          </button>
        </div>
      </div>

      {/* Informasi Profil */}
      <div className="bg-white shadow overflow-hidden sm:rounded-lg">
        <div className="px-4 py-5 sm:px-6">
          <h3 className="text-lg leading-6 font-medium text-gray-900">Informasi Profil</h3>
          <p className="mt-1 max-w-2xl text-sm text-gray-500">Detail informasi akun Anda</p>
        </div>
        <div className="border-t border-gray-200">
          <dl>
            {/* Nama */}
            <div className="bg-gray-50 px-4 py-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
              <dt className="text-sm font-medium text-gray-500">Nama Lengkap</dt>
              <dd className="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2">
                {isEditing ? (
                  <input
                    type="text"
                    className="block w-full border border-gray-300 rounded-md px-3 py-2"
                    value={profile.nama}
                    onChange={(e) => setProfile({ ...profile, nama: e.target.value })}
                  />
                ) : (
                  profile.nama
                )}
              </dd>
            </div>
            {/* Nomor HP */}
            <div className="bg-white px-4 py-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
              <dt className="text-sm font-medium text-gray-500">Nomor HP</dt>
              <dd className="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2">
                {isEditing ? (
                  <input
                    type="text"
                    className="block w-full border border-gray-300 rounded-md px-3 py-2"
                    value={profile.no_hp}
                    onChange={(e) => setProfile({ ...profile, no_hp: e.target.value })}
                  />
                ) : (
                  profile.no_hp
                )}
              </dd>
            </div>
            {/* PPPoE */}
            <div className="bg-gray-50 px-4 py-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
              <dt className="text-sm font-medium text-gray-500">PPPoE Username</dt>
              <dd className="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2">{profile.pppoe}</dd>
            </div>
            {/* Alamat */}
            <div className="bg-white px-4 py-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
              <dt className="text-sm font-medium text-gray-500">Alamat</dt>
              <dd className="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2">
                {isEditing ? (
                  <textarea
                    rows={3}
                    className="block w-full border border-gray-300 rounded-md px-3 py-2"
                    value={profile.alamat}
                    onChange={(e) => setProfile({ ...profile, alamat: e.target.value })}
                  />
                ) : (
                  profile.alamat
                )}
              </dd>
            </div>
          </dl>
        </div>
      </div>

      {/* Tombol Simpan */}
      {isEditing && (
        <div className="flex justify-end">
          <button
            onClick={handleSave}
            className="bg-green-600 hover:bg-green-700 text-white px-6 py-2 rounded-md text-sm font-medium"
          >
            Simpan Perubahan
          </button>
        </div>
      )}

      {/* Ubah Password */}
      <div className="bg-white shadow overflow-hidden sm:rounded-lg">
        <div className="px-4 py-5 sm:px-6">
          <h3 className="text-lg leading-6 font-medium text-gray-900">Ubah Password</h3>
          <p className="mt-1 max-w-2xl text-sm text-gray-500">Ubah password untuk keamanan akun Anda</p>
        </div>
        <div className="border-t border-gray-200 px-4 py-5 sm:px-6">
          <form ref={passwordFormRef} className="space-y-4" onSubmit={handleChangePassword}>
            <div>
              <label className="block text-sm font-medium text-gray-700">Password Lama</label>
              <input
                name="old-password"
                type="password"
                className="mt-1 block w-full border border-gray-300 rounded-md px-3 py-2"
                placeholder="Masukkan password lama"
              />
            </div>
            <div>
              <label className="block text-sm font-medium text-gray-700">Password Baru</label>
              <input
                name="new-password"
                type="password"
                className="mt-1 block w-full border border-gray-300 rounded-md px-3 py-2"
                placeholder="Masukkan password baru"
              />
            </div>
            <div>
              <label className="block text-sm font-medium text-gray-700">Konfirmasi Password Baru</label>
              <input
                name="confirm-password"
                type="password"
                className="mt-1 block w-full border border-gray-300 rounded-md px-3 py-2"
                placeholder="Konfirmasi password baru"
              />
            </div>
            <button
              type="submit"
              className="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-md text-sm font-medium"
            >
              Ubah Password
            </button>
          </form>
        </div>
      </div>
    </div>
  );
};

export default ProfilePage;
