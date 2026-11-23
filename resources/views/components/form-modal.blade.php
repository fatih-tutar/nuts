@props([
    'name',
    'title',
    'show' => false,
    'maxWidth' => 'lg',
])

<x-modal :name="$name" :show="$show" :maxWidth="$maxWidth" focusable>
    <div class="relative p-6">
        <button
            type="button"
            x-on:click="$dispatch('close')"
            class="absolute top-4 right-4 inline-flex items-center justify-center rounded-md p-1.5 text-gray-400 hover:text-gray-600 hover:bg-gray-100 focus:outline-none focus:ring-2 focus:ring-indigo-500"
            aria-label="Kapat"
        >
            <svg class="h-5 w-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" aria-hidden="true">
                <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
            </svg>
        </button>

        <h3 class="text-lg font-medium text-gray-900 pr-8">{{ $title }}</h3>

        <div class="mt-4">
            {{ $slot }}
        </div>
    </div>
</x-modal>
