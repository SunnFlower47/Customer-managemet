@props([
    'icon' => 'fas fa-inbox',
    'title' => 'Tidak ada data',
    'description' => 'Belum ada data yang ditemukan.',
    'action' => null,
    'actionText' => 'Tambah Data',
    'actionRoute' => null
])

<div class="flex flex-col items-center justify-center py-16 px-4">
    <div class="w-24 h-24 bg-gradient-to-br from-gray-100 to-gray-200 rounded-full flex items-center justify-center mb-6">
        <i class="{{ $icon }} text-gray-400 text-4xl"></i>
    </div>
    <h3 class="text-xl font-bold text-gray-900 mb-2">{{ $title }}</h3>
    <p class="text-gray-500 text-center max-w-md mb-6">{{ $description }}</p>
    @if($actionRoute)
        <a href="{{ $actionRoute }}" class="inline-flex items-center px-6 py-3 bg-gradient-to-r from-blue-600 to-blue-700 text-white rounded-xl hover:shadow-lg hover:scale-105 transition-all duration-200 font-semibold">
            <i class="fas fa-plus mr-2"></i>{{ $actionText }}
        </a>
    @elseif($action)
        {{ $action }}
    @endif
</div>

