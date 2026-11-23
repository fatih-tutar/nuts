<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between gap-4 flex-wrap">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">Siparişler</h2>
            <a href="{{ route('orders.create') }}" class="inline-flex items-center px-4 py-2 bg-gray-800 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700">Yeni sipariş</a>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-4">
            @if (session('status'))
                <div class="bg-green-50 text-green-800 px-4 py-3 rounded-md text-sm">{{ session('status') }}</div>
            @endif

            <form method="GET" class="flex flex-wrap gap-2 items-end">
                <div>
                    <x-input-label for="season_id" value="Sezon" />
                    <select id="season_id" name="season_id" class="mt-1 block border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm text-sm">
                        <option value="">Tümü</option>
                        @foreach ($seasons as $season)
                            <option value="{{ $season->id }}" @selected(($filters['season_id'] ?? '') == $season->id)>{{ $season->displayName() }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <x-input-label for="status" value="Durum" />
                    <select id="status" name="status" class="mt-1 block border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm text-sm">
                        <option value="">Tümü</option>
                        @foreach ($statuses as $value => $label)
                            <option value="{{ $value }}" @selected(($filters['status'] ?? '') === $value)>{{ $label }}</option>
                        @endforeach
                    </select>
                </div>
                <x-primary-button>Filtrele</x-primary-button>
            </form>

            <div class="bg-white shadow-sm sm:rounded-lg overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200 text-sm">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-4 py-3 text-left font-medium text-gray-500">Müşteri</th>
                            <th class="px-4 py-3 text-left font-medium text-gray-500">Sezon</th>
                            <th class="px-4 py-3 text-right font-medium text-gray-500">Kg</th>
                            <th class="px-4 py-3 text-right font-medium text-gray-500">Tutar ₺</th>
                            <th class="px-4 py-3 text-left font-medium text-gray-500">Durum</th>
                            <th class="px-4 py-3 text-right font-medium text-gray-500"></th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @forelse ($orders as $order)
                            <tr>
                                <td class="px-4 py-3 text-gray-900 font-medium">
                                    @if ($order->customer)
                                        <a href="{{ route('customers.show', $order->customer) }}" class="hover:underline">{{ $order->customer->name }}</a>
                                    @endif
                                </td>
                                <td class="px-4 py-3 text-gray-600">{{ $order->season?->displayName() }}</td>
                                <td class="px-4 py-3 text-right text-gray-700">{{ number_format((float) $order->quantity_kg, 2, ',', '.') }}</td>
                                <td class="px-4 py-3 text-right text-gray-700">{{ number_format($order->revenueTry(), 2, ',', '.') }}</td>
                                <td class="px-4 py-3 text-gray-700">{{ $order->status->label() }}</td>
                                <td class="px-4 py-3 text-right space-x-3 whitespace-nowrap">
                                    <a href="{{ route('orders.edit', $order) }}" class="text-indigo-600 hover:underline">Düzenle</a>
                                    <form action="{{ route('orders.destroy', $order) }}" method="POST" class="inline" onsubmit="return confirm('Silmek istediğinize emin misiniz?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="text-red-600 hover:underline">Sil</button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="px-4 py-8 text-center text-gray-500">Sipariş bulunamadı.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div>{{ $orders->links() }}</div>
        </div>
    </div>
</x-app-layout>
