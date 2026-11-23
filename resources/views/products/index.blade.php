<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">Ürünler</h2>
            <a href="{{ route('products.create') }}" class="inline-flex items-center px-4 py-2 bg-gray-800 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700">Yeni ürün</a>
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
                            <th class="px-4 py-3 text-left font-medium text-gray-500">Ad</th>
                            <th class="px-4 py-3 text-left font-medium text-gray-500">Birim</th>
                            <th class="px-4 py-3 text-left font-medium text-gray-500">Sıra</th>
                            <th class="px-4 py-3 text-left font-medium text-gray-500">Durum</th>
                            <th class="px-4 py-3 text-right font-medium text-gray-500"></th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @forelse ($products as $product)
                            <tr>
                                <td class="px-4 py-3 text-gray-900">{{ $product->name }}</td>
                                <td class="px-4 py-3 text-gray-600">{{ $product->unit }}</td>
                                <td class="px-4 py-3 text-gray-600">{{ $product->sort_order }}</td>
                                <td class="px-4 py-3">
                                    <span class="{{ $product->is_active ? 'text-green-700' : 'text-gray-400' }}">
                                        {{ $product->is_active ? 'Aktif' : 'Pasif' }}
                                    </span>
                                </td>
                                <td class="px-4 py-3 text-right space-x-3">
                                    <a href="{{ route('products.edit', $product) }}" class="text-indigo-600 hover:underline">Düzenle</a>
                                    <form action="{{ route('products.destroy', $product) }}" method="POST" class="inline" onsubmit="return confirm('Silmek istediğinize emin misiniz?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="text-red-600 hover:underline">Sil</button>
                                    </form>
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
</x-app-layout>
