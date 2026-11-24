<div class="bg-white shadow-sm sm:rounded-lg overflow-hidden">
    <div class="px-6 py-4 border-b border-gray-100 flex flex-wrap items-center justify-between gap-2">
        <h3 class="text-sm font-semibold uppercase tracking-wide text-gray-500">{{ $title }}</h3>
        <p class="text-xs text-gray-400">
            En yüksek cirolar
            <span class="inline-flex items-center rounded-md bg-amber-100 px-1.5 py-0.5 font-medium text-amber-900">vurgulu</span>
        </p>
    </div>
    <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-200 text-sm">
            <thead class="bg-gray-50">
                <tr class="whitespace-nowrap">
                    <th class="px-3 py-3 text-left text-xs font-medium text-gray-500">Sezon</th>
                    <th class="px-3 py-3 text-right text-xs font-medium text-gray-500">Kg</th>
                    <th class="px-3 py-3 text-right text-xs font-medium text-gray-500">Sipariş</th>
                    <th class="px-3 py-3 text-right text-xs font-medium text-gray-500">Ort. kg</th>
                    <th class="px-3 py-3 text-right text-xs font-medium text-gray-500">Birim ₺</th>
                    <th class="px-3 py-3 text-right text-xs font-medium text-gray-500">$/₺</th>
                    <th class="px-3 py-3 text-right text-xs font-medium text-gray-500">Altın</th>
                    <th class="px-3 py-3 text-right text-xs font-medium text-gray-500">Ciro ₺</th>
                    <th class="px-3 py-3 text-right text-xs font-medium text-gray-500">Ciro $</th>
                    <th class="px-3 py-3 text-right text-xs font-medium text-gray-500">Ciro altın</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                @forelse ($rows as $row)
                    @php
                        $isPeakTry = ($peaks['revenue_try'] ?? null) !== null
                            && (float) $row['revenue_try'] === (float) $peaks['revenue_try']
                            && (float) $row['revenue_try'] > 0;
                        $isPeakUsd = ($peaks['revenue_usd'] ?? null) !== null
                            && $row['revenue_usd'] !== null
                            && (float) $row['revenue_usd'] === (float) $peaks['revenue_usd']
                            && (float) $row['revenue_usd'] > 0;
                        $isPeakGold = ($peaks['revenue_gold'] ?? null) !== null
                            && $row['revenue_gold'] !== null
                            && (float) $row['revenue_gold'] === (float) $peaks['revenue_gold']
                            && (float) $row['revenue_gold'] > 0;
                        $isPeakRow = $isPeakTry || $isPeakUsd || $isPeakGold;
                    @endphp
                    <tr
                        class="cursor-pointer {{ $isPeakRow ? 'bg-amber-50/70 hover:bg-amber-50' : 'hover:bg-gray-50' }}"
                        x-on:click="showDetail(@js($row['key']))"
                    >
                        <td class="px-4 py-3 text-gray-900 font-medium whitespace-nowrap">
                            <span class="inline-flex items-center gap-1.5">
                                {{ $row['year'] }}
                                @if ($isPeakTry)
                                    <span class="inline-flex items-center rounded-full bg-amber-500 px-1.5 py-0.5 text-[10px] font-semibold uppercase tracking-wide text-white">Zirve</span>
                                @endif
                            </span>
                        </td>
                        <td class="px-4 py-3 text-right text-gray-700 tabular-nums">{{ number_format($row['total_kg'], 2, ',', '.') }}</td>
                        <td class="px-4 py-3 text-right text-gray-700 tabular-nums">{{ number_format($row['order_count'], 0, ',', '.') }}</td>
                        <td class="px-4 py-3 text-right text-gray-600 tabular-nums">
                            {{ $row['avg_order_kg'] !== null ? number_format($row['avg_order_kg'], 2, ',', '.') : '—' }}
                        </td>
                        <td class="px-4 py-3 text-right text-gray-600 tabular-nums">{{ number_format($row['unit_price'], 2, ',', '.') }}</td>
                        <td class="px-4 py-3 text-right text-gray-600 tabular-nums">
                            {{ $row['usd_rate'] !== null ? number_format($row['usd_rate'], 2, ',', '.') : '—' }}
                        </td>
                        <td class="px-4 py-3 text-right text-gray-600 tabular-nums">
                            {{ $row['gold_price'] !== null ? number_format($row['gold_price'], 2, ',', '.') : '—' }}
                        </td>
                        <td class="px-4 py-3 text-right tabular-nums {{ $isPeakTry ? 'bg-amber-100 font-bold text-amber-950 ring-1 ring-inset ring-amber-300' : 'font-medium text-gray-900' }}">
                            {{ number_format($row['revenue_try'], 2, ',', '.') }}
                        </td>
                        <td class="px-4 py-3 text-right tabular-nums {{ $isPeakUsd ? 'bg-emerald-100 font-bold text-emerald-950 ring-1 ring-inset ring-emerald-300' : 'text-gray-700' }}">
                            {{ $row['revenue_usd'] !== null ? number_format($row['revenue_usd'], 2, ',', '.') : '—' }}
                        </td>
                        <td class="px-4 py-3 text-right tabular-nums {{ $isPeakGold ? 'bg-yellow-100 font-bold text-yellow-950 ring-1 ring-inset ring-yellow-300' : 'text-gray-700' }}">
                            {{ $row['revenue_gold'] !== null ? number_format($row['revenue_gold'], 2, ',', '.') : '—' }}
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="10" class="px-4 py-8 text-center text-gray-500">Henüz sezon verisi yok.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
