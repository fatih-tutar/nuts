<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">Özet</h2>
    </x-slot>

    <div class="py-8">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
            <div class="grid grid-cols-2 md:grid-cols-5 gap-4">
                <div class="bg-white shadow-sm sm:rounded-lg p-4">
                    <div class="text-xs uppercase tracking-wide text-gray-500">Ürün</div>
                    <div class="mt-1 text-2xl font-semibold text-gray-900">{{ $stats['products'] }}</div>
                </div>
                <div class="bg-white shadow-sm sm:rounded-lg p-4">
                    <div class="text-xs uppercase tracking-wide text-gray-500">Müşteri</div>
                    <div class="mt-1 text-2xl font-semibold text-gray-900">{{ $stats['customers'] }}</div>
                </div>
                <div class="bg-white shadow-sm sm:rounded-lg p-4">
                    <div class="text-xs uppercase tracking-wide text-gray-500">Sezon</div>
                    <div class="mt-1 text-2xl font-semibold text-gray-900">{{ $stats['seasons'] }}</div>
                </div>
                <div class="bg-white shadow-sm sm:rounded-lg p-4">
                    <div class="text-xs uppercase tracking-wide text-gray-500">Sipariş</div>
                    <div class="mt-1 text-2xl font-semibold text-gray-900">{{ $stats['orders'] }}</div>
                </div>
                <div class="bg-white shadow-sm sm:rounded-lg p-4">
                    <div class="text-xs uppercase tracking-wide text-gray-500">Sipariş verdi</div>
                    <div class="mt-1 text-2xl font-semibold text-amber-700">{{ $stats['open_orders'] }}</div>
                </div>
            </div>

            <div class="bg-white shadow-sm sm:rounded-lg p-6 flex flex-wrap gap-3">
                <a href="{{ route('orders.create') }}" class="inline-flex items-center px-4 py-2 bg-gray-800 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700">Yeni sipariş</a>
                <a href="{{ route('orders.index', ['status' => 'ordered']) }}" class="inline-flex items-center px-4 py-2 bg-white border border-gray-300 rounded-md font-semibold text-xs text-gray-700 uppercase tracking-widest hover:bg-gray-50">2026 ceviz (açık)</a>
                <a href="{{ route('customers.create') }}" class="inline-flex items-center px-4 py-2 bg-white border border-gray-300 rounded-md font-semibold text-xs text-gray-700 uppercase tracking-widest hover:bg-gray-50">Yeni müşteri</a>
                <a href="{{ route('seasons.create') }}" class="inline-flex items-center px-4 py-2 bg-white border border-gray-300 rounded-md font-semibold text-xs text-gray-700 uppercase tracking-widest hover:bg-gray-50">Yeni sezon</a>
            </div>
        </div>
    </div>
</x-app-layout>
