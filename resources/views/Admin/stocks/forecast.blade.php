<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Forecast Stok') }}
            </h2>

        </div>
    </x-slot>


    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <!-- Notifikasi Reorder -->
        {{-- @foreach ($products as $product)
            @if ($product->forecast['needs_reorder'])
                <div class="mb-4 bg-red-100 border-l-4 border-red-500 text-red-700 p-4" role="alert">
                    <div class="flex">
                        <div class="flex-shrink-0">
                            <svg class="h-5 w-5 text-red-500" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd" />
                            </svg>
                        </div>
                        <div class="ml-3">
                            <p class="text-sm">
                                <strong>Perlu Reorder:</strong> {{ $product->nama }} ({{ $product->code }})
                                <br>
                                Stok saat ini: {{ number_format($product->stok, 0) }} {{ $product->unit->nama_satuan }}
                                <br>
                                Reorder Point: {{ number_format($product->forecast['reorder_point'], 0) }} {{ $product->unit->nama_satuan }}
                                <br>
                                Rekomendasi Order: {{ number_format($product->forecast['eoq'], 0) }} {{ $product->unit->nama_satuan }}
                            </p>
                        </div>
                    </div>
                </div>
            @endif
        @endforeach --}}

        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
            <a href="{{ route('admin.stocks.index') }}"
                class="inline-flex items-center px-4 py-2 bg-gray-300 border border-transparent rounded-md font-semibold text-xs text-gray-700 uppercase tracking-widest hover:bg-gray-400 focus:bg-gray-400 active:bg-gray-500 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150">
                Kembali
            </a>
            <div class="p-6 text-gray-900">
                <!-- Filter -->
                <form action="{{ route('admin.stocks.forecast') }}" method="GET" class="mb-6">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label for="kategori_id" class="block text-sm font-medium text-gray-700">Kategori</label>
                            <select name="kategori_id" id="kategori_id"
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                <option value="">Semua Kategori</option>
                                @foreach ($categories as $category)
                                    <option value="{{ $category->id }}"
                                        {{ request('kategori_id') == $category->id ? 'selected' : '' }}>
                                        {{ $category->nama_kategori }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="flex items-end">
                            <button type="submit"
                                class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-700 focus:bg-indigo-700 active:bg-indigo-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150">
                                Filter
                            </button>
                        </div>
                    </div>
                </form>

                <!-- Tabel Forecast -->
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th scope="col"
                                    class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Kode
                                </th>
                                <th scope="col"
                                    class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Nama Produk
                                </th>
                                <th scope="col"
                                    class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Kategori
                                </th>
                                <th scope="col"
                                    class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Stok Saat Ini
                                </th>
                                <th scope="col"
                                    class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Penjualan Mingguan
                                </th>
                                <th scope="col"
                                    class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Safety Stock
                                </th>
                                <th scope="col"
                                    class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Reorder Point
                                </th>
                                <th scope="col"
                                    class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    EOQ
                                </th>
                                <th scope="col"
                                    class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Pola Penjualan
                                </th>
                                <th scope="col"
                                    class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Forecast Minggu Depan
                                </th>
                                <th scope="col"
                                    class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Status
                                </th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @forelse($products as $product)
                                <tr class="hover:bg-gray-50">
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                        {{ $product->kode_produk }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                        {{ $product->nama_produk }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                        {{ $product->category->nama_kategori }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                        {{ $product->stok }} {{ $product->unit->nama_satuan }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                        {{ number_format($product->forecast['avg_weekly_sales'], 0) }}
                                        {{ $product->unit->nama_satuan }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                        {{ number_format($product->forecast['safety_stock'], 0) }}
                                        {{ $product->unit->nama_satuan }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                        {{ number_format($product->forecast['reorder_point'], 0) }}
                                        {{ $product->unit->nama_satuan }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                        {{ number_format($product->forecast['eoq'], 0) }}
                                        {{ $product->unit->nama_satuan }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm">
                                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full 
                                            {{ $product->forecast['sales_pattern'] === 'Stabil' ? 'bg-green-100 text-green-800' : 
                                               ($product->forecast['sales_pattern'] === 'Moderat' ? 'bg-yellow-100 text-yellow-800' : 
                                               'bg-red-100 text-red-800') }}">
                                            {{ $product->forecast['sales_pattern'] }}
                                        </span>
                                        <div class="text-xs text-gray-500 mt-1">
                                            {{ $product->forecast['pattern_description'] }}
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                        {{ number_format($product->forecast['next_week_forecast'], 0) }}
                                        {{ $product->unit->nama_satuan }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm">
                                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full 
                                            {{ $product->forecast['stock_status'] === 'Aman' ? 'bg-green-100 text-green-800' : 
                                               ($product->forecast['stock_status'] === 'Rendah' ? 'bg-yellow-100 text-yellow-800' : 
                                               'bg-red-100 text-red-800') }}">
                                            {{ $product->forecast['stock_status'] }}
                                        </span>
                                        <div class="text-xs text-gray-500 mt-1">
                                            {{ $product->forecast['status_description'] }}
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="9"
                                        class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 text-center">
                                        Tidak ada data produk
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
    </div>
</x-app-layout>
