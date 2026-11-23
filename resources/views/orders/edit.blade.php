<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">Sipariş düzenle</h2>
    </x-slot>

    <div class="py-8">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white shadow-sm sm:rounded-lg p-6">
                <form method="POST" action="{{ route('orders.update', $order) }}" class="space-y-4">
                    @csrf
                    @method('PUT')
                    @include('orders._form')
                    <div class="flex gap-3">
                        <x-primary-button>Güncelle</x-primary-button>
                        <a href="{{ route('orders.index') }}" class="inline-flex items-center px-4 py-2 text-sm text-gray-600 hover:underline">İptal</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
