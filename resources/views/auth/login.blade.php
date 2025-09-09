<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <!-- PWA Meta Tags -->
    <meta name="theme-color" content="#2563eb">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="default">
    <meta name="apple-mobile-web-app-title" content="WiFi Customer">

    <title>Login - WiFi Customer Management</title>

    <!-- Favicon -->
    @php
        $companyProfile = \App\Models\CompanyProfile::first();
        $faviconUrl = $companyProfile && $companyProfile->logo_url ? $companyProfile->logo_url : asset('icon-192x192.png');
    @endphp
    <link rel="icon" type="image/png" sizes="32x32" href="{{ $faviconUrl }}">
    <link rel="icon" type="image/png" sizes="16x16" href="{{ $faviconUrl }}">
    <link rel="apple-touch-icon" href="{{ $faviconUrl }}">
    <link rel="shortcut icon" href="{{ $faviconUrl }}">
    <link rel="manifest" href="{{ asset('manifest.json') }}">

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>

    <style>
        [x-cloak] { display: none !important; }

        /* Custom animations */
        @keyframes float {
            0%, 100% { transform: translateY(0px); }
            50% { transform: translateY(-10px); }
        }

        @keyframes glow {
            0%, 100% { box-shadow: 0 0 20px rgba(59, 130, 246, 0.3); }
            50% { box-shadow: 0 0 30px rgba(59, 130, 246, 0.6); }
        }

        .floating {
            animation: float 6s ease-in-out infinite;
        }

        .glowing {
            animation: glow 3s ease-in-out infinite;
        }

        /* Glass morphism effect */
        .glass {
            background: rgba(255, 255, 255, 0.25);
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.18);
        }

        /* Custom scrollbar */
        ::-webkit-scrollbar {
            width: 8px;
        }

        ::-webkit-scrollbar-track {
            background: #f1f1f1;
            border-radius: 10px;
        }

        ::-webkit-scrollbar-thumb {
            background: linear-gradient(45deg, #3b82f6, #6366f1);
            border-radius: 10px;
        }

        ::-webkit-scrollbar-thumb:hover {
            background: linear-gradient(45deg, #2563eb, #4f46e5);
        }
    </style>
</head>
<body class="font-sans antialiased bg-gray-50">
    <div class="min-h-screen flex items-center justify-center bg-gradient-to-br from-blue-50 via-indigo-50 to-purple-50 py-12 px-4 sm:px-6 lg:px-8 relative overflow-hidden">
        <!-- Background Pattern -->
        <div class="absolute inset-0 bg-[url('data:image/svg+xml,%3Csvg width="60" height="60" viewBox="0 0 60 60" xmlns="http://www.w3.org/2000/svg"%3E%3Cg fill="none" fill-rule="evenodd"%3E%3Cg fill="%239C92AC" fill-opacity="0.05"%3E%3Ccircle cx="30" cy="30" r="2"/%3E%3C/g%3E%3C/g%3E%3C/svg%3E')] opacity-40"></div>

        <!-- Floating Elements -->
        <div class="absolute top-20 left-20 w-20 h-20 bg-blue-200/30 rounded-full blur-xl animate-pulse"></div>
        <div class="absolute top-40 right-32 w-32 h-32 bg-indigo-200/30 rounded-full blur-xl animate-pulse delay-1000"></div>
        <div class="absolute bottom-32 left-32 w-24 h-24 bg-purple-200/30 rounded-full blur-xl animate-pulse delay-2000"></div>
        <div class="absolute bottom-20 right-20 w-28 h-28 bg-blue-300/30 rounded-full blur-xl animate-pulse delay-500"></div>

        <div class="max-w-md w-full space-y-8 relative z-10">
            <div class="text-center">
                @php
                    $companyProfile = \App\Models\CompanyProfile::first();
                @endphp

                <!-- Company Logo with Animation -->
                <div class="mx-auto h-32 w-32 flex items-center justify-center rounded-full bg-gradient-to-br from-white to-blue-50 shadow-2xl border-4 border-blue-200 transform hover:scale-105 transition-all duration-300 hover:shadow-3xl floating glowing">
                    @if($companyProfile && $companyProfile->logo_path)
                        <img src="{{ $companyProfile->logo_url }}"
                             alt="{{ $companyProfile->nama_perusahaan }}"
                             class="h-20 w-20 object-contain rounded-full">
                    @else
                        <div class="relative flex flex-col items-center justify-center">
                            <!-- BCM Text Logo -->
                            <div class="text-3xl font-bold bg-gradient-to-r from-blue-600 to-indigo-600 bg-clip-text text-transparent mb-1">
                                BCM
                            </div>
                            <!-- WiFi Icon -->
                            <svg class="h-6 w-6 text-blue-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.111 16.404a5.5 5.5 0 017.778 0M12 20h.01m-7.08-7.071c3.904-3.905 10.236-3.905 14.141 0M1.394 9.393c5.857-5.857 15.355-5.857 21.213 0" />
                            </svg>
                            <!-- WiFi Signal Animation -->
                            <div class="absolute -top-2 -right-2 w-4 h-4 bg-green-400 rounded-full animate-pulse shadow-lg"></div>
                        </div>
                    @endif
                </div>

                <!-- Company Name with Gradient -->
                <h2 class="mt-8 text-center text-4xl font-bold bg-gradient-to-r from-gray-800 to-gray-600 bg-clip-text text-transparent">
                    {{ $companyProfile->nama_perusahaan ?? 'BCM' }}
                </h2>

                <!-- System Name with Badge -->
                <div class="mt-4 inline-flex items-center px-6 py-3 rounded-full bg-gradient-to-r from-blue-500 to-indigo-600 text-white text-base font-semibold shadow-lg">
                    <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                    WiFi Customer Management
                </div>

                <p class="mt-5 text-center text-base text-gray-600 font-medium">
                    Masuk ke dashboard sistem
                </p>
            </div>

            <div class="bg-white/80 backdrop-blur-sm py-10 px-8 shadow-2xl rounded-2xl border border-white/20 transform hover:scale-[1.02] transition-all duration-300">
                <form class="space-y-6" method="POST" action="{{ route('login') }}">
                    @csrf

                    @if ($errors && $errors->any())
                        <div class="bg-red-50 border-l-4 border-red-400 p-4 rounded-lg shadow-sm" role="alert">
                            <div class="flex">
                                <div class="flex-shrink-0">
                                    <svg class="h-5 w-5 text-red-400" viewBox="0 0 20 20" fill="currentColor">
                                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd" />
                                    </svg>
                                </div>
                                <div class="ml-3">
                                    <ul class="text-sm text-red-700">
                                        @foreach ($errors->all() as $error)
                                            <li>{{ $error }}</li>
                                        @endforeach
                                    </ul>
                                </div>
                            </div>
                        </div>
                    @endif

                    <div class="space-y-5">
                        <div class="group">
                            <label for="email" class="block text-sm font-semibold text-gray-700 mb-2 group-focus-within:text-blue-600 transition-colors">Email Address</label>
                            <div class="relative">
                                <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                                    <svg class="h-5 w-5 text-gray-400 group-focus-within:text-blue-500 transition-colors" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 12a4 4 0 10-8 0 4 4 0 008 0zm0 0v1.5a2.5 2.5 0 005 0V12a9 9 0 10-9 9m4.5-1.206a8.959 8.959 0 01-4.5 1.207" />
                                    </svg>
                                </div>
                                <input id="email" name="email" type="email" autocomplete="email" required
                                       class="block w-full pl-12 pr-4 py-4 border-2 border-gray-200 rounded-xl focus:outline-none focus:ring-4 focus:ring-blue-500/20 focus:border-blue-500 transition-all duration-200 bg-gray-50/50 focus:bg-white"
                                       placeholder="Masukkan email Anda" value="{{ old('email') }}">
                            </div>
                        </div>

                        <div class="group">
                            <label for="password" class="block text-sm font-semibold text-gray-700 mb-2 group-focus-within:text-blue-600 transition-colors">Password</label>
                            <div class="relative">
                                <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                                    <svg class="h-5 w-5 text-gray-400 group-focus-within:text-blue-500 transition-colors" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                                    </svg>
                                </div>
                                <input id="password" name="password" type="password" autocomplete="current-password" required
                                       class="block w-full pl-12 pr-4 py-4 border-2 border-gray-200 rounded-xl focus:outline-none focus:ring-4 focus:ring-blue-500/20 focus:border-blue-500 transition-all duration-200 bg-gray-50/50 focus:bg-white"
                                       placeholder="Masukkan password Anda">
                            </div>
                        </div>
                    </div>

                    <div class="flex items-center justify-between">
                        <div class="flex items-center">
                            <input id="remember" name="remember" type="checkbox"
                                   class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded transition-colors">
                            <label for="remember" class="ml-3 block text-sm font-medium text-gray-700">
                                Ingat saya
                            </label>
                        </div>
                    </div>

                    <div class="pt-2">
                        <button type="submit"
                                class="group relative w-full flex justify-center py-4 px-6 border border-transparent text-base font-semibold rounded-xl text-white bg-gradient-to-r from-blue-600 via-blue-700 to-indigo-600 hover:from-blue-700 hover:via-blue-800 hover:to-indigo-700 focus:outline-none focus:ring-4 focus:ring-blue-500/50 transition-all duration-300 shadow-lg hover:shadow-xl transform hover:-translate-y-0.5">
                            <span class="absolute left-0 inset-y-0 flex items-center pl-6">
                                <svg class="h-5 w-5 text-blue-200 group-hover:text-white transition-colors" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M5 9V7a5 5 0 0110 0v2a2 2 0 012 2v5a2 2 0 01-2 2H5a2 2 0 01-2-2v-5a2 2 0 012-2zm8-2v2H7V7a3 3 0 016 0z" clip-rule="evenodd" />
                                </svg>
                            </span>
                            <span class="flex items-center">
                                Masuk ke Dashboard
                                <svg class="ml-2 h-4 w-4 group-hover:translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6"></path>
                                </svg>
                            </span>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</body>
</html>

            </div>
        </div>
    </div>
</body>
</html>
