@php
    $idPrefix = $idPrefix ?? '';
    $customer = $customer ?? null;
@endphp

<div>
    <x-input-label for="{{ $idPrefix }}name" value="İsim" />
    <x-text-input id="{{ $idPrefix }}name" name="name" class="mt-1 block w-full" :value="old('name', $customer->name ?? '')" required />
    <x-input-error :messages="$errors->get('name')" class="mt-2" />
</div>

<div>
    <x-input-label for="{{ $idPrefix }}phone" value="Telefon" />
    <x-text-input id="{{ $idPrefix }}phone" name="phone" class="mt-1 block w-full" :value="old('phone', $customer->phone ?? '')" />
    <x-input-error :messages="$errors->get('phone')" class="mt-2" />
</div>

<div>
    <x-input-label for="{{ $idPrefix }}city" value="Şehir" />
    <x-text-input id="{{ $idPrefix }}city" name="city" class="mt-1 block w-full" :value="old('city', $customer->city ?? '')" />
    <x-input-error :messages="$errors->get('city')" class="mt-2" />
</div>

<div>
    <x-input-label for="{{ $idPrefix }}address" value="Adres" />
    <textarea id="{{ $idPrefix }}address" name="address" rows="3" class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm">{{ old('address', $customer->address ?? '') }}</textarea>
    <x-input-error :messages="$errors->get('address')" class="mt-2" />
</div>

<div>
    <x-input-label for="{{ $idPrefix }}notes" value="Not" />
    <textarea id="{{ $idPrefix }}notes" name="notes" rows="2" class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm">{{ old('notes', $customer->notes ?? '') }}</textarea>
    <x-input-error :messages="$errors->get('notes')" class="mt-2" />
</div>
