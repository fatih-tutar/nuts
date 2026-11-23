<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">Özet</h2>
    </x-slot>

    <div
        class="py-8"
        x-data="{
            open: false,
            detail: null,
            details: {{ Js::from($seasonDetails) }},
            showDetail(key) {
                this.detail = this.details[key] ?? null;
                this.open = !!this.detail;
            },
            formatNumber(value, digits = 2) {
                if (value === null || value === undefined) return '—';
                return new Intl.NumberFormat('tr-TR', {
                    minimumFractionDigits: digits,
                    maximumFractionDigits: digits,
                }).format(value);
            },
            formatInt(value) {
                if (value === null || value === undefined) return '—';
                return new Intl.NumberFormat('tr-TR').format(value);
            }
        }"
        x-on:open-season-detail.window="showDetail($event.detail)"
        x-on:keydown.escape.window="open = false"
    >
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
            <div class="grid grid-cols-2 md:grid-cols-5 gap-4">
                <div class="bg-white shadow-sm sm:rounded-lg p-4">
                    <div class="text-xs uppercase tracking-wide text-gray-500">Ürün</div>
                    <div class="mt-1 text-2xl font-semibold text-gray-900">{{ $stats['products'] }}</div>
                </div>
                <div class="bg-white shadow-sm sm:rounded-lg p-4">
                    <div class="text-xs uppercase tracking-wide text-gray-500">Müşteri</div>
                    <div class="mt-1 text-2xl font-semibold text-gray-900">{{ $stats['customers'] }}</div>
                </div>
                <div class="bg-white shadow-sm sm:rounded-lg p-4">
                    <div class="text-xs uppercase tracking-wide text-gray-500">Sezon</div>
                    <div class="mt-1 text-2xl font-semibold text-gray-900">{{ $stats['seasons'] }}</div>
                </div>
                <div class="bg-white shadow-sm sm:rounded-lg p-4">
                    <div class="text-xs uppercase tracking-wide text-gray-500">Sipariş</div>
                    <div class="mt-1 text-2xl font-semibold text-gray-900">{{ $stats['orders'] }}</div>
                </div>
                <div class="bg-white shadow-sm sm:rounded-lg p-4">
                    <div class="text-xs uppercase tracking-wide text-gray-500">Sipariş verdi</div>
                    <div class="mt-1 text-2xl font-semibold text-amber-700">{{ $stats['open_orders'] }}</div>
                </div>
            </div>

            <div class="bg-white shadow-sm sm:rounded-lg p-6">
                <h3 class="text-sm font-semibold uppercase tracking-wide text-gray-500 mb-1">Yıllara göre satış (kg)</h3>
                <p class="text-xs text-gray-400 mb-4">Bir çubuğa tıklayarak sezon detayını açın.</p>
                @if (count($chart['years']) === 0)
                    <p class="text-sm text-gray-500">Henüz grafikte gösterilecek sipariş yok.</p>
                @else
                    <div class="relative h-96">
                        <canvas id="salesByYearChart"></canvas>
                    </div>
                @endif
            </div>

            @php
                $seasonRows = collect($seasonDetails)
                    ->sortBy([
                        ['year', 'desc'],
                        ['slug', 'asc'],
                    ])
                    ->values();

                $peakByProduct = $seasonRows
                    ->groupBy('slug')
                    ->map(function ($rows) {
                        return [
                            'revenue_try' => $rows->max('revenue_try'),
                            'revenue_usd' => $rows->max('revenue_usd'),
                            'revenue_gold' => $rows->max('revenue_gold'),
                        ];
                    });
            @endphp

            <div class="bg-white shadow-sm sm:rounded-lg overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-100 flex flex-wrap items-center justify-between gap-2">
                    <h3 class="text-sm font-semibold uppercase tracking-wide text-gray-500">Yıllara göre durum</h3>
                    <p class="text-xs text-gray-400">
                        Her ürün için en yüksek cirolar
                        <span class="inline-flex items-center rounded-md bg-amber-100 px-1.5 py-0.5 font-medium text-amber-900">vurgulu</span>
                        gösterilir.
                    </p>
                </div>
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200 text-sm">
                        <thead class="bg-gray-50">
                            <tr class="whitespace-nowrap">
                                <th class="px-3 py-3 text-left text-xs font-medium text-gray-500">Sezon</th>
                                <th class="px-3 py-3 text-left text-xs font-medium text-gray-500">Ürün</th>
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
                            @forelse ($seasonRows as $row)
                                @php
                                    $peaks = $peakByProduct->get($row['slug'], []);
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
                                    <td class="px-4 py-3 text-gray-700 whitespace-nowrap">{{ $row['product'] }}</td>
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
                                    <td colspan="11" class="px-4 py-8 text-center text-gray-500">Henüz sezon verisi yok.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <div
            x-show="open"
            x-cloak
            class="fixed inset-0 overflow-y-auto px-4 py-6 sm:px-0 z-50"
            style="display: none;"
        >
            <div
                x-show="open"
                x-transition:enter="ease-out duration-200"
                x-transition:enter-start="opacity-0"
                x-transition:enter-end="opacity-100"
                x-transition:leave="ease-in duration-150"
                x-transition:leave-start="opacity-100"
                x-transition:leave-end="opacity-0"
                class="fixed inset-0 bg-gray-500/75"
                x-on:click="open = false"
            ></div>

            <div
                x-show="open"
                x-transition:enter="ease-out duration-200"
                x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
                x-transition:leave="ease-in duration-150"
                x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
                x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                class="relative mb-6 bg-white rounded-lg overflow-hidden shadow-xl sm:w-full sm:max-w-2xl sm:mx-auto"
                role="dialog"
                aria-modal="true"
            >
                <div class="relative p-6" x-show="detail">
                    <button
                        type="button"
                        x-on:click="open = false"
                        class="absolute top-4 right-4 inline-flex items-center justify-center rounded-md p-1.5 text-gray-400 hover:text-gray-600 hover:bg-gray-100"
                        aria-label="Kapat"
                    >
                        <svg class="h-5 w-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>

                    <h3 class="text-lg font-medium text-gray-900 pr-8" x-text="detail?.title"></h3>
                    <p class="mt-1 text-sm text-gray-500" x-text="(detail?.year ?? '') + ' · ' + (detail?.product ?? '')"></p>

                    <dl class="mt-6 grid grid-cols-2 sm:grid-cols-3 gap-4">
                        <div class="rounded-lg bg-gray-50 p-3">
                            <dt class="text-xs uppercase tracking-wide text-gray-500">Toplam kg</dt>
                            <dd class="mt-1 text-lg font-semibold text-gray-900" x-text="formatNumber(detail?.total_kg)"></dd>
                        </div>
                        <div class="rounded-lg bg-gray-50 p-3">
                            <dt class="text-xs uppercase tracking-wide text-gray-500">Sipariş sayısı</dt>
                            <dd class="mt-1 text-lg font-semibold text-gray-900" x-text="formatInt(detail?.order_count)"></dd>
                        </div>
                        <div class="rounded-lg bg-gray-50 p-3">
                            <dt class="text-xs uppercase tracking-wide text-gray-500">Ort. sipariş kg</dt>
                            <dd class="mt-1 text-lg font-semibold text-gray-900" x-text="formatNumber(detail?.avg_order_kg)"></dd>
                        </div>
                        <div class="rounded-lg bg-amber-50 p-3">
                            <dt class="text-xs uppercase tracking-wide text-amber-800/70">Ciro ₺</dt>
                            <dd class="mt-1 text-lg font-semibold text-amber-950" x-text="formatNumber(detail?.revenue_try)"></dd>
                        </div>
                        <div class="rounded-lg bg-emerald-50 p-3">
                            <dt class="text-xs uppercase tracking-wide text-emerald-800/70">Ciro $</dt>
                            <dd class="mt-1 text-lg font-semibold text-emerald-950" x-text="formatNumber(detail?.revenue_usd)"></dd>
                        </div>
                        <div class="rounded-lg bg-yellow-50 p-3">
                            <dt class="text-xs uppercase tracking-wide text-yellow-800/70">Ciro altın</dt>
                            <dd class="mt-1 text-lg font-semibold text-yellow-950" x-text="formatNumber(detail?.revenue_gold)"></dd>
                        </div>
                        <div class="rounded-lg border border-gray-100 p-3">
                            <dt class="text-xs uppercase tracking-wide text-gray-500">Birim fiyat ₺</dt>
                            <dd class="mt-1 text-base font-medium text-gray-900" x-text="formatNumber(detail?.unit_price)"></dd>
                        </div>
                        <div class="rounded-lg border border-gray-100 p-3">
                            <dt class="text-xs uppercase tracking-wide text-gray-500">Dolar kuru</dt>
                            <dd class="mt-1 text-base font-medium text-gray-900" x-text="formatNumber(detail?.usd_rate, 4)"></dd>
                        </div>
                        <div class="rounded-lg border border-gray-100 p-3">
                            <dt class="text-xs uppercase tracking-wide text-gray-500">Altın fiyatı</dt>
                            <dd class="mt-1 text-base font-medium text-gray-900" x-text="formatNumber(detail?.gold_price)"></dd>
                        </div>
                    </dl>

                    <div class="mt-6 flex justify-end">
                        <a
                            x-bind:href="detail?.orders_url"
                            class="inline-flex items-center px-4 py-2 bg-gray-800 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700"
                        >
                            Siparişleri gör
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @if (count($chart['years']) > 0)
        <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.7/dist/chart.umd.min.js"></script>
        <script>
            document.addEventListener('DOMContentLoaded', () => {
                const years = @json($chart['years']);
                const ceviz = @json($chart['ceviz']);
                const badem = @json($chart['badem']);
                const slugs = ['ceviz', 'badem'];
                const numberTick = (value) => new Intl.NumberFormat('tr-TR').format(value);

                new Chart(document.getElementById('salesByYearChart'), {
                    type: 'bar',
                    data: {
                        labels: years,
                        datasets: [
                            {
                                label: 'Ceviz',
                                data: ceviz,
                                backgroundColor: 'rgba(120, 53, 15, 0.75)',
                                borderColor: 'rgb(120, 53, 15)',
                                borderWidth: 1,
                            },
                            {
                                label: 'Badem',
                                data: badem,
                                backgroundColor: 'rgba(180, 83, 9, 0.7)',
                                borderColor: 'rgb(180, 83, 9)',
                                borderWidth: 1,
                            },
                        ],
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        onHover: (event, elements) => {
                            event.native.target.style.cursor = elements.length ? 'pointer' : 'default';
                        },
                        onClick: (event, elements) => {
                            if (!elements.length) return;
                            const el = elements[0];
                            const year = years[el.index];
                            const slug = slugs[el.datasetIndex];
                            window.dispatchEvent(new CustomEvent('open-season-detail', {
                                detail: `${year}-${slug}`,
                            }));
                        },
                        plugins: {
                            legend: { position: 'top' },
                            tooltip: {
                                callbacks: {
                                    label: (ctx) => `${ctx.dataset.label}: ${numberTick(ctx.parsed.y)} kg`,
                                    afterBody: () => ['', 'Detay için tıklayın'],
                                },
                            },
                        },
                        scales: {
                            y: {
                                beginAtZero: true,
                                ticks: { callback: (v) => numberTick(v) },
                                title: { display: true, text: 'kg' },
                            },
                        },
                    },
                });
            });
        </script>
    @endif
</x-app-layout>
