<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">Sezonlar</h2>
            <button
                type="button"
                x-data=""
                x-on:click="$dispatch('open-modal', 'create-season')"
                class="inline-flex items-center px-4 py-2 bg-gray-800 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700"
            >
                Yeni sezon
            </button>
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
                            <th class="w-10 px-1 sm:px-2 py-3"></th>
                            <th class="px-4 py-3 text-left font-medium text-gray-500">Sezon</th>
                            <th class="px-4 py-3 text-right font-medium text-gray-500">Birim fiyat</th>
                            <th class="px-4 py-3 text-right font-medium text-gray-500">Altın</th>
                            <th class="px-4 py-3 text-right font-medium text-gray-500">Dolar</th>
                            <th class="px-4 py-3 text-right font-medium text-gray-500">Toplam kg</th>
                            <th class="px-4 py-3 text-right font-medium text-gray-500">Ciro ₺</th>
                            <th class="px-4 py-3 text-right font-medium text-gray-500">Ciro $</th>
                            <th class="px-4 py-3 text-right font-medium text-gray-500">Ciro altın</th>
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
                                <td class="px-1 sm:px-2 py-3">
                                    <x-row-menu>
                                        <x-row-menu-link :href="route('orders.index', ['season_id' => $season->id])">Siparişler</x-row-menu-link>
                                        <x-row-menu-link :href="route('seasons.index', ['edit' => $season->id])">Düzenle</x-row-menu-link>
                                        <form action="{{ route('seasons.destroy', $season) }}" method="POST" onsubmit="return confirm('Silmek istediğinize emin misiniz?')">
                                            @csrf
                                            @method('DELETE')
                                            <x-row-menu-button variant="danger">Sil</x-row-menu-button>
                                        </form>
                                    </x-row-menu>
                                </td>
                                <td class="px-4 py-3 text-gray-900 font-medium">{{ $season->displayName() }}</td>
                                <td class="px-4 py-3 text-right text-gray-700">{{ number_format((float) $season->unit_price, 2, ',', '.') }}</td>
                                <td class="px-4 py-3 text-right text-gray-600">{{ $season->gold_price !== null ? number_format((float) $season->gold_price, 2, ',', '.') : '—' }}</td>
                                <td class="px-4 py-3 text-right text-gray-600">{{ $season->usd_rate !== null ? number_format((float) $season->usd_rate, 2, ',', '.') : '—' }}</td>
                                <td class="px-4 py-3 text-right text-gray-700">{{ number_format($kg, 2, ',', '.') }}</td>
                                <td class="px-4 py-3 text-right text-gray-700">{{ number_format($revenue, 2, ',', '.') }}</td>
                                <td class="px-4 py-3 text-right text-gray-600">{{ $usd !== null ? number_format($usd, 2, ',', '.') : '—' }}</td>
                                <td class="px-4 py-3 text-right text-gray-600">{{ $gold !== null ? number_format($gold, 2, ',', '.') : '—' }}</td>
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

    <x-form-modal name="create-season" title="Yeni sezon" :show="$showCreateModal" maxWidth="2xl">
        <form method="POST" action="{{ route('seasons.store') }}" class="space-y-4">
            @csrf
            <input type="hidden" name="_modal" value="create-season">
            @include('seasons._form', ['idPrefix' => 'create_', 'season' => null, 'products' => $products])
            <div class="flex gap-3 pt-2">
                <x-primary-button>Kaydet</x-primary-button>
                <button type="button" x-on:click="$dispatch('close')" class="inline-flex items-center px-4 py-2 text-sm text-gray-600 hover:underline">İptal</button>
            </div>
        </form>
    </x-form-modal>

    @if ($editing)
        <x-form-modal name="edit-season" title="Sezon düzenle" :show="$showEditModal" maxWidth="2xl">
            <form method="POST" action="{{ route('seasons.update', $editing) }}" class="space-y-4">
                @csrf
                @method('PUT')
                <input type="hidden" name="_modal" value="edit-season">
                <input type="hidden" name="_edit_id" value="{{ $editing->id }}">
                @include('seasons._form', ['idPrefix' => 'edit_', 'season' => $editing, 'products' => $products])
                <div class="flex gap-3 pt-2">
                    <x-primary-button>Güncelle</x-primary-button>
                    <button type="button" x-on:click="$dispatch('close')" class="inline-flex items-center px-4 py-2 text-sm text-gray-600 hover:underline">İptal</button>
                </div>
            </form>
        </x-form-modal>
    @endif
</x-app-layout>
