<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Laporan
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <!-- Filter Tanggal -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
                <div class="p-6">
                    <form action="{{ route('admin.reports.index') }}" method="GET" class="flex gap-4">
                        <div>
                            <label for="start_date" class="block text-sm font-medium text-gray-700">Tanggal Mulai</label>
                            <input type="date" name="start_date" id="start_date" value="{{ $startDate->format('Y-m-d') }}"
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                        </div>
                        <div>
                            <label for="end_date" class="block text-sm font-medium text-gray-700">Tanggal Akhir</label>
                            <input type="date" name="end_date" id="end_date" value="{{ $endDate->format('Y-m-d') }}"
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                        </div>
                        <div class="flex items-end">
                            <button type="submit"
                                class="bg-indigo-600 text-white px-4 py-2 rounded-md hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2">
                                Filter
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Ringkasan -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-6">
                <!-- Total Penjualan -->
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <h3 class="text-lg font-medium text-gray-900 mb-2">Total Penjualan</h3>
                        <p class="text-3xl font-bold text-indigo-600">Rp {{ number_format($salesSummary->total_sales ?? 0) }}</p>
                        <p class="text-sm text-gray-500 mt-2">{{ $salesSummary->total_transactions ?? 0 }} Transaksi</p>
                    </div>
                </div>

                <!-- Rata-rata Penjualan -->
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <h3 class="text-lg font-medium text-gray-900 mb-2">Rata-rata Penjualan</h3>
                        <p class="text-3xl font-bold text-indigo-600">Rp {{ number_format($salesSummary->average_sales ?? 0) }}</p>
                        <p class="text-sm text-gray-500 mt-2">Per Transaksi</p>
                    </div>
                </div>

                <!-- Total Pembelian -->
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <h3 class="text-lg font-medium text-gray-900 mb-2">Total Pembelian</h3>
                        <p class="text-3xl font-bold text-indigo-600">Rp {{ number_format($purchaseSummary->total_purchases_amount ?? 0) }}</p>
                        <p class="text-sm text-gray-500 mt-2">{{ $purchaseSummary->total_purchases ?? 0 }} Pembelian</p>
                    </div>
                </div>

                <!-- Rata-rata Pembelian -->
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <h3 class="text-lg font-medium text-gray-900 mb-2">Rata-rata Pembelian</h3>
                        <p class="text-3xl font-bold text-indigo-600">Rp {{ number_format($purchaseSummary->average_purchase ?? 0) }}</p>
                        <p class="text-sm text-gray-500 mt-2">Per Pembelian</p>
                    </div>
                </div>
            </div>

            <!-- Grafik dan Tabel -->
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                <!-- Grafik Penjualan -->
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <h3 class="text-lg font-medium text-gray-900 mb-4">Grafik Penjualan Harian</h3>
                        <canvas id="salesChart" height="300"></canvas>
                    </div>
                </div>

                <!-- Grafik Pembelian -->
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <h3 class="text-lg font-medium text-gray-900 mb-4">Grafik Pembelian Harian</h3>
                        <canvas id="purchaseChart" height="300"></canvas>
                    </div>
                </div>

                <!-- Produk Terlaris -->
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <h3 class="text-lg font-medium text-gray-900 mb-4">Produk Terlaris</h3>
                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200">
                                <thead>
                                    <tr>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Produk</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Jumlah Terjual</th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200">
                                    @foreach($topProducts as $product)
                                    <tr>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                            {{ $product->product->nama_produk }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                            {{ number_format($product->total_quantity) }}
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                <!-- Metode Pembayaran -->
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <h3 class="text-lg font-medium text-gray-900 mb-4">Metode Pembayaran</h3>
                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200">
                                <thead>
                                    <tr>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Metode</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Jumlah</th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200">
                                    @foreach($paymentMethods as $method)
                                    <tr>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                            {{ ucfirst($method->metode_pembayaran) }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                            {{ number_format($method->total) }}
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        // Data untuk grafik
        const salesData = @json($dailySales);
        const purchaseData = @json($dailyPurchases);

        // Grafik Penjualan
        new Chart(document.getElementById('salesChart'), {
            type: 'line',
            data: {
                labels: salesData.map(item => item.date),
                datasets: [{
                    label: 'Total Penjualan',
                    data: salesData.map(item => item.total_sales),
                    borderColor: 'rgb(79, 70, 229)',
                    tension: 0.1
                }]
            },
            options: {
                responsive: true,
                plugins: {
                    legend: {
                        position: 'top',
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true
                    }
                }
            }
        });

        // Grafik Pembelian
        new Chart(document.getElementById('purchaseChart'), {
            type: 'line',
            data: {
                labels: purchaseData.map(item => item.date),
                datasets: [{
                    label: 'Total Pembelian',
                    data: purchaseData.map(item => item.total_purchases),
                    borderColor: 'rgb(16, 185, 129)',
                    tension: 0.1
                }]
            },
            options: {
                responsive: true,
                plugins: {
                    legend: {
                        position: 'top',
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true
                    }
                }
            }
        });
    </script>
</x-app-layout> 