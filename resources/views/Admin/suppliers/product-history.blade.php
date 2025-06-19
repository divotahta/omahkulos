<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Riwayat Produk yang Pernah Dibeli dari ' . $supplier->nama) }}
            </h2>

        </div>
    </x-slot>

    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 mt-6">
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
            <a href="{{ route('admin.suppliers.show', $supplier) }}"
                class="inline-flex items-center px-4 py-2 bg-gray-300 border border-transparent rounded-md font-semibold text-xs text-gray-700 uppercase tracking-widest hover:bg-gray-400 focus:bg-gray-400 active:bg-gray-500 focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-offset-2 transition ease-in-out duration-150">
                Kembali
            </a>
            <div class="p-6 text-gray-900">
                <h3 class="text-lg font-medium text-gray-900 mb-4">Daftar Produk yang Pernah Dibeli</h3>
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th
                                    class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Tanggal</th>
                                <th
                                    class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Nama Produk</th>
                                <th
                                    class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Kode</th>
                                <th
                                    class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Satuan</th>
                                <th
                                    class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Jumlah</th>
                                <th
                                    class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Harga</th>
                                <th
                                    class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Total</th>
                                <th
                                    class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    No. Pembelian</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @forelse($productHistory as $detail)
                                <tr>
                                    <td class="px-4 py-2 whitespace-nowrap">
                                        {{ optional($detail->purchase->tanggal_pembelian)->format('d/m/Y') }}</td>
                                    <td class="px-4 py-2 whitespace-nowrap">{{ $detail->rawMaterial->nama ?? '-' }}</td>
                                    <td class="px-4 py-2 whitespace-nowrap">{{ $detail->rawMaterial->kode ?? '-' }}</td>
                                    <td class="px-4 py-2 whitespace-nowrap">{{ $detail->rawMaterial->satuan ?? '-' }}
                                    </td>
                                    <td class="px-4 py-2 whitespace-nowrap">{{ $detail->jumlah }}</td>
                                    <td class="px-4 py-2 whitespace-nowrap">Rp
                                        {{ number_format($detail->harga, 0, ',', '.') }}</td>
                                    <td class="px-4 py-2 whitespace-nowrap">Rp
                                        {{ number_format($detail->total, 0, ',', '.') }}</td>
                                    <td class="px-4 py-2 whitespace-nowrap">
                                        {{ $detail->purchase->nomor_pembelian ?? '-' }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="8" class="px-4 py-2 text-center text-gray-500">Belum ada riwayat
                                        produk</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
