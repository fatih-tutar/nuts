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

            @php
                $activeStatus = (string) ($filters['status'] ?? '');
                $seasonQuery = ['season_id' => $filters['season_id'] ?? ''];
            @endphp

            <div class="space-y-3">
                <form method="GET" class="flex items-center gap-3 max-w-xl">
                    @if ($activeStatus !== '')
                        <input type="hidden" name="status" value="{{ $activeStatus }}">
                    @endif
                    <x-input-label for="filter_season_id" value="Sezon" class="!mb-0 shrink-0" />
                    <select id="filter_season_id" name="season_id" onchange="this.form.submit()" class="block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm text-sm">
                        <option value="">Tümü</option>
                        @foreach ($seasons as $season)
                            <option value="{{ $season->id }}" @selected((string) ($filters['season_id'] ?? '') === (string) $season->id)>{{ $season->displayName() }}</option>
                        @endforeach
                    </select>
                </form>

                <div class="grid grid-cols-[0.7fr_1.15fr_1.15fr_1.45fr_1.15fr] gap-0.5 rounded-lg bg-gray-100 p-0.5 sm:p-1" role="tablist" aria-label="Sipariş durumu">
                    <a
                        href="{{ route('orders.index', $seasonQuery) }}"
                        role="tab"
                        aria-selected="{{ $activeStatus === '' ? 'true' : 'false' }}"
                        class="min-w-0 rounded-md px-0.5 py-2 text-center text-[10px] sm:text-xs font-semibold whitespace-nowrap border transition {{ $activeStatus === '' ? 'border-gray-800 bg-gray-800 text-white shadow-sm' : 'border-transparent text-gray-500 hover:bg-white hover:text-gray-800' }}"
                    >
                        Tümü
                    </a>
                    @foreach (\App\Enums\OrderStatus::cases() as $statusCase)
                        <a
                            href="{{ route('orders.index', array_merge($seasonQuery, ['status' => $statusCase->value])) }}"
                            role="tab"
                            aria-selected="{{ $activeStatus === $statusCase->value ? 'true' : 'false' }}"
                            class="min-w-0 rounded-md px-0.5 py-2 text-center text-[10px] sm:text-xs font-semibold whitespace-nowrap border transition {{ $statusCase->tabClasses($activeStatus === $statusCase->value) }}"
                        >
                            {{ $statusCase->shortLabel() }}
                        </a>
                    @endforeach
                </div>
            </div>

            <div class="bg-white shadow-sm sm:rounded-lg overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200 text-sm">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="w-10 px-1 sm:px-2 py-3"></th>
                            <th class="px-3 sm:px-4 py-3 text-left font-medium text-gray-500">Müşteri</th>
                            <th class="hidden md:table-cell px-4 py-3 text-left font-medium text-gray-500">Sezon</th>
                            <th class="px-2 sm:px-4 py-3 text-right font-medium text-gray-500">Kg</th>
                            <th class="px-2 sm:px-4 py-3 text-right font-medium text-gray-500">Tutar ₺</th>
                            <th class="hidden md:table-cell px-4 py-3 text-left font-medium text-gray-500">Durum</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @forelse ($orders as $order)
                            <tr>
                                <td class="px-1 sm:px-2 py-3">
                                    <x-row-menu>
                                        <x-row-menu-link :href="route('orders.index', array_filter(['edit' => $order->id, 'season_id' => $filters['season_id'] ?? $order->season_id, 'status' => $activeStatus !== '' ? $activeStatus : null]))">
                                            Düzenle
                                        </x-row-menu-link>
                                        <form action="{{ route('orders.destroy', $order) }}" method="POST" onsubmit="return confirm('Silmek istediğinize emin misiniz?')">
                                            @csrf
                                            @method('DELETE')
                                            <x-row-menu-button variant="danger">Sil</x-row-menu-button>
                                        </form>
                                    </x-row-menu>
                                </td>
                                <td class="px-3 sm:px-4 py-3 text-gray-900 font-medium">
                                    @if ($order->customer)
                                        <a href="{{ route('customers.show', $order->customer) }}" class="hover:underline break-words">{{ $order->customer->name }}</a>
                                    @endif
                                </td>
                                <td class="hidden md:table-cell px-4 py-3 text-gray-600 whitespace-nowrap">{{ $order->season?->displayName() }}</td>
                                <td class="px-2 sm:px-4 py-3 text-right text-gray-700 whitespace-nowrap">{{ number_format((float) $order->quantity_kg, 2, ',', '.') }}</td>
                                <td class="px-2 sm:px-4 py-3 text-right text-gray-700 whitespace-nowrap">{{ number_format($order->revenueTry(), 2, ',', '.') }}</td>
                                <td class="hidden md:table-cell px-4 py-3">
                                    <span class="inline-flex items-center rounded-full px-2 py-0.5 text-xs font-medium {{ $order->status->badgeClasses() }}">
                                        {{ $order->status->shortLabel() }}
                                    </span>
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

            @if ($orders->total() > 0)
                <p class="text-xs text-gray-500 px-1">
                    Toplam {{ number_format($orders->total(), 0, ',', '.') }} kişi
                </p>
            @endif

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
