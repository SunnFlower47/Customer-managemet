import React from 'react';
import { Link } from 'react-router-dom';

const NotFoundPage: React.FC = () => {
  return (
    <div className="min-h-screen flex items-center justify-center bg-gray-50 p-6">
      <div className="max-w-md w-full bg-white shadow rounded-lg p-8 border border-gray-200 text-center">
        <h1 className="text-2xl font-semibold text-gray-900 mb-2">404 - Halaman Tidak Ditemukan</h1>
        <p className="text-sm text-gray-600 mb-6">Halaman yang Anda cari tidak tersedia atau telah dipindahkan.</p>
        <div className="flex items-center justify-center gap-3">
          <Link
            to="/dashboard"
            className="inline-flex items-center px-4 py-2 bg-blue-600 text-white text-sm font-medium rounded-md hover:bg-blue-700"
          >
            Ke Dashboard
          </Link>
          <Link
            to="/login"
            className="inline-flex items-center px-4 py-2 border border-gray-300 text-gray-700 text-sm font-medium rounded-md hover:bg-gray-50"
          >
            Login
          </Link>
        </div>
      </div>
    </div>
  );
};

export default NotFoundPage;




