@props([
    'align' => 'left',
])

@php
    $alignmentClasses = $align === 'right'
        ? 'end-0'
        : 'start-0';
@endphp

<div
    class="relative inline-flex"
    x-data="{ open: false }"
    @click.outside="open = false"
    @keydown.escape.window="open = false"
>
    <button
        type="button"
        @click.stop="open = ! open"
        class="rounded p-1 text-gray-500 hover:bg-gray-100 hover:text-gray-800 focus:outline-none focus:ring-2 focus:ring-indigo-500"
        aria-label="İşlemler"
        :aria-expanded="open.toString()"
    >
        <svg class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
            <path d="M10 4.5a1.5 1.5 0 1 1 0-3 1.5 1.5 0 0 1 0 3ZM10 11.5a1.5 1.5 0 1 1 0-3 1.5 1.5 0 0 1 0 3ZM10 18.5a1.5 1.5 0 1 1 0-3 1.5 1.5 0 0 1 0 3Z" />
        </svg>
    </button>

    <div
        x-show="open"
        x-cloak
        x-transition:enter="transition ease-out duration-100"
        x-transition:enter-start="opacity-0 scale-95"
        x-transition:enter-end="opacity-100 scale-100"
        x-transition:leave="transition ease-in duration-75"
        x-transition:leave-start="opacity-100 scale-100"
        x-transition:leave-end="opacity-0 scale-95"
        class="absolute {{ $alignmentClasses }} top-full z-50 mt-1 w-44 origin-top-left rounded-md bg-white py-1 shadow-lg ring-1 ring-black/5"
        style="display: none;"
        @click="open = false"
    >
        {{ $slot }}
    </div>
</div>
