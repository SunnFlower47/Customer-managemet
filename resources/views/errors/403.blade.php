<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Akses Ditolak - {{ \App\Models\CompanyProfile::first()->nama_perusahaan ?? 'BCM' }}</title>

    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        primary: {
                            50: '#eff6ff',
                            100: '#dbeafe',
                            200: '#bfdbfe',
                            300: '#93c5fd',
                            400: '#60a5fa',
                            500: '#3b82f6',
                            600: '#2563eb',
                            700: '#1d4ed8',
                            800: '#1e40af',
                            900: '#1e3a8a',
                        }
                    }
                }
            }
        }
    </script>

    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body class="bg-gray-50">
    <div class="min-h-screen flex items-center justify-center px-4 sm:px-6 lg:px-8">
        <div class="max-w-md w-full space-y-8 text-center">
            <!-- Error Icon -->
            <div class="mx-auto h-24 w-24 flex items-center justify-center rounded-full bg-red-100">
                <svg class="h-12 w-12 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.732 16.5c-.77.833.192 2.5 1.732 2.5z"></path>
                </svg>
            </div>

            <!-- Error Content -->
            <div>
                <h1 class="text-6xl font-bold text-gray-900 mb-4">403</h1>
                <h2 class="text-2xl font-bold text-gray-900 mb-2">Akses Ditolak</h2>
                <p class="text-gray-600 mb-8">
                    Maaf, Anda tidak memiliki izin untuk mengakses halaman ini.
                    Silakan hubungi administrator jika Anda merasa ini adalah kesalahan.
                </p>

                <!-- User Info -->
                @auth
                <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-4 mb-6">
                    <div class="flex items-center justify-center space-x-3">
                        <div class="h-10 w-10 rounded-full bg-gradient-to-r from-primary-500 to-primary-600 flex items-center justify-center">
                            <span class="text-sm font-medium text-white">{{ substr(auth()->user()?->name ?? 'G', 0, 1) }}</span>
                        </div>
                        <div class="text-left">
                            <p class="text-sm font-medium text-gray-900">{{ auth()->user()?->name ?? 'Guest' }}</p>
                            <p class="text-xs text-gray-500">Role: {{ ucfirst(auth()->user()?->role ?? 'guest') }}</p>
                        </div>
                    </div>
                </div>
                @endauth

                <!-- Action Buttons -->
                <div class="space-y-3">
                    <a href="{{ route('dashboard') }}" class="w-full flex justify-center py-3 px-4 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-primary-600 hover:bg-primary-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500 transition-colors">
                        <i class="fas fa-home mr-2"></i>
                        Kembali ke Dashboard
                    </a>

                    <button onclick="history.back()" class="w-full flex justify-center py-3 px-4 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500 transition-colors">
                        <i class="fas fa-arrow-left mr-2"></i>
                        Kembali ke Halaman Sebelumnya
                    </button>

                    @auth
                    <form method="POST" action="{{ route('logout') }}" class="w-full">
                        @csrf
                        <button type="submit" class="w-full flex justify-center py-3 px-4 border border-red-300 rounded-md shadow-sm text-sm font-medium text-red-700 bg-white hover:bg-red-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500 transition-colors">
                            <i class="fas fa-sign-out-alt mr-2"></i>
                            Logout
                        </button>
                    </form>
                    @endauth
                </div>

                <!-- Help Text -->
                <div class="mt-8 text-sm text-gray-500">
                    <p>Jika Anda memerlukan akses ke halaman ini, silakan hubungi administrator sistem.</p>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
