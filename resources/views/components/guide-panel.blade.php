@props([
    'key' => 'guide',
    'title' => 'Panduan',
    'event' => null,
])

@php
    $eventName = $event ?? 'open-guide-' . \Illuminate\Support\Str::slug($key);
@endphp

<div
    x-data="{ open: false }"
    x-on:{{ $eventName }}.window="open = true"
    x-show="open"
    x-cloak
    class="fixed inset-0 z-50 flex items-center justify-center bg-gray-900/60 p-4 overflow-y-auto"
    role="dialog"
    aria-modal="true"
    @click.self="open = false"
>
    <div class="bg-white rounded-2xl w-full max-w-2xl shadow-2xl overflow-hidden my-auto max-h-[90vh] flex flex-col">
        <!-- Header - Fixed -->
        <div class="flex items-center justify-between px-5 py-4 border-b border-gray-100 flex-shrink-0">
            <div>
                <p class="text-xs uppercase tracking-wide text-blue-500 font-semibold">{{ strtoupper($key) }}</p>
                <h3 class="text-base font-semibold text-gray-900">{{ $title }}</h3>
            </div>
            <button @click="open = false" class="text-gray-400 hover:text-gray-600 transition-colors">
                <i class="fas fa-times text-lg"></i>
            </button>
        </div>
        
        <!-- Content - Scrollable -->
        <div class="px-5 py-4 text-sm text-gray-600 space-y-3 overflow-y-auto flex-1">
            {{ $slot }}
        </div>
        
        <!-- Footer - Fixed -->
        <div class="px-5 py-4 border-t border-gray-100 text-right flex-shrink-0">
            <button @click="open = false" class="btn-secondary px-5">Tutup Panduan</button>
        </div>
    </div>
</div>

