@props([
    'href' => '#',
    'variant' => 'default',
])

@php
    $color = match ($variant) {
        'danger' => 'text-red-600 hover:bg-red-50',
        default => 'text-gray-700 hover:bg-gray-100',
    };
@endphp

<a {{ $attributes->merge(['href' => $href, 'class' => "block w-full px-4 py-2 text-left text-sm {$color}"]) }}>
    {{ $slot }}
</a>
