            @extends('layouts.app')

            @section('title', 'Pelanggan - WiFi Billing Management')

            @section('content')
            <div class="max-w-7xl mx-auto px-4 py-6 sm:px-6 lg:px-8">
                <div class="sm:flex sm:items-center">
                    <div class="sm:flex-auto">
                        <h1 class="text-2xl font-semibold text-gray-900">Data Pelanggan</h1>
                        <p class="mt-2 text-sm text-gray-700">Kelola data pelanggan WiFi.</p>
                    </div>
                    <div class="mt-4 sm:mt-0 sm:ml-16 sm:flex-none">
                        <a href="{{ route('pelanggans.create', request()->only(['page', 'search', 'status', 'penagih_id', 'paket_id'])) }}" class="inline-flex items-center justify-center rounded-md border border-transparent bg-blue-600 px-4 py-2 text-sm font-medium text-white shadow-sm hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 sm:w-auto">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                            </svg>
                            Tambah Pelanggan
                        </a>
                    </div>
                </div>

                <!-- Search and Filters -->
                <div class="mt-6 bg-white shadow rounded-lg p-6">
                    <form method="GET" action="{{ route('pelanggans.index') }}" class="space-y-4">
                        <!-- Search -->
                        <div>
                            <label for="search" class="block text-sm font-medium text-gray-700 mb-2">
                                <i class="fas fa-search mr-1 text-gray-400"></i>Cari Pelanggan
                            </label>
                            <input type="text" name="search" id="search" value="{{ request('search') }}"
                                placeholder="Cari berdasarkan nama, PPPoE, no HP, atau alamat..."
                                class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition duration-200 text-sm">
                        </div>

                        <!-- Filters -->
                        <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                            <div>
                                <label for="status" class="block text-sm font-medium text-gray-700 mb-2">
                                    <i class="fas fa-info-circle mr-1 text-gray-400"></i>Status
                                </label>
                                <select name="status" id="status" class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition duration-200 text-sm">
                                    <option value="">Semua Status</option>
                                    <option value="aktif" {{ request('status') === 'aktif' ? 'selected' : '' }}>Aktif</option>
                                    <option value="nonaktif" {{ request('status') === 'nonaktif' ? 'selected' : '' }}>Nonaktif</option>
                                    <option value="suspend" {{ request('status') === 'suspend' ? 'selected' : '' }}>Suspend</option>
                                </select>
                            </div>
                            <div>
                                <label for="paket_id" class="block text-sm font-medium text-gray-700 mb-2">
                                    <i class="fas fa-box mr-1 text-gray-400"></i>Paket
                                </label>
                                <select name="paket_id" id="paket_id" class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition duration-200 text-sm">
                                    <option value="">Semua Paket</option>
                                    @foreach($pakets as $paket)
                                        <option value="{{ $paket->id }}" {{ request('paket_id') == $paket->id ? 'selected' : '' }}>
                                            {{ $paket->nama_paket }} - Rp {{ number_format($paket->harga, 0, ',', '.') }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div>
                                <label for="penagih_id" class="block text-sm font-medium text-gray-700 mb-2">
                                    <i class="fas fa-user-tie mr-1 text-gray-400"></i>Penagih
                                </label>
                                <div class="relative">
                                    <input type="text"
                                        id="penagih_search"
                                        class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition duration-200 text-sm"
                                        placeholder="Cari penagih..."
                                        autocomplete="off">
                                    <input type="hidden" name="penagih_id" id="penagih_id" value="{{ request('penagih_id') }}">
                                    <div id="penagih_dropdown" class="absolute z-10 w-full mt-1 bg-white border border-gray-300 rounded-lg shadow-lg hidden max-h-60 overflow-y-auto">
                                        <div class="px-4 py-2 text-gray-500 cursor-pointer hover:bg-gray-100" data-value="">
                                            Semua Penagih
                                        </div>
                                        @foreach($penagihs as $penagih)
                                            <div class="px-4 py-2 cursor-pointer hover:bg-gray-100" data-value="{{ $penagih->id }}" data-name="{{ $penagih->nama }}">
                                                {{ $penagih->nama }}
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                            </div>

                            <div class="flex items-end space-x-2">
                                <button type="submit" class="flex-1 bg-blue-600 text-white px-4 py-3 rounded-lg hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transition duration-200 font-medium">
                                    <i class="fas fa-filter mr-2"></i>Filter
                                </button>
                                <a href="{{ route('pelanggans.export.pdf', request()->query()) }}" class="flex-1 bg-green-600 text-white px-4 py-3 rounded-lg hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-green-500 focus:ring-offset-2 text-center text-sm font-medium transition duration-200">
                                    <i class="fas fa-download mr-2"></i>Export PDF
                                </a>
                            </div>
                        </div>
                    </form>
                </div>

                <!-- Data Table -->
                <div class="mt-8 bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden">
                    <!-- Desktop Table -->
                    <div class="hidden lg:block overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        <i class="fas fa-user mr-2 text-gray-400"></i>Nama
                                    </th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        <i class="fas fa-wifi mr-2 text-gray-400"></i>PPPoE
                                    </th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        <i class="fas fa-map-marker-alt mr-2 text-gray-400"></i>Alamat
                                    </th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        <i class="fas fa-box mr-2 text-gray-400"></i>Paket
                                    </th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        <i class="fas fa-user-tie mr-2 text-gray-400"></i>Penagih
                                    </th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        <i class="fas fa-calendar mr-2 text-gray-400"></i>Tanggal Pembayaran
                                    </th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        <i class="fas fa-info-circle mr-2 text-gray-400"></i>Status
                                    </th>
                                    <th scope="col" class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        <i class="fas fa-cog mr-2 text-gray-400"></i>Aksi
                                    </th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @forelse($pelanggans as $pelanggan)
                                <tr class="hover:bg-gray-50 transition duration-150">
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="flex items-center">
                                            <div class="flex-shrink-0 h-10 w-10">
                                                <div class="h-10 w-10 rounded-full bg-gray-100 flex items-center justify-center">
                                                    <span class="text-gray-600 font-semibold text-sm">{{ substr($pelanggan->nama, 0, 1) }}</span>
                                                </div>
                                            </div>
                                            <div class="ml-4">
                                                <div class="text-sm font-medium text-gray-900">{{ $pelanggan->nama }}</div>
                                                <div class="text-sm text-gray-500">{{ $pelanggan->no_hp }}</div>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm text-gray-900 font-mono">{{ $pelanggan->pppoe }}</div>
                                    </td>
                                    <td class="px-6 py-4">
                                        <div class="text-sm text-gray-900 max-w-xs truncate" title="{{ $pelanggan->alamat }}">
                                            {{ $pelanggan->alamat }}
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm text-gray-900">{{ $pelanggan->paket->nama_paket }}</div>
                                        <div class="text-sm text-gray-500">Rp {{ number_format((float)$pelanggan->paket->harga, 0, ',', '.') }}</div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm text-gray-900">
                                            @if($pelanggan->penagih)
                                                {{ $pelanggan->penagih->nama }}
                                            @else
                                                <span class="text-gray-400 italic">Belum ada penagih</span>
                                            @endif
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm text-gray-900">Tanggal {{ $pelanggan->tanggal_pembayaran }}</div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                                            {{ $pelanggan->status === 'aktif' ? 'bg-green-100 text-green-800' :
                                            ($pelanggan->status === 'suspend' ? 'bg-yellow-100 text-yellow-800' : 'bg-red-100 text-red-800') }}">
                                            {{ ucfirst($pelanggan->status) }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-center text-sm font-medium">
                                        <div class="flex justify-center space-x-2">
                                            <!-- Tombol Detail -->
                                            <a href="{{ route('pelanggans.show', $pelanggan) }}"
                                            class="inline-flex items-center px-3 py-1.5 bg-blue-100 text-blue-700 rounded-md hover:bg-blue-200 transition duration-150"
                                            title="Lihat Detail">
                                                <i class="fas fa-eye mr-1"></i>Detail
                                            </a>

                                            <!-- Tombol Edit -->
                                            <a href="{{ route('pelanggans.edit', array_merge([$pelanggan], request()->only(['page', 'search', 'status', 'penagih_id', 'paket_id']))) }}"
                                            class="inline-flex items-center px-3 py-1.5 bg-indigo-100 text-indigo-700 rounded-md hover:bg-indigo-200 transition duration-150"
                                            title="Edit Data">
                                                <i class="fas fa-edit mr-1"></i>Edit
                                            </a>

                                            <!-- Tombol Hapus -->
                                            <form method="POST" action="{{ route('pelanggans.destroy', $pelanggan) }}" class="inline delete-form">
                                                @csrf
                                                @method('DELETE')
                                                @if(request('page'))
                                                    <input type="hidden" name="page" value="{{ request('page') }}">
                                                @endif
                                                @if(request('search'))
                                                    <input type="hidden" name="search" value="{{ request('search') }}">
                                                @endif
                                                @if(request('status'))
                                                    <input type="hidden" name="status" value="{{ request('status') }}">
                                                @endif
                                                @if(request('penagih_id'))
                                                    <input type="hidden" name="penagih_id" value="{{ request('penagih_id') }}">
                                                @endif
                                                @if(request('paket_id'))
                                                    <input type="hidden" name="paket_id" value="{{ request('paket_id') }}">
                                                @endif
                                                <button type="submit"
                                                        class="inline-flex items-center px-3 py-1.5 bg-red-100 text-red-700 rounded-md hover:bg-red-200 transition duration-150"
                                                        title="Hapus Data">
                                                    <i class="fas fa-trash mr-1"></i>Hapus
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="8" class="px-6 py-12 text-center">
                                        <div class="flex flex-col items-center">
                                            <i class="fas fa-users text-gray-300 text-4xl mb-4"></i>
                                            <h3 class="text-lg font-medium text-gray-900 mb-2">Tidak ada pelanggan</h3>
                                            <p class="text-gray-500">Belum ada data pelanggan yang ditemukan.</p>
                                        </div>
                                    </td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <!-- Mobile Cards -->
                    <div class="lg:hidden">
                        @forelse($pelanggans as $pelanggan)
                        <div class="border-b border-gray-200 p-4 hover:bg-gray-50 transition duration-150">
                            <div class="flex items-start justify-between">
                                <div class="flex-1">
                                    <div class="flex items-center mb-2">
                                        <div class="flex-shrink-0 h-10 w-10">
                                            <div class="h-10 w-10 rounded-full bg-gray-100 flex items-center justify-center">
                                                <span class="text-gray-600 font-semibold text-sm">{{ substr($pelanggan->nama, 0, 1) }}</span>
                                            </div>
                                        </div>
                                        <div class="ml-3">
                                            <div class="text-sm font-medium text-gray-900">{{ $pelanggan->nama }}</div>
                                            <div class="text-sm text-gray-500">{{ $pelanggan->no_hp }}</div>
                                        </div>
                                    </div>

                                    <div class="grid grid-cols-1 gap-2 text-xs text-gray-600 mb-3">
                                        <div>
                                            <span class="font-medium">PPPoE:</span><br>
                                            <span class="font-mono text-xs break-all">{{ \Illuminate\Support\Str::limit($pelanggan->pppoe, 25) }}</span>
                                        </div>
                                        <div class="grid grid-cols-2 gap-2">
                                            <div>
                                                <span class="font-medium">Paket:</span><br>
                                                <span>{{ $pelanggan->paket->nama_paket }}</span>
                                            </div>
                                            <div>
                                                <span class="font-medium">Tgl Bayar:</span><br>
                                                <span>Tanggal {{ $pelanggan->tanggal_pembayaran }}</span>
                                            </div>
                                        </div>
                                        <div>
                                            <span class="font-medium">Alamat:</span><br>
                                            <span>{{ $pelanggan->alamat }}</span>
                                        </div>
                                        <div>
                                            <span class="font-medium">Penagih:</span><br>
                                            <span>{{ $pelanggan->penagih ? $pelanggan->penagih->nama : 'Belum ditugaskan' }}</span>
                                        </div>
                                        <div>
                                            <span class="font-medium">Tgl Bayar:</span><br>
                                            <span>Tanggal {{ $pelanggan->tanggal_pembayaran }}</span>
                                        </div>
                                    </div>

                                    <div class="flex items-center justify-between">
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                                            @if($pelanggan->status === 'aktif') bg-green-100 text-green-800
                                            @elseif($pelanggan->status === 'nonaktif') bg-red-100 text-red-800
                                            @else bg-yellow-100 text-yellow-800 @endif">
                                            {{ ucfirst($pelanggan->status) }}
                                        </span>
                                    </div>
                                </div>

                                <div class="flex flex-col space-y-2 ml-4">
                                    <a href="{{ route('pelanggans.show', $pelanggan) }}"
                                    class="inline-flex items-center px-3 py-1.5 border border-gray-300 shadow-sm text-xs font-medium rounded text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                                        <i class="fas fa-eye mr-1"></i>Detail
                                    </a>
                                    <a href="{{ route('pelanggans.edit', array_merge([$pelanggan], request()->only(['page', 'search', 'status', 'penagih_id', 'paket_id']))) }}"
                                    class="inline-flex items-center px-3 py-1.5 border border-gray-300 shadow-sm text-xs font-medium rounded text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                                        <i class="fas fa-edit mr-1"></i>Edit
                                    </a>
                                    <form method="POST" action="{{ route('pelanggans.destroy', $pelanggan) }}" class="delete-form">
                                        @csrf
                                        @method('DELETE')
                                        @if(request('page'))
                                            <input type="hidden" name="page" value="{{ request('page') }}">
                                        @endif
                                        @if(request('search'))
                                            <input type="hidden" name="search" value="{{ request('search') }}">
                                        @endif
                                        @if(request('status'))
                                            <input type="hidden" name="status" value="{{ request('status') }}">
                                        @endif
                                        @if(request('penagih_id'))
                                            <input type="hidden" name="penagih_id" value="{{ request('penagih_id') }}">
                                        @endif
                                        @if(request('paket_id'))
                                            <input type="hidden" name="paket_id" value="{{ request('paket_id') }}">
                                        @endif
                                        <button type="submit"
                                                class="inline-flex items-center px-3 py-1.5 border border-transparent text-xs font-medium rounded text-white bg-red-600 hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500">
                                            <i class="fas fa-trash mr-1"></i>Hapus
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </div>
                        @empty
                        <div class="text-center py-12">
                            <i class="fas fa-users text-gray-400 text-4xl mb-4"></i>
                            <h3 class="text-lg font-medium text-gray-900 mb-2">Tidak ada pelanggan</h3>
                            <p class="text-gray-500">Mulai dengan menambahkan pelanggan pertama Anda.</p>
                        </div>
                        @endforelse
                    </div>
                </div>

                <!-- Pagination -->
                @if($pelanggans->hasPages())
                <div class="mt-6 bg-white rounded-lg shadow-sm border border-gray-200 px-6 py-4">
                    {{ $pelanggans->appends(request()->query())->links('vendor.pagination.tailwind') }}
                </div>
                @endif
            </div>

            @push('scripts')
            <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
            <script>
            document.addEventListener('DOMContentLoaded', function() {
                console.log('Pelanggans page loaded, initializing SweetAlert...');

                // Searchable Penagih Dropdown
                const searchInput = document.getElementById('penagih_search');
                const hiddenInput = document.getElementById('penagih_id');
                const dropdown = document.getElementById('penagih_dropdown');

                if (searchInput && hiddenInput && dropdown) {
                    const allOptions = dropdown.querySelectorAll('[data-value]');
                    let isDropdownOpen = false;
                    let clickTimeout = null;

                    // Set initial value if selected
                    const selectedValue = hiddenInput.value;
                    if (selectedValue) {
                        const selectedOption = dropdown.querySelector(`[data-value="${selectedValue}"]`);
                        if (selectedOption) {
                            searchInput.value = selectedOption.dataset.name || selectedOption.textContent.trim();
                        }
                    }

                    // Show dropdown
                    function showDropdown() {
                        dropdown.classList.remove('hidden');
                        isDropdownOpen = true;
                        filterOptions();
                    }

                    // Hide dropdown
                    function hideDropdown() {
                        dropdown.classList.add('hidden');
                        isDropdownOpen = false;
                    }

                    // Filter options based on search
                    function filterOptions() {
                        const searchTerm = searchInput.value.toLowerCase().trim();
                        allOptions.forEach(option => {
                            const text = option.textContent.toLowerCase().trim();
                            if (text.includes(searchTerm)) {
                                option.style.display = 'block';
                            } else {
                                option.style.display = 'none';
                            }
                        });
                    }

                    // Show/hide dropdown
                    searchInput.addEventListener('focus', function() {
                        showDropdown();
                    });

                    searchInput.addEventListener('blur', function() {
                        // Delay hiding to allow click on options
                        clickTimeout = setTimeout(() => {
                            hideDropdown();
                        }, 300);
                    });

                    // Filter options based on search
                    searchInput.addEventListener('input', function() {
                        showDropdown();
                    });

                    // Handle option selection
                    dropdown.addEventListener('mousedown', function(e) {
                        e.preventDefault(); // Prevent input blur
                    });

                    dropdown.addEventListener('click', function(e) {
                        const option = e.target.closest('[data-value]');
                        if (option) {
                            const value = option.dataset.value;
                            const name = option.dataset.name || option.textContent.trim();

                            // Clear timeout to prevent hiding
                            if (clickTimeout) {
                                clearTimeout(clickTimeout);
                            }

                            hiddenInput.value = value;
                            searchInput.value = name;
                            hideDropdown();

                            console.log('Penagih selected:', name, 'ID:', value);
                        }
                    });

                    // Handle escape key
                    searchInput.addEventListener('keydown', function(e) {
                        if (e.key === 'Escape') {
                            hideDropdown();
                        }
                    });
                }

                // Handle delete confirmation with SweetAlert
                const deleteForms = document.querySelectorAll('.delete-form');
                console.log('Found delete forms:', deleteForms.length);

                deleteForms.forEach(form => {
                    form.addEventListener('submit', function(e) {
                        e.preventDefault();
                        console.log('Delete form submitted');

                        const form = this;
                        // Get pelanggan name from both desktop and mobile views
                        let pelangganName = '';
                        const tableRow = form.closest('tr');
                        const mobileCard = form.closest('.border-b');

                        if (tableRow) {
                            // Desktop view
                            pelangganName = tableRow.querySelector('td:first-child').textContent.trim();
                        } else if (mobileCard) {
                            // Mobile view
                            pelangganName = mobileCard.querySelector('.text-sm.font-medium.text-gray-900').textContent.trim();
                        }
                        console.log('Pelanggan to delete:', pelangganName);

                        Swal.fire({
                            title: 'Hapus Pelanggan?',
                            text: `Apakah Anda yakin ingin menghapus pelanggan "${pelangganName}"?`,
                            icon: 'warning',
                            showCancelButton: true,
                            confirmButtonColor: '#EF4444',
                            cancelButtonColor: '#6B7280',
                            confirmButtonText: 'Ya, Hapus!',
                            cancelButtonText: 'Batal'
                        }).then((result) => {
                            if (result.isConfirmed) {
                                console.log('User confirmed deletion');
                                form.submit();
                            } else {
                                console.log('User cancelled deletion');
                            }
                        });
                    });
                });

                // Show SweetAlert for session messages
                @if(session('success'))
                    Swal.fire({
                        title: 'Berhasil!',
                        text: '{{ session('success') }}',
                        icon: 'success',
                        confirmButtonColor: '#10B981'
                    });
                @endif

                @if(session('error'))
                    Swal.fire({
                        title: 'Error!',
                        text: '{{ session('error') }}',
                        icon: 'error',
                        confirmButtonColor: '#EF4444'
                    });
                @endif
            });
            </script>
            @endpush
            @endsection
