<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">Ürünler</h2>
            <button
                type="button"
                x-data=""
                x-on:click="$dispatch('open-modal', 'create-product')"
                class="inline-flex items-center px-4 py-2 bg-gray-800 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700"
            >
                Yeni ürün
            </button>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-4">
            @if (session('status'))
                <div class="bg-green-50 text-green-800 px-4 py-3 rounded-md text-sm">{{ session('status') }}</div>
            @endif

            <div class="bg-white shadow-sm sm:rounded-lg overflow-hidden">
                <table class="min-w-full divide-y divide-gray-200 text-sm">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="w-10 px-1 sm:px-2 py-3"></th>
                            <th class="px-4 py-3 text-left font-medium text-gray-500">Ad</th>
                            <th class="px-4 py-3 text-left font-medium text-gray-500">Birim</th>
                            <th class="px-4 py-3 text-left font-medium text-gray-500">Sıra</th>
                            <th class="px-4 py-3 text-left font-medium text-gray-500">Durum</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @forelse ($products as $product)
                            <tr>
                                <td class="px-1 sm:px-2 py-3">
                                    <x-row-menu>
                                        <x-row-menu-link :href="route('products.index', ['edit' => $product->id])">Düzenle</x-row-menu-link>
                                        <form action="{{ route('products.destroy', $product) }}" method="POST" onsubmit="return confirm('Silmek istediğinize emin misiniz?')">
                                            @csrf
                                            @method('DELETE')
                                            <x-row-menu-button variant="danger">Sil</x-row-menu-button>
                                        </form>
                                    </x-row-menu>
                                </td>
                                <td class="px-4 py-3 text-gray-900">{{ $product->name }}</td>
                                <td class="px-4 py-3 text-gray-600">{{ $product->unit }}</td>
                                <td class="px-4 py-3 text-gray-600">{{ $product->sort_order }}</td>
                                <td class="px-4 py-3">
                                    <span class="{{ $product->is_active ? 'text-green-700' : 'text-gray-400' }}">
                                        {{ $product->is_active ? 'Aktif' : 'Pasif' }}
                                    </span>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="px-4 py-8 text-center text-gray-500">Henüz ürün yok.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <x-form-modal name="create-product" title="Yeni ürün" :show="$showCreateModal">
        <form method="POST" action="{{ route('products.store') }}" class="space-y-4">
            @csrf
            <input type="hidden" name="_modal" value="create-product">
            @include('products._form', ['idPrefix' => 'create_', 'product' => null])
            <div class="flex gap-3 pt-2">
                <x-primary-button>Kaydet</x-primary-button>
                <button type="button" x-on:click="$dispatch('close')" class="inline-flex items-center px-4 py-2 text-sm text-gray-600 hover:underline">İptal</button>
            </div>
        </form>
    </x-form-modal>

    @if ($editing)
        <x-form-modal name="edit-product" title="Ürün düzenle" :show="$showEditModal">
            <form method="POST" action="{{ route('products.update', $editing) }}" class="space-y-4">
                @csrf
                @method('PUT')
                <input type="hidden" name="_modal" value="edit-product">
                <input type="hidden" name="_edit_id" value="{{ $editing->id }}">
                @include('products._form', ['idPrefix' => 'edit_', 'product' => $editing])
                <div class="flex gap-3 pt-2">
                    <x-primary-button>Güncelle</x-primary-button>
                    <button type="button" x-on:click="$dispatch('close')" class="inline-flex items-center px-4 py-2 text-sm text-gray-600 hover:underline">İptal</button>
                </div>
            </form>
        </x-form-modal>
    @endif
</x-app-layout>
