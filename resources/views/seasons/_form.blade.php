<div>
    <x-input-label for="product_id" value="Ürün" />
    <select id="product_id" name="product_id" class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm" required>
        <option value="">Seçin</option>
        @foreach ($products as $product)
            <option value="{{ $product->id }}" @selected(old('product_id', $season->product_id ?? '') == $product->id)>{{ $product->name }}</option>
        @endforeach
    </select>
    <x-input-error :messages="$errors->get('product_id')" class="mt-2" />
</div>

<div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
    <div>
        <x-input-label for="year" value="Yıl" />
        <x-text-input id="year" name="year" type="number" min="2000" max="2100" class="mt-1 block w-full" :value="old('year', $season->year ?? now()->year)" required />
        <x-input-error :messages="$errors->get('year')" class="mt-2" />
    </div>
    <div>
        <x-input-label for="label" value="Etiket (opsiyonel)" />
        <x-text-input id="label" name="label" class="mt-1 block w-full" :value="old('label', $season->label ?? '')" />
        <x-input-error :messages="$errors->get('label')" class="mt-2" />
    </div>
</div>

<div>
    <x-input-label for="order_start_date" value="Sipariş alma başlangıç" />
    <x-text-input id="order_start_date" name="order_start_date" type="date" class="mt-1 block w-full" :value="old('order_start_date', isset($season) && $season->order_start_date ? $season->order_start_date->format('Y-m-d') : '')" />
    <x-input-error :messages="$errors->get('order_start_date')" class="mt-2" />
</div>

<div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
    <div>
        <x-input-label for="unit_price" value="Birim fiyat (₺/kg)" />
        <x-text-input id="unit_price" name="unit_price" type="number" step="0.01" min="0" class="mt-1 block w-full" :value="old('unit_price', $season->unit_price ?? '')" required />
        <x-input-error :messages="$errors->get('unit_price')" class="mt-2" />
    </div>
    <div>
        <x-input-label for="gold_price" value="Altın fiyatı" />
        <x-text-input id="gold_price" name="gold_price" type="number" step="0.01" min="0" class="mt-1 block w-full" :value="old('gold_price', $season->gold_price ?? '')" />
        <x-input-error :messages="$errors->get('gold_price')" class="mt-2" />
    </div>
    <div>
        <x-input-label for="usd_rate" value="Dolar kuru" />
        <x-text-input id="usd_rate" name="usd_rate" type="number" step="0.0001" min="0" class="mt-1 block w-full" :value="old('usd_rate', $season->usd_rate ?? '')" />
        <x-input-error :messages="$errors->get('usd_rate')" class="mt-2" />
    </div>
</div>
