@props(['field'])

@error($field)
    <div class="mt-2 flex items-start gap-2 text-sm text-red-600">
        <i class="fas fa-exclamation-circle mt-0.5 flex-shrink-0"></i>
        <span class="font-medium">{{ $message }}</span>
    </div>
@enderror

