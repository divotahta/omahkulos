<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Detail Pembelian') }} #{{ $purchase->invoice_number }}
            </h2>
            <div class="flex space-x-2">
                <a href="{{ route('owner.purchases.index') }}" 
                    class="inline-flex items-center px-4 py-2 bg-gray-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700">
                    <i class="fas fa-arrow-left mr-2"></i> Kembali
                </a>
                @if($purchase->status === 'pending')
                <button onclick="showApprovalModal()"
                    class="inline-flex items-center px-4 py-2 bg-green-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-green-700">
                    <i class="fas fa-check mr-2"></i> Setujui
                </button>
                <button onclick="showRejectionModal()"
                    class="inline-flex items-center px-4 py-2 bg-red-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-red-700">
                    <i class="fas fa-times mr-2"></i> Tolak
                </button>
                @endif
            </div>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 bg-white border-b border-gray-200">
                    <!-- Informasi Pembelian -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                        <div>
                            <h3 class="text-lg font-medium text-gray-900 mb-4">Informasi Pembelian</h3>
                            <dl class="grid grid-cols-1 gap-4">
                                <div>
                                    <dt class="text-sm font-medium text-gray-500">Status</dt>
                                    <dd class="mt-1">
                                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full 
                                            {{ $purchase->status === 'pending' ? 'bg-yellow-100 text-yellow-800' : '' }}
                                            {{ $purchase->status === 'approved' ? 'bg-green-100 text-green-800' : '' }}
                                            {{ $purchase->status === 'rejected' ? 'bg-red-100 text-red-800' : '' }}">
                                            {{ ucfirst($purchase->status) }}
                                        </span>
                                    </dd>
                                </div>
                                <div>
                                    <dt class="text-sm font-medium text-gray-500">Tanggal Pembelian</dt>
                                </div>
                                <div>
                                    <dt class="text-sm font-medium text-gray-500">Pemasok</dt>
                                </div>
                                <div>
                                    <dt class="text-sm font-medium text-gray-500">Catatan</dt>
                                </div>
                            </dl>
                        </div>
                        <div>
                            <h3 class="text-lg font-medium text-gray-900 mb-4">Informasi Persetujuan</h3>
                            <dl class="grid grid-cols-1 gap-4">
                                @if($purchase->status === 'approved')
                                <div>
                                    <dt class="text-sm font-medium text-gray-500">Disetujui Oleh</dt>
                                    <dd class="mt-1 text-sm text-gray-900">{{ $purchase->approvedBy->name }}</dd>
                                </div>
                                <div>
                                    <dt class="text-sm font-medium text-gray-500">Tanggal Persetujuan</dt>
                                    <dd class="mt-1 text-sm text-gray-900">{{ $purchase->approved_at->format('d/m/Y H:i') }}</dd>
                                </div>
                                @elseif($purchase->status === 'rejected')
                                <div>
                                    <dt class="text-sm font-medium text-gray-500">Ditolak Oleh</dt>
                                    <dd class="mt-1 text-sm text-gray-900">{{ $purchase->rejectedBy->name }}</dd>
                                </div>
                                <div>
                                    <dt class="text-sm font-medium text-gray-500">Tanggal Penolakan</dt>
                                    <dd class="mt-1 text-sm text-gray-900">{{ $purchase->rejected_at->format('d/m/Y H:i') }}</dd>
                                </div>
                                <div>
                                    <dt class="text-sm font-medium text-gray-500">Alasan Penolakan</dt>
                                    <dd class="mt-1 text-sm text-gray-900">{{ $purchase->rejection_reason }}</dd>
                                </div>
                                @endif
                            </dl>
                        </div>
                    </div>

                    <!-- Detail Produk -->
                    <div class="mt-8">
                        <h3 class="text-lg font-medium text-gray-900 mb-4">Detail Produk</h3>
                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Produk</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Harga</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Qty</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Subtotal</th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200">
                                    @foreach($purchase->details as $detail)
                                    <tr>
                                        <td class="px-6 py-4 whitespace-nowrap">{{ $detail->product->name }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap">Rp {{ number_format($detail->price, 0, ',', '.') }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap">{{ $detail->quantity }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap">Rp {{ number_format($detail->subtotal, 0, ',', '.') }}</td>
                                    </tr>
                                    @endforeach
                                </tbody>
                                <tfoot class="bg-gray-50">
                                    <tr>
                                        <td colspan="3" class="px-6 py-4 text-right font-medium">Total:</td>
                                        <td class="px-6 py-4 whitespace-nowrap font-medium">
                                            Rp {{ number_format($purchase->total_amount, 0, ',', '.') }}
                                        </td>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>
                    </div>

                    
            </div>
        </div>
    </div>

    

    @push('scripts')
    <script>
        function showApprovalModal() {
            document.getElementById('approvalModal').classList.remove('hidden');
        }

        function hideApprovalModal() {
            document.getElementById('approvalModal').classList.add('hidden');
        }

        function showRejectionModal() {
            document.getElementById('rejectionModal').classList.remove('hidden');
        }

        function hideRejectionModal() {
            document.getElementById('rejectionModal').classList.add('hidden');
        }
    </script>
    @endpush
</x-app-layout> 