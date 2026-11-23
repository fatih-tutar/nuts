<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between gap-4 flex-wrap">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">Müşteriler</h2>
            <button
                type="button"
                x-data=""
                x-on:click="$dispatch('open-modal', 'create-customer')"
                class="inline-flex items-center px-4 py-2 bg-gray-800 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700"
            >
                Yeni müşteri
            </button>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-4">
            @if (session('status'))
                <div class="bg-green-50 text-green-800 px-4 py-3 rounded-md text-sm">{{ session('status') }}</div>
            @endif

            <form method="GET" class="flex gap-2">
                <x-text-input name="q" class="w-full max-w-md" placeholder="İsim, telefon veya şehir ara..." :value="$q" />
                <x-primary-button>Ara</x-primary-button>
            </form>

            <div class="bg-white shadow-sm sm:rounded-lg overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200 text-sm">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="w-10 px-1 sm:px-2 py-3"></th>
                            <th class="px-4 py-3 text-left font-medium text-gray-500">İsim</th>
                            <th class="px-4 py-3 text-left font-medium text-gray-500">Telefon</th>
                            <th class="hidden md:table-cell px-4 py-3 text-left font-medium text-gray-500">Şehir</th>
                            <th class="hidden md:table-cell px-4 py-3 text-left font-medium text-gray-500">Adres</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @forelse ($customers as $customer)
                            <tr>
                                <td class="px-1 sm:px-2 py-3">
                                    <x-row-menu>
                                        <x-row-menu-link :href="route('orders.create', ['customer_id' => $customer->id])">
                                            Sipariş
                                        </x-row-menu-link>
                                        <x-row-menu-link :href="route('customers.index', ['edit' => $customer->id, 'q' => $q])">
                                            Düzenle
                                        </x-row-menu-link>
                                        <form action="{{ route('customers.destroy', $customer) }}" method="POST" onsubmit="return confirm('Silmek istediğinize emin misiniz?')">
                                            @csrf
                                            @method('DELETE')
                                            <x-row-menu-button variant="danger">Sil</x-row-menu-button>
                                        </form>
                                    </x-row-menu>
                                </td>
                                <td class="px-4 py-3 text-gray-900 font-medium">
                                    <a href="{{ route('customers.show', $customer) }}" class="hover:underline">{{ $customer->name }}</a>
                                </td>
                                <td class="px-4 py-3 text-gray-600">
                                    @if ($customer->phone)
                                        <a href="tel:{{ preg_replace('/\s+/', '', $customer->phone) }}" class="hover:underline text-indigo-600">{{ $customer->phone }}</a>
                                    @else
                                        —
                                    @endif
                                </td>
                                <td class="hidden md:table-cell px-4 py-3 text-gray-600">{{ $customer->city ?: '—' }}</td>
                                <td class="hidden md:table-cell px-4 py-3 text-gray-600 max-w-xs truncate" title="{{ $customer->address }}">{{ $customer->address ?: '—' }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="px-4 py-8 text-center text-gray-500">Müşteri bulunamadı.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div>{{ $customers->links() }}</div>
        </div>
    </div>

    <x-form-modal name="create-customer" title="Yeni müşteri" :show="$showCreateModal">
        <form method="POST" action="{{ route('customers.store') }}" class="space-y-4">
            @csrf
            <input type="hidden" name="_modal" value="create-customer">
            @include('customers._form', ['idPrefix' => 'create_', 'customer' => null])
            <div class="flex gap-3 pt-2">
                <x-primary-button>Kaydet</x-primary-button>
                <button type="button" x-on:click="$dispatch('close')" class="inline-flex items-center px-4 py-2 text-sm text-gray-600 hover:underline">İptal</button>
            </div>
        </form>
    </x-form-modal>

    @if ($editing)
        <x-form-modal name="edit-customer" title="Müşteri düzenle" :show="$showEditModal">
            <form method="POST" action="{{ route('customers.update', $editing) }}" class="space-y-4">
                @csrf
                @method('PUT')
                <input type="hidden" name="_modal" value="edit-customer">
                <input type="hidden" name="_edit_id" value="{{ $editing->id }}">
                @include('customers._form', ['idPrefix' => 'edit_', 'customer' => $editing])
                <div class="flex gap-3 pt-2">
                    <x-primary-button>Güncelle</x-primary-button>
                    <button type="button" x-on:click="$dispatch('close')" class="inline-flex items-center px-4 py-2 text-sm text-gray-600 hover:underline">İptal</button>
                </div>
            </form>
        </x-form-modal>
    @endif
</x-app-layout>
