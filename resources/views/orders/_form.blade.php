@php
    $idPrefix = $idPrefix ?? '';
    $order = $order ?? null;
    $selectedCustomer = old('customer_id', isset($order) ? $order->customer_id : ($selectedCustomerId ?? ''));
    $selectedSeason = old('season_id', isset($order) ? $order->season_id : ($selectedSeasonId ?? ''));
    $selectedStatus = old('status', isset($order) ? $order->status->value : 'ordered');
@endphp

<div>
    <x-input-label for="{{ $idPrefix }}customer_id" value="Müşteri" />
    <select id="{{ $idPrefix }}customer_id" name="customer_id" class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm" required>
        <option value="">Seçin</option>
        @foreach ($customers as $customer)
            <option value="{{ $customer->id }}" @selected($selectedCustomer == $customer->id)>{{ $customer->name }}@if($customer->phone) — {{ $customer->phone }}@endif</option>
        @endforeach
    </select>
    <x-input-error :messages="$errors->get('customer_id')" class="mt-2" />
</div>

<div>
    <x-input-label for="{{ $idPrefix }}season_id" value="Sezon / ürün" />
    <select id="{{ $idPrefix }}season_id" name="season_id" class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm" required>
        <option value="">Seçin</option>
        @foreach ($seasons as $season)
            <option value="{{ $season->id }}" @selected($selectedSeason == $season->id)>
                {{ $season->displayName() }} — {{ number_format((float) $season->unit_price, 2, ',', '.') }} ₺/kg
            </option>
        @endforeach
    </select>
    <x-input-error :messages="$errors->get('season_id')" class="mt-2" />
</div>

<div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
    <div>
        <x-input-label for="{{ $idPrefix }}quantity_kg" value="Miktar (kg)" />
        <x-text-input id="{{ $idPrefix }}quantity_kg" name="quantity_kg" type="number" step="0.01" min="0.01" class="mt-1 block w-full" :value="old('quantity_kg', $order->quantity_kg ?? '')" required />
        <x-input-error :messages="$errors->get('quantity_kg')" class="mt-2" />
    </div>
    <div>
        <x-input-label for="{{ $idPrefix }}status" value="Durum" />
        <select id="{{ $idPrefix }}status" name="status" class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm" required>
            @foreach ($statuses as $value => $label)
                <option value="{{ $value }}" @selected($selectedStatus === $value)>{{ $label }}</option>
            @endforeach
        </select>
        <x-input-error :messages="$errors->get('status')" class="mt-2" />
    </div>
</div>

<div>
    <x-input-label for="{{ $idPrefix }}ordered_on" value="Sipariş tarihi" />
    <x-text-input id="{{ $idPrefix }}ordered_on" name="ordered_on" type="date" class="mt-1 block w-full" :value="old('ordered_on', isset($order) && $order->ordered_on ? $order->ordered_on->format('Y-m-d') : now()->format('Y-m-d'))" />
    <x-input-error :messages="$errors->get('ordered_on')" class="mt-2" />
</div>

<div>
    <x-input-label for="{{ $idPrefix }}notes" value="Not" />
    <textarea id="{{ $idPrefix }}notes" name="notes" rows="2" class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm">{{ old('notes', $order->notes ?? '') }}</textarea>
    <x-input-error :messages="$errors->get('notes')" class="mt-2" />
</div>
