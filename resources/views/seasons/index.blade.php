<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">Sezonlar</h2>
            <a href="{{ route('seasons.create') }}" class="inline-flex items-center px-4 py-2 bg-gray-800 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700">Yeni sezon</a>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-4">
            @if (session('status'))
                <div class="bg-green-50 text-green-800 px-4 py-3 rounded-md text-sm">{{ session('status') }}</div>
            @endif

            <div class="bg-white shadow-sm sm:rounded-lg overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200 text-sm">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-4 py-3 text-left font-medium text-gray-500">Sezon</th>
                            <th class="px-4 py-3 text-right font-medium text-gray-500">Birim fiyat</th>
                            <th class="px-4 py-3 text-right font-medium text-gray-500">Altın</th>
                            <th class="px-4 py-3 text-right font-medium text-gray-500">Dolar</th>
                            <th class="px-4 py-3 text-right font-medium text-gray-500">Toplam kg</th>
                            <th class="px-4 py-3 text-right font-medium text-gray-500">Ciro ₺</th>
                            <th class="px-4 py-3 text-right font-medium text-gray-500">Ciro $</th>
                            <th class="px-4 py-3 text-right font-medium text-gray-500">Ciro altın</th>
                            <th class="px-4 py-3 text-right font-medium text-gray-500"></th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @forelse ($seasons as $season)
                            @php
                                $kg = (float) ($season->orders_quantity_kg ?? 0);
                                $revenue = round($kg * (float) $season->unit_price, 2);
                                $usd = $season->usd_rate > 0 ? round($revenue / (float) $season->usd_rate, 2) : null;
                                $gold = $season->gold_price > 0 ? round($revenue / (float) $season->gold_price, 2) : null;
                            @endphp
                            <tr>
                                <td class="px-4 py-3 text-gray-900 font-medium">{{ $season->displayName() }}</td>
                                <td class="px-4 py-3 text-right text-gray-700">{{ number_format((float) $season->unit_price, 2, ',', '.') }}</td>
                                <td class="px-4 py-3 text-right text-gray-600">{{ $season->gold_price !== null ? number_format((float) $season->gold_price, 2, ',', '.') : '—' }}</td>
                                <td class="px-4 py-3 text-right text-gray-600">{{ $season->usd_rate !== null ? number_format((float) $season->usd_rate, 2, ',', '.') : '—' }}</td>
                                <td class="px-4 py-3 text-right text-gray-700">{{ number_format($kg, 2, ',', '.') }}</td>
                                <td class="px-4 py-3 text-right text-gray-700">{{ number_format($revenue, 2, ',', '.') }}</td>
                                <td class="px-4 py-3 text-right text-gray-600">{{ $usd !== null ? number_format($usd, 2, ',', '.') : '—' }}</td>
                                <td class="px-4 py-3 text-right text-gray-600">{{ $gold !== null ? number_format($gold, 2, ',', '.') : '—' }}</td>
                                <td class="px-4 py-3 text-right space-x-3 whitespace-nowrap">
                                    <a href="{{ route('orders.index', ['season_id' => $season->id]) }}" class="text-gray-700 hover:underline">Siparişler</a>
                                    <a href="{{ route('seasons.edit', $season) }}" class="text-indigo-600 hover:underline">Düzenle</a>
                                    <form action="{{ route('seasons.destroy', $season) }}" method="POST" class="inline" onsubmit="return confirm('Silmek istediğinize emin misiniz?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="text-red-600 hover:underline">Sil</button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="9" class="px-4 py-8 text-center text-gray-500">Henüz sezon yok.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</x-app-layout>
