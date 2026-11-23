<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between gap-4 flex-wrap">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">Müşteriler</h2>
            <a href="{{ route('customers.create') }}" class="inline-flex items-center px-4 py-2 bg-gray-800 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700">Yeni müşteri</a>
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

            <div class="bg-white shadow-sm sm:rounded-lg overflow-hidden">
                <table class="min-w-full divide-y divide-gray-200 text-sm">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-4 py-3 text-left font-medium text-gray-500">İsim</th>
                            <th class="px-4 py-3 text-left font-medium text-gray-500">Telefon</th>
                            <th class="px-4 py-3 text-left font-medium text-gray-500">Şehir</th>
                            <th class="px-4 py-3 text-left font-medium text-gray-500">Adres</th>
                            <th class="px-4 py-3 text-right font-medium text-gray-500"></th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @forelse ($customers as $customer)
                            <tr>
                                <td class="px-4 py-3 text-gray-900 font-medium">
                                    <a href="{{ route('customers.show', $customer) }}" class="hover:underline">{{ $customer->name }}</a>
                                </td>
                                <td class="px-4 py-3 text-gray-600">{{ $customer->phone }}</td>
                                <td class="px-4 py-3 text-gray-600">{{ $customer->city }}</td>
                                <td class="px-4 py-3 text-gray-600 max-w-xs truncate" title="{{ $customer->address }}">{{ $customer->address }}</td>
                                <td class="px-4 py-3 text-right space-x-3 whitespace-nowrap">
                                    <a href="{{ route('customers.show', $customer) }}" class="text-gray-700 hover:underline">Detay</a>
                                    <a href="{{ route('orders.create', ['customer_id' => $customer->id]) }}" class="text-gray-700 hover:underline">Sipariş</a>
                                    <a href="{{ route('customers.edit', $customer) }}" class="text-indigo-600 hover:underline">Düzenle</a>
                                    <form action="{{ route('customers.destroy', $customer) }}" method="POST" class="inline" onsubmit="return confirm('Silmek istediğinize emin misiniz?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="text-red-600 hover:underline">Sil</button>
                                    </form>
                                </td>
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
</x-app-layout>
