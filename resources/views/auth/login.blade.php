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

    <title>Login - {{ $companyProfile->nama_perusahaan ?? 'BCM' }}</title>

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
    <link href="https://fonts.bunny.net/css?family=inter:400,500,600,700,800&display=swap" rel="stylesheet" />

    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>

    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <style>
        * { font-family: 'Inter', sans-serif; }
        [x-cloak] { display: none !important; }

        .left-panel {
            background: linear-gradient(135deg, #0f172a 0%, #1e3a5f 55%, #1e40af 100%);
        }
        .grid-pattern {
            background-image:
                linear-gradient(rgba(255,255,255,0.04) 1px, transparent 1px),
                linear-gradient(90deg, rgba(255,255,255,0.04) 1px, transparent 1px);
            background-size: 40px 40px;
        }
        .glass-card {
            background: rgba(255,255,255,0.08);
            backdrop-filter: blur(12px);
            border: 1px solid rgba(255,255,255,0.15);
        }
        .input-field {
            transition: all 0.2s ease;
            border: 1.5px solid #e5e7eb;
        }
        .input-field:focus {
            border-color: #2563eb;
            box-shadow: 0 0 0 3px rgba(37,99,235,0.12);
            outline: none;
        }
        .btn-login {
            background: linear-gradient(135deg, #2563eb, #1d4ed8);
            transition: all 0.2s ease;
            box-shadow: 0 4px 15px rgba(37,99,235,0.35);
        }
        .btn-login:hover:not(:disabled) {
            transform: translateY(-1px);
            box-shadow: 0 8px 25px rgba(37,99,235,0.45);
        }
        .btn-login:disabled { opacity: 0.7; cursor: not-allowed; }

        .pulse-dot { animation: pulse-ring 2s ease infinite; }
        @keyframes pulse-ring {
            0%   { box-shadow: 0 0 0 0 rgba(74,222,128,0.4); }
            70%  { box-shadow: 0 0 0 10px rgba(74,222,128,0); }
            100% { box-shadow: 0 0 0 0 rgba(74,222,128,0); }
        }
        .float-anim { animation: float 4s ease-in-out infinite; }
        @keyframes float {
            0%,100% { transform: translateY(0); }
            50%      { transform: translateY(-8px); }
        }
    </style>
</head>
<body class="antialiased bg-white">
    <div class="min-h-screen flex">

        <!-- ====== LEFT PANEL (hidden on mobile) ====== -->
        <div class="hidden lg:flex lg:w-1/2 left-panel grid-pattern flex-col justify-between p-12 relative overflow-hidden">
            <!-- Top: Brand -->
            <div>
                <a href="{{ route('welcome') }}" class="inline-flex items-center gap-3 text-white hover:opacity-90 transition-opacity">
                    @if($companyProfile && $companyProfile->logo_path)
                        <img src="{{ $companyProfile->logo_url }}" alt="" class="h-10 w-10 object-contain rounded-xl">
                    @else
                        <div class="h-10 w-10 bg-white/20 rounded-xl flex items-center justify-center">
                            <i class="fas fa-wifi text-white"></i>
                        </div>
                    @endif
                    <div>
                        <p class="font-bold text-base leading-tight">{{ $companyProfile->nama_perusahaan ?? 'BCM' }}</p>
                        <p class="text-blue-300 text-xs">WiFi Management System</p>
                    </div>
                </a>
            </div>

            <!-- Middle: Floating card + text -->
            <div class="flex-1 flex flex-col justify-center py-12">
                <div class="mb-8">
                    <div class="inline-flex items-center gap-2 glass-card px-4 py-2 rounded-full mb-5">
                        <span class="w-2 h-2 bg-green-400 rounded-full pulse-dot"></span>
                        <span class="text-xs font-medium text-blue-200">Sistem Aktif & Berjalan</span>
                    </div>
                    <h1 class="text-4xl font-extrabold text-white leading-tight mb-3">
                        Selamat Datang<br>
                        <span class="text-transparent bg-clip-text" style="background:linear-gradient(90deg,#60a5fa,#a78bfa);">
                            Kembali! 👋
                        </span>
                    </h1>
                    <p class="text-blue-200 text-sm leading-relaxed max-w-xs">
                        Masuk ke dashboard untuk mengelola pelanggan, pembayaran, dan monitoring jaringan WiFi Anda.
                    </p>
                </div>

                <!-- Stats card -->
                <div class="float-anim glass-card rounded-2xl p-5 max-w-xs">
                    @php
                        $totalP = \App\Models\Pelanggan::count();
                        $aktifP = \App\Models\Pelanggan::where('status','aktif')->count();
                    @endphp
                    <div class="flex items-center gap-3 mb-4">
                        <div class="h-8 w-8 bg-blue-500 rounded-lg flex items-center justify-center">
                            <i class="fas fa-chart-line text-white text-xs"></i>
                        </div>
                        <p class="text-white text-sm font-semibold">Ringkasan Sistem</p>
                    </div>
                    <div class="grid grid-cols-2 gap-3">
                        <div class="bg-white/10 rounded-xl p-3">
                            <p class="text-2xl font-bold text-white">{{ number_format($totalP) }}</p>
                            <p class="text-xs text-blue-300 mt-0.5">Total Pelanggan</p>
                        </div>
                        <div class="bg-white/10 rounded-xl p-3">
                            <p class="text-2xl font-bold text-green-400">{{ number_format($aktifP) }}</p>
                            <p class="text-xs text-blue-300 mt-0.5">Pelanggan Aktif</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Bottom: version -->
            <div>
                <p class="text-blue-400 text-xs">© {{ date('Y') }} {{ $companyProfile->nama_perusahaan ?? 'BCM' }} &bull; v2.0</p>
            </div>

            <!-- Decorative circles -->
            <div class="absolute -bottom-20 -right-20 w-64 h-64 bg-blue-600/20 rounded-full blur-3xl pointer-events-none"></div>
            <div class="absolute top-20 -right-10 w-40 h-40 bg-purple-600/20 rounded-full blur-2xl pointer-events-none"></div>
        </div>

        <!-- ====== RIGHT PANEL: Login Form ====== -->
        <div class="w-full lg:w-1/2 flex flex-col justify-center px-6 sm:px-12 lg:px-16 xl:px-20 py-12 bg-white">
            <!-- Mobile: back link -->
            <div class="lg:hidden mb-6">
                <a href="{{ route('welcome') }}" class="inline-flex items-center gap-2 text-sm font-medium text-gray-500 hover:text-gray-800 transition">
                    <i class="fas fa-arrow-left text-xs"></i> Kembali
                </a>
            </div>

            <div class="max-w-md w-full mx-auto">
                <!-- Mobile branding -->
                <div class="lg:hidden text-center mb-8">
                    @if($companyProfile && $companyProfile->logo_path)
                        <img src="{{ $companyProfile->logo_url }}" alt="" class="h-16 w-16 object-contain mx-auto mb-3 rounded-2xl">
                    @else
                        <div class="h-16 w-16 bg-blue-600 rounded-2xl flex items-center justify-center mx-auto mb-3">
                            <i class="fas fa-wifi text-white text-2xl"></i>
                        </div>
                    @endif
                    <h1 class="text-xl font-bold text-gray-900">{{ $companyProfile->nama_perusahaan ?? 'BCM' }}</h1>
                    <p class="text-gray-500 text-sm">WiFi Management System</p>
                </div>

                <!-- Form header -->
                <div class="mb-8">
                    <h2 class="text-2xl sm:text-3xl font-bold text-gray-900 mb-1">Masuk ke Dashboard</h2>
                    <p class="text-gray-500 text-sm">Masukkan kredensial akun Anda untuk melanjutkan</p>
                </div>

                <!-- Error alert -->
                @if ($errors && $errors->any())
                    <div class="mb-5 rounded-xl bg-red-50 border border-red-200 p-4">
                        <div class="flex items-start gap-3">
                            <div class="h-5 w-5 bg-red-100 rounded-full flex items-center justify-center flex-shrink-0 mt-0.5">
                                <i class="fas fa-exclamation text-red-500 text-xs"></i>
                            </div>
                            <ul class="text-sm text-red-700 space-y-0.5">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    </div>
                @endif

                <!-- Form -->
                <form method="POST" action="{{ route('login') }}" id="loginForm" class="space-y-5">
                    @csrf

                    <!-- Email -->
                    <div>
                        <label for="email" class="block text-sm font-semibold text-gray-700 mb-2">Email</label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none">
                                <i class="fas fa-envelope text-gray-400 text-sm"></i>
                            </div>
                            <input
                                id="email" name="email" type="email" autocomplete="email" required
                                class="input-field block w-full pl-10 pr-4 py-3 rounded-xl bg-gray-50 text-gray-900 text-sm placeholder-gray-400 focus:bg-white"
                                placeholder="admin@example.com"
                                value="{{ old('email') }}"
                            >
                        </div>
                    </div>

                    <!-- Password -->
                    <div>
                        <label for="password" class="block text-sm font-semibold text-gray-700 mb-2">Password</label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none">
                                <i class="fas fa-lock text-gray-400 text-sm"></i>
                            </div>
                            <input
                                id="password" name="password" type="password" autocomplete="current-password" required
                                class="input-field block w-full pl-10 pr-11 py-3 rounded-xl bg-gray-50 text-gray-900 text-sm placeholder-gray-400 focus:bg-white"
                                placeholder="••••••••"
                            >
                            <button type="button" onclick="togglePassword()"
                                class="absolute inset-y-0 right-0 pr-3.5 flex items-center text-gray-400 hover:text-gray-600 transition">
                                <i id="eyeIcon" class="fas fa-eye text-sm"></i>
                            </button>
                        </div>
                    </div>

                    <!-- Remember me -->
                    <div class="flex items-center justify-between">
                        <label class="flex items-center gap-2 cursor-pointer select-none">
                            <input id="remember" name="remember" type="checkbox"
                                class="h-4 w-4 rounded text-blue-600 border-gray-300 focus:ring-blue-500">
                            <span class="text-sm text-gray-600">Ingat saya</span>
                        </label>
                    </div>

                    <!-- Submit -->
                    <button
                        type="submit" id="submitBtn"
                        class="btn-login w-full flex justify-center items-center gap-2 py-3.5 px-4 rounded-xl text-sm font-semibold text-white">
                        <span id="buttonText">Masuk ke Dashboard</span>
                        <i id="buttonIcon" class="fas fa-arrow-right text-xs"></i>
                        <svg id="buttonSpinner" class="h-4 w-4 animate-spin hidden" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                        </svg>
                    </button>
                </form>

                <!-- Footer -->
                <p class="mt-8 text-center text-xs text-gray-400">
                    © {{ date('Y') }} {{ $companyProfile->nama_perusahaan ?? 'BCM' }} &bull; WiFi Management System
                </p>
            </div>
        </div>
    </div>

    <script>
        function togglePassword() {
            const pwd = document.getElementById('password');
            const icon = document.getElementById('eyeIcon');
            if (pwd.type === 'password') {
                pwd.type = 'text';
                icon.classList.replace('fa-eye', 'fa-eye-slash');
            } else {
                pwd.type = 'password';
                icon.classList.replace('fa-eye-slash', 'fa-eye');
            }
        }

        document.getElementById('loginForm').addEventListener('submit', function() {
            const btn = document.getElementById('submitBtn');
            const text = document.getElementById('buttonText');
            const icon = document.getElementById('buttonIcon');
            const spinner = document.getElementById('buttonSpinner');
            btn.disabled = true;
            text.textContent = 'Memproses...';
            icon.classList.add('hidden');
            spinner.classList.remove('hidden');
        });
    </script>
</body>
</html>
