<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Detail Pembelian') }}
            </h2>
            
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="flex space-x-2">
                <a href="{{ route('admin.purchases.index') }}"
                    class="inline-flex items-center px-4 py-2 bg-gray-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 focus:bg-gray-700 active:bg-gray-900 focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-offset-2 transition">
                    <i class="fas fa-arrow-left mr-2"></i>
                    Kembali
                </a>
                @if ($purchase->status_pembelian === 'pending')
                    <a href="{{ route('admin.purchases.edit', $purchase) }}"
                        class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-700 focus:bg-indigo-700 active:bg-indigo-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition">
                        <i class="fas fa-edit mr-2"></i>
                        Edit
                    </a>
                @endif
            </div>
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 bg-white border-b border-gray-200">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <!-- Informasi Pembelian -->
                        <div class="bg-gray-50 p-4 rounded-lg">
                            <h3 class="text-lg font-semibold mb-4">Informasi Pembelian</h3>
                            <div class="space-y-3">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700">No. Pembelian</label>
                                    <p class="mt-1 text-sm text-gray-900">{{ $purchase->nomor_pembelian }}</p>
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700">Tanggal Pembelian</label>
                                    <p class="mt-1 text-sm text-gray-900">{{ $purchase->tanggal_pembelian->format('d/m/Y') }}</p>
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700">Supplier</label>
                                    <p class="mt-1 text-sm text-gray-900">{{ $purchase->supplier->nama_supplier }}</p>
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700">Status</label>
                                    <p class="mt-1">
                                        <span
                                            class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full 
                                            {{ $purchase->status_pembelian === 'pending' ? 'bg-yellow-100 text-yellow-800' : '' }}
                                            {{ $purchase->status_pembelian === 'approved' ? 'bg-green-100 text-green-800' : '' }}
                                            {{ $purchase->status_pembelian === 'rejected' ? 'bg-red-100 text-red-800' : '' }}
                                            {{ $purchase->status_pembelian === 'received' ? 'bg-blue-100 text-blue-800' : '' }}">
                                            {{ ucfirst($purchase->status_pembelian) }}
                                        </span>
                                    </p>
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700">Catatan</label>
                                    <p class="mt-1 text-sm text-gray-900">{{ $purchase->catatan ?? '-' }}</p>
                                </div>
                            </div>
                        </div>

                        <!-- Ringkasan -->
                        <div class="bg-gray-50 p-4 rounded-lg">
                            <h3 class="text-lg font-semibold mb-4">Ringkasan</h3>
                            <div class="space-y-3">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700">Total Item</label>
                                    <p class="mt-1 text-sm text-gray-900">{{ $purchase->details->count() }} item</p>
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700">Total Pembelian</label>
                                    <p class="mt-1 text-sm text-gray-900">Rp {{ number_format($purchase->total_amount, 0, ',', '.') }}</p>
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700">Dibuat Oleh</label>
                                    <p class="mt-1 text-sm text-gray-900">{{ $purchase->user->nama}}</p>
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700">Tanggal Dibuat</label>
                                    <p class="mt-1 text-sm text-gray-900">{{ $purchase->created_at->format('d/m/Y H:i') }}</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Detail Item -->
                    <div class="mt-8">
                        <h3 class="text-lg font-semibold mb-4">Detail Item</h3>
                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th scope="col"
                                            class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            Bahan Baku
                                        </th>
                                        <th scope="col"
                                            class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            Kuantitas
                                        </th>
                                        <th scope="col"
                                            class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            Harga Satuan
                                        </th>
                                        <th scope="col"
                                            class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            Total
                                        </th>
                                        <th scope="col"
                                            class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            Catatan
                                        </th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200">
                                    @foreach ($purchase->details as $detail)
                                        <tr>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                                {{ $detail->rawMaterial->nama }}
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                                {{ $detail->jumlah }} {{ $detail->rawMaterial->satuan }}
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                                Rp {{ number_format($detail->harga, 0, ',', '.') }}
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                                Rp {{ number_format($detail->total, 0, ',', '.') }}
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                                {{ $detail->catatan ?? '-' }}
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

    <!-- Modal Tolak Pembelian -->
    <div id="rejectModal" class="fixed inset-0 bg-gray-500 bg-opacity-75 hidden" x-data="{ show: false }" x-show="show"
        @click.away="show = false">
        <div class="flex items-center justify-center min-h-screen p-4">
            <div class="bg-white rounded-lg p-6 max-w-md w-full">
                <h3 class="text-lg font-medium text-gray-900 mb-4">Tolak Pembelian</h3>
                <form action="{{ route('admin.purchases.reject', $purchase) }}" method="POST">
                    @csrf
                    <div class="mb-4">
                        <label for="alasan_penolakan" class="block text-sm font-medium text-gray-700">Alasan
                            Penolakan</label>
                        <textarea name="alasan_penolakan" id="alasan_penolakan" rows="3"
                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                            required></textarea>
                    </div>
                    <div class="flex justify-end space-x-2">
                        <button type="button" onclick="hideRejectModal()"
                            class="inline-flex items-center px-4 py-2 bg-gray-300 border border-transparent rounded-md font-semibold text-xs text-gray-700 uppercase tracking-widest hover:bg-gray-400">
                            Batal
                        </button>
                        <button type="submit"
                            class="inline-flex items-center px-4 py-2 bg-red-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-red-700">
                            Tolak Pembelian
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    @push('scripts')
        <script>
            function showRejectModal() {
                document.getElementById('rejectModal').classList.remove('hidden');
            }

            function hideRejectModal() {
                document.getElementById('rejectModal').classList.add('hidden');
            }
        </script>
    @endpush
</x-app-layout>
