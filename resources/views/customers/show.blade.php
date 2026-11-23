<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between gap-4 flex-wrap">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">{{ $customer->name }}</h2>
            <div class="flex gap-2">
                <a href="{{ route('orders.create', ['customer_id' => $customer->id]) }}" class="inline-flex items-center px-4 py-2 bg-gray-800 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700">Sipariş ekle</a>
                <button
                    type="button"
                    x-data=""
                    x-on:click="$dispatch('open-modal', 'edit-customer')"
                    class="inline-flex items-center px-4 py-2 bg-white border border-gray-300 rounded-md font-semibold text-xs text-gray-700 uppercase tracking-widest hover:bg-gray-50"
                >
                    Düzenle
                </button>
            </div>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
            @if (session('status'))
                <div class="bg-green-50 text-green-800 px-4 py-3 rounded-md text-sm">{{ session('status') }}</div>
            @endif

            <div class="bg-white shadow-sm sm:rounded-lg p-6">
                <h3 class="text-sm font-semibold uppercase tracking-wide text-gray-500 mb-4">Müşteri bilgileri</h3>
                <dl class="grid grid-cols-1 sm:grid-cols-2 gap-4 text-sm">
                    <div>
                        <dt class="text-gray-500">İsim</dt>
                        <dd class="mt-1 text-gray-900 font-medium">{{ $customer->name }}</dd>
                    </div>
                    <div>
                        <dt class="text-gray-500">Telefon</dt>
                        <dd class="mt-1 text-gray-900">
                            @if ($customer->phone)
                                <a href="tel:{{ preg_replace('/\s+/', '', $customer->phone) }}" class="text-indigo-600 hover:underline">{{ $customer->phone }}</a>
                            @else
                                —
                            @endif
                        </dd>
                    </div>
                    <div>
                        <dt class="text-gray-500">Şehir</dt>
                        <dd class="mt-1 text-gray-900">{{ $customer->city ?: '—' }}</dd>
                    </div>
                    <div class="sm:col-span-2">
                        <dt class="text-gray-500">Adres</dt>
                        <dd class="mt-1 text-gray-900 whitespace-pre-line">{{ $customer->address ?: '—' }}</dd>
                    </div>
                    @if ($customer->notes)
                        <div class="sm:col-span-2">
                            <dt class="text-gray-500">Not</dt>
                            <dd class="mt-1 text-gray-900 whitespace-pre-line">{{ $customer->notes }}</dd>
                        </div>
                    @endif
                </dl>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                <div class="bg-white shadow-sm sm:rounded-lg p-5">
                    <div class="text-xs uppercase tracking-wide text-gray-500">Toplam ceviz</div>
                    <div class="mt-2 text-2xl font-semibold text-gray-900">{{ number_format($cevizTotal['kg'], 2, ',', '.') }} kg</div>
                    <div class="mt-1 text-sm text-gray-600">{{ number_format($cevizTotal['revenue'], 2, ',', '.') }} ₺ · {{ $cevizTotal['order_count'] }} sipariş</div>
                </div>
                <div class="bg-white shadow-sm sm:rounded-lg p-5">
                    <div class="text-xs uppercase tracking-wide text-gray-500">Toplam badem</div>
                    <div class="mt-2 text-2xl font-semibold text-gray-900">{{ number_format($bademTotal['kg'], 2, ',', '.') }} kg</div>
                    <div class="mt-1 text-sm text-gray-600">{{ number_format($bademTotal['revenue'], 2, ',', '.') }} ₺ · {{ $bademTotal['order_count'] }} sipariş</div>
                </div>
                <div class="bg-white shadow-sm sm:rounded-lg p-5">
                    <div class="text-xs uppercase tracking-wide text-gray-500">Genel toplam</div>
                    <div class="mt-2 text-2xl font-semibold text-gray-900">{{ number_format($grandKg, 2, ',', '.') }} kg</div>
                    <div class="mt-1 text-sm text-gray-600">{{ number_format($grandRevenue, 2, ',', '.') }} ₺ · {{ $orders->count() }} sipariş</div>
                </div>
            </div>

            <div class="bg-white shadow-sm sm:rounded-lg overflow-x-auto">
                <div class="px-4 py-3 border-b border-gray-100">
                    <h3 class="text-sm font-semibold uppercase tracking-wide text-gray-500">Siparişler</h3>
                </div>
                <table class="min-w-full divide-y divide-gray-200 text-sm">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="w-10 px-1 sm:px-2 py-3"></th>
                            <th class="px-4 py-3 text-left font-medium text-gray-500">Sezon</th>
                            <th class="px-4 py-3 text-left font-medium text-gray-500">Ürün</th>
                            <th class="px-4 py-3 text-right font-medium text-gray-500">Kg</th>
                            <th class="px-4 py-3 text-right font-medium text-gray-500">Birim fiyat</th>
                            <th class="px-4 py-3 text-right font-medium text-gray-500">Tutar ₺</th>
                            <th class="px-4 py-3 text-left font-medium text-gray-500">Durum</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @forelse ($orders as $order)
                            <tr>
                                <td class="px-1 sm:px-2 py-3">
                                    <x-row-menu>
                                        <x-row-menu-link :href="route('orders.edit', $order)">Düzenle</x-row-menu-link>
                                        <form action="{{ route('orders.destroy', $order) }}" method="POST" onsubmit="return confirm('Silmek istediğinize emin misiniz?')">
                                            @csrf
                                            @method('DELETE')
                                            <x-row-menu-button variant="danger">Sil</x-row-menu-button>
                                        </form>
                                    </x-row-menu>
                                </td>
                                <td class="px-4 py-3 text-gray-900">{{ $order->season?->year }}</td>
                                <td class="px-4 py-3 text-gray-700">{{ $order->season?->product?->name }}</td>
                                <td class="px-4 py-3 text-right text-gray-700">{{ number_format((float) $order->quantity_kg, 2, ',', '.') }}</td>
                                <td class="px-4 py-3 text-right text-gray-600">{{ number_format((float) ($order->season?->unit_price ?? 0), 2, ',', '.') }}</td>
                                <td class="px-4 py-3 text-right text-gray-700">{{ number_format($order->revenueTry(), 2, ',', '.') }}</td>
                                <td class="px-4 py-3">
                                    <span class="inline-flex items-center rounded-full px-2 py-0.5 text-xs font-medium {{ $order->status->badgeClasses() }}">
                                        {{ $order->status->shortLabel() }}
                                    </span>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="px-4 py-8 text-center text-gray-500">Bu müşteriye ait sipariş yok.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div>
                <a href="{{ route('customers.index') }}" class="text-sm text-gray-600 hover:underline">← Müşteri listesi</a>
            </div>
        </div>
    </div>

    <x-form-modal name="edit-customer" title="Müşteri düzenle" :show="$showEditModal">
        <form method="POST" action="{{ route('customers.update', $customer) }}" class="space-y-4">
            @csrf
            @method('PUT')
            <input type="hidden" name="_modal" value="edit-customer">
            <input type="hidden" name="_edit_id" value="{{ $customer->id }}">
            @include('customers._form', ['idPrefix' => 'show_edit_', 'customer' => $customer])
            <div class="flex gap-3 pt-2">
                <x-primary-button>Güncelle</x-primary-button>
                <button type="button" x-on:click="$dispatch('close')" class="inline-flex items-center px-4 py-2 text-sm text-gray-600 hover:underline">İptal</button>
            </div>
        </form>
    </x-form-modal>
</x-app-layout>
