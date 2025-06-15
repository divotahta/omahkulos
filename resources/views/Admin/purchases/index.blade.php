<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Daftar Pembelian Bahan Baku') }}
            </h2>

        </div>
    </x-slot>

    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 py-6">
        <div class="flex justify-start">
        <a href="{{ route('admin.purchases.create') }}"
                class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded-lg transition-all duration-300 ease-in-out transform hover:scale-105 hover:shadow-lg flex items-center gap-2">
                <i class="fas fa-plus"></i>
                <span>Tambah Pembelian</span>
        </a>
        </div>
        <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg">
            <div class="p-6">
                @if (session('success'))
                    <div class="mb-4 bg-green-100 border-l-4 border-green-500 text-green-700 p-4 rounded-lg animate-fade-in-down"
                        role="alert">
                        <div class="flex items-center">
                            <i class="fas fa-check-circle mr-2"></i>
                            <span>{{ session('success') }}</span>
                        </div>
                    </div>
                @endif

                @if (session('error'))
                    <div class="mb-4 bg-red-100 border-l-4 border-red-500 text-red-700 p-4 rounded-lg animate-fade-in-down"
                        role="alert">
                        <div class="flex items-center">
                            <i class="fas fa-exclamation-circle mr-2"></i>
                            <span>{{ session('error') }}</span>
                        </div>
                    </div>
                @endif

                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th
                                    class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Supplier</th>
                                <th
                                    class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Tanggal</th>
                                <th
                                    class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Bahan Baku</th>
                                <th
                                    class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Total</th>
                                <th
                                    class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Status</th>
                                <th
                                    class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @forelse ($purchases as $purchase)
                                <tr class="hover:bg-gray-50 transition-all duration-300 animate-fade-in">
                                    <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-900">
                                        {{ $purchase->supplier->nama }}
                                    </td>
                                    <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-900">
                                        {{ $purchase->tanggal_pembelian->format('d/m/Y') }}
                                    </td>
                                    <td class="px-4 py-3 text-sm text-gray-900">
                                        <div class="space-y-1">
                                            @foreach ($purchase->details as $detail)
                                                <div class="flex justify-between items-center">
                                                    <span class="text-gray-700">{{ $detail->rawMaterial->nama }}</span>
                                                    <span class="text-gray-600 ml-2">{{ $detail->jumlah }} {{ $detail->rawMaterial->satuan }}</span>
                                                </div>
                                            @endforeach
                                        </div>
                                    </td>
                                    <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-900">
                                        <span class="font-semibold text-purple-600">
                                        Rp {{ number_format($purchase->total_amount, 0, ',', '.') }}
                                        </span>
                                    </td>
                                    <td class="px-4 py-3 whitespace-nowrap">
                                        <span
                                            class="px-3 py-1 text-xs font-semibold rounded-full
                                                @if ($purchase->status_pembelian == 'pending') bg-yellow-100 text-yellow-800
                                                @elseif($purchase->status_pembelian == 'approved') bg-green-100 text-green-800
                                                @elseif($purchase->status_pembelian == 'rejected') bg-red-100 text-red-800
                                                @elseif($purchase->status_pembelian == 'received') bg-blue-100 text-blue-800 @endif">
                                            {{ ucfirst($purchase->status_pembelian) }}
                                        </span>
                                    </td>
                                    <td class="px-4 py-3 whitespace-nowrap text-sm font-medium">
                                        <div class="flex space-x-2">
                                            <a href="{{ route('admin.purchases.show', $purchase) }}"
                                                class="text-indigo-600 hover:text-indigo-900 transition-all duration-300 transform hover:scale-110"
                                                title="Lihat Detail">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                            @if ($purchase->status_pembelian == 'pending')
                                                <a href="{{ route('admin.purchases.edit', $purchase) }}"
                                                    class="text-yellow-600 hover:text-yellow-900 transition-all duration-300 transform hover:scale-110"
                                                    title="Edit">
                                                    <i class="fas fa-edit"></i>
                                                </a>
                                                <form action="{{ route('admin.purchases.approve', $purchase->id) }}"
                                                    method="POST" class="inline">
                                                    @csrf
                                                    <button type="submit"
                                                        class="text-green-600 hover:text-green-900 transition-all duration-300 transform hover:scale-110"
                                                        title="Approve"
                                                        onclick="return confirm('Apakah Anda yakin ingin menyetujui pembelian ini?')">
                                                        <i class="fas fa-check"></i>
                                                    </button>
                                                </form>
                                                <form action="{{ route('admin.purchases.reject', $purchase->id) }}"
                                                    method="POST" class="inline">
                                                    @csrf
                                                    <button type="submit"
                                                        class="text-red-600 hover:text-red-900 transition-all duration-300 transform hover:scale-110"
                                                        title="Reject"
                                                        onclick="return confirm('Apakah Anda yakin ingin menolak pembelian ini?')">
                                                        <i class="fas fa-times"></i>
                                                </button>
                                                </form>
                                            @endif
                                            @if ($purchase->status_pembelian == 'approved')
                                                <form action="{{ route('admin.purchases.receive', $purchase->id) }}"
                                                    method="POST" class="inline">
                                                    @csrf
                                                    <button type="submit"
                                                        class="text-blue-600 hover:text-blue-900 transition-all duration-300 transform hover:scale-110"
                                                        title="Receive"
                                                        onclick="return confirm('Apakah Anda yakin ingin menerima barang ini?')">
                                                        <i class="fas fa-box"></i>
                                                    </button>
                                                </form>
                                            @endif
                                            @if ($purchase->status_pembelian == 'pending')
                                                <form action="{{ route('admin.purchases.destroy', $purchase->id) }}"
                                                    method="POST" class="inline">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit"
                                                        class="text-red-600 hover:text-red-900 transition-all duration-300 transform hover:scale-110"
                                                        title="Hapus"
                                                        onclick="return confirm('Apakah Anda yakin ingin menghapus pembelian ini?')">
                                                        <i class="fas fa-trash"></i>
                                                    </button>
                                                </form>
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="px-4 py-8 text-center text-gray-500">
                                        <div class="flex flex-col items-center justify-center">
                                            <i class="fas fa-box-open text-4xl text-gray-400 mb-2"></i>
                                            <p>Tidak ada data pembelian</p>
                                        </div>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <div class="mt-6">
                    {{ $purchases->links() }}
                </div>
            </div>
        </div>
    </div>

    <style>
        @keyframes fadeIn {
            from {
                opacity: 0;
                transform: translateY(-10px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .animate-fade-in {
            animation: fadeIn 0.5s ease-out;
        }

        .animate-fade-in-down {
            animation: fadeIn 0.3s ease-out;
        }

        .transition-all {
            transition: all 0.3s ease;
        }

        .transform {
            transform: translateZ(0);
        }

        .hover\:scale-110:hover {
            transform: scale(1.1);
        }
    </style>
</x-app-layout>
