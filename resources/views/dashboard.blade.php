<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">Özet</h2>
    </x-slot>

    <div class="py-8">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
            <div class="bg-white shadow-sm sm:rounded-lg p-6">
                <h3 class="text-sm font-semibold uppercase tracking-wide text-gray-500 mb-4">Yıllara göre satış (kg)</h3>
                @if (count($chart['years']) === 0)
                    <p class="text-sm text-gray-500">Henüz grafikte gösterilecek sipariş yok.</p>
                @else
                    <div class="relative h-80">
                        <canvas id="salesByYearChart"></canvas>
                    </div>
                @endif
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div class="bg-white shadow-sm sm:rounded-lg p-6">
                    <h3 class="text-sm font-semibold uppercase tracking-wide text-gray-500 mb-4">Ceviz (kg)</h3>
                    @if (count($chart['years']) === 0)
                        <p class="text-sm text-gray-500">Veri yok.</p>
                    @else
                        <div class="relative h-64">
                            <canvas id="cevizByYearChart"></canvas>
                        </div>
                    @endif
                </div>
                <div class="bg-white shadow-sm sm:rounded-lg p-6">
                    <h3 class="text-sm font-semibold uppercase tracking-wide text-gray-500 mb-4">Badem (kg)</h3>
                    @if (count($chart['years']) === 0)
                        <p class="text-sm text-gray-500">Veri yok.</p>
                    @else
                        <div class="relative h-64">
                            <canvas id="bademByYearChart"></canvas>
                        </div>
                    @endif
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

                const numberTick = (value) => new Intl.NumberFormat('tr-TR').format(value);

                const sharedOptions = {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: { position: 'top' },
                        tooltip: {
                            callbacks: {
                                label: (ctx) => `${ctx.dataset.label}: ${numberTick(ctx.parsed.y)} kg`,
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
                };

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
                    options: sharedOptions,
                });

                new Chart(document.getElementById('cevizByYearChart'), {
                    type: 'bar',
                    data: {
                        labels: years,
                        datasets: [{
                            label: 'Ceviz',
                            data: ceviz,
                            backgroundColor: 'rgba(120, 53, 15, 0.75)',
                            borderColor: 'rgb(120, 53, 15)',
                            borderWidth: 1,
                        }],
                    },
                    options: { ...sharedOptions, plugins: { ...sharedOptions.plugins, legend: { display: false } } },
                });

                new Chart(document.getElementById('bademByYearChart'), {
                    type: 'bar',
                    data: {
                        labels: years,
                        datasets: [{
                            label: 'Badem',
                            data: badem,
                            backgroundColor: 'rgba(180, 83, 9, 0.7)',
                            borderColor: 'rgb(180, 83, 9)',
                            borderWidth: 1,
                        }],
                    },
                    options: { ...sharedOptions, plugins: { ...sharedOptions.plugins, legend: { display: false } } },
                });
            });
        </script>
    @endif
</x-app-layout>
