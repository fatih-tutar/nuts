<div>
    <x-input-label for="name" value="Ad" />
    <x-text-input id="name" name="name" class="mt-1 block w-full" :value="old('name', $product->name ?? '')" required />
    <x-input-error :messages="$errors->get('name')" class="mt-2" />
</div>

<div>
    <x-input-label for="unit" value="Birim" />
    <x-text-input id="unit" name="unit" class="mt-1 block w-full" :value="old('unit', $product->unit ?? 'kg')" required />
    <x-input-error :messages="$errors->get('unit')" class="mt-2" />
</div>

<div>
    <x-input-label for="sort_order" value="Sıra" />
    <x-text-input id="sort_order" name="sort_order" type="number" min="0" class="mt-1 block w-full" :value="old('sort_order', $product->sort_order ?? 0)" />
    <x-input-error :messages="$errors->get('sort_order')" class="mt-2" />
</div>

<div class="flex items-center gap-2">
    <input id="is_active" name="is_active" type="checkbox" value="1" class="rounded border-gray-300 text-indigo-600 shadow-sm focus:ring-indigo-500"
        @checked(old('is_active', $product->is_active ?? true))>
    <x-input-label for="is_active" value="Aktif" class="!mb-0" />
</div>
