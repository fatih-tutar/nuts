<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between gap-4 flex-wrap">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">Siparişler</h2>
            <button
                type="button"
                x-data=""
                x-on:click="$dispatch('open-modal', 'create-order')"
                class="inline-flex items-center px-4 py-2 bg-gray-800 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700"
            >
                Yeni sipariş
            </button>
        </div>
    </x-slot>

    <div class="py-4 sm:py-8">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-3 sm:space-y-4">
            @if (session('status'))
                <div class="bg-green-50 text-green-800 px-4 py-3 rounded-md text-sm">{{ session('status') }}</div>
            @endif

            <form method="GET" class="grid grid-cols-2 gap-3 sm:flex sm:flex-wrap sm:gap-4 items-end">
                <div class="min-w-0">
                    <x-input-label for="filter_season_id" value="Sezon" />
                    <select id="filter_season_id" name="season_id" onchange="this.form.submit()" class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm text-sm">
                        <option value="">Tümü</option>
                        @foreach ($seasons as $season)
                            <option value="{{ $season->id }}" @selected((string) ($filters['season_id'] ?? '') === (string) $season->id)>{{ $season->displayName() }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="min-w-0">
                    <x-input-label for="filter_status" value="Durum" />
                    <select id="filter_status" name="status" onchange="this.form.submit()" class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm text-sm">
                        <option value="">Tümü</option>
                        @foreach ($statuses as $value => $label)
                            <option value="{{ $value }}" @selected(($filters['status'] ?? '') === $value)>{{ $label }}</option>
                        @endforeach
                    </select>
                </div>
            </form>

            <div class="bg-white shadow-sm sm:rounded-lg overflow-x-auto">
                <table class="min-w-full table-fixed divide-y divide-gray-200 text-sm">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="w-[40%] px-3 sm:px-4 py-3 text-left font-medium text-gray-500">Müşteri</th>
                            <th class="w-[12%] px-2 sm:px-4 py-3 text-right font-medium text-gray-500">Kg</th>
                            <th class="w-[16%] px-2 sm:px-4 py-3 text-right font-medium text-gray-500">Tutar ₺</th>
                            <th class="w-[20%] px-2 sm:px-4 py-3 text-left font-medium text-gray-500">Durum</th>
                            <th class="w-[12%] px-2 sm:px-4 py-3 text-right font-medium text-gray-500"></th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @forelse ($orders as $order)
                            <tr>
                                <td class="px-3 sm:px-4 py-3 text-gray-900 font-medium">
                                    @if ($order->customer)
                                        <a href="{{ route('customers.show', $order->customer) }}" class="hover:underline break-words">{{ $order->customer->name }}</a>
                                    @endif
                                </td>
                                <td class="px-2 sm:px-4 py-3 text-right text-gray-700 whitespace-nowrap">{{ number_format((float) $order->quantity_kg, 2, ',', '.') }}</td>
                                <td class="px-2 sm:px-4 py-3 text-right text-gray-700 whitespace-nowrap">{{ number_format($order->revenueTry(), 2, ',', '.') }}</td>
                                <td class="px-2 sm:px-4 py-3 text-gray-700">{{ $order->status->label() }}</td>
                                <td class="px-2 sm:px-4 py-3 text-right space-x-2 sm:space-x-3 whitespace-nowrap">
                                    <a href="{{ route('orders.index', ['edit' => $order->id, 'season_id' => $filters['season_id'] ?? $order->season_id]) }}" class="text-indigo-600 hover:underline">Düzenle</a>
                                    <form action="{{ route('orders.destroy', $order) }}" method="POST" class="inline" onsubmit="return confirm('Silmek istediğinize emin misiniz?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="text-red-600 hover:underline">Sil</button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="px-4 py-8 text-center text-gray-500">Sipariş bulunamadı.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div>{{ $orders->links() }}</div>
        </div>
    </div>

    <x-form-modal name="create-order" title="Yeni sipariş" :show="$showCreateModal" maxWidth="2xl">
        <form method="POST" action="{{ route('orders.store') }}" class="space-y-4">
            @csrf
            <input type="hidden" name="_modal" value="create-order">
            @include('orders._form', [
                'idPrefix' => 'create_',
                'order' => null,
                'customers' => $customers,
                'seasons' => $seasons,
                'statuses' => $statuses,
                'selectedCustomerId' => $selectedCustomerId,
                'selectedSeasonId' => $selectedSeasonId,
            ])
            <div class="flex gap-3 pt-2">
                <x-primary-button>Kaydet</x-primary-button>
                <button type="button" x-on:click="$dispatch('close')" class="inline-flex items-center px-4 py-2 text-sm text-gray-600 hover:underline">İptal</button>
            </div>
        </form>
    </x-form-modal>

    @if ($editing)
        <x-form-modal name="edit-order" title="Sipariş düzenle" :show="$showEditModal" maxWidth="2xl">
            <form method="POST" action="{{ route('orders.update', $editing) }}" class="space-y-4">
                @csrf
                @method('PUT')
                <input type="hidden" name="_modal" value="edit-order">
                <input type="hidden" name="_edit_id" value="{{ $editing->id }}">
                @include('orders._form', [
                    'idPrefix' => 'edit_',
                    'order' => $editing,
                    'customers' => $customers,
                    'seasons' => $seasons,
                    'statuses' => $statuses,
                ])
                <div class="flex gap-3 pt-2">
                    <x-primary-button>Güncelle</x-primary-button>
                    <button type="button" x-on:click="$dispatch('close')" class="inline-flex items-center px-4 py-2 text-sm text-gray-600 hover:underline">İptal</button>
                </div>
            </form>
        </x-form-modal>
    @endif
</x-app-layout>
