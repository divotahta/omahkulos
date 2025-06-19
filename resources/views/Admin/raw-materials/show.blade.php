<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Detail Bahan Baku
            </h2>
            
        </div>
    </x-slot>

        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="flex space-x-2">
                <a href="{{ route('admin.raw-materials.index') }}" class="inline-flex items-center px-4 py-2 bg-gray-800 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                    </svg>
                    Kembali
                </a>
                <a href="{{ route('admin.raw-materials.edit', $rawMaterial) }}" class="inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-700">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                    </svg>
                    Edit
                </a>
            </div>
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 bg-white border-b border-gray-200">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div class="space-y-4">
                            <div class="border-b pb-4">
                                <h3 class="text-lg font-medium text-gray-900">Informasi Dasar</h3>
                            </div>
                            <div class="grid grid-cols-2 gap-4">
                                <div class="text-sm font-medium text-gray-500">Kode</div>
                                <div class="text-sm text-gray-900">{{ $rawMaterial->kode }}</div>

                                <div class="text-sm font-medium text-gray-500">Nama</div>
                                <div class="text-sm text-gray-900">{{ $rawMaterial->nama }}</div>

                                <div class="text-sm font-medium text-gray-500">Satuan</div>
                                <div class="text-sm text-gray-900">{{ $rawMaterial->satuan }}</div>
                            </div>
                        </div>

                        <div class="space-y-4">
                            <div class="border-b pb-4">
                                <h3 class="text-lg font-medium text-gray-900">Informasi Stok & Harga</h3>
                            </div>
                            <div class="grid grid-cols-2 gap-4">
                                <div class="text-sm font-medium text-gray-500">Stok</div>
                                <div class="text-sm text-gray-900">{{ $rawMaterial->stok }}</div>

                                <div class="text-sm font-medium text-gray-500">Harga</div>
                                <div class="text-sm text-gray-900">Rp {{ number_format($rawMaterial->harga, 0, ',', '.') }}</div>

                                <div class="text-sm font-medium text-gray-500">Supplier</div>
                                <div class="text-sm text-gray-900">{{ $rawMaterial->supplier ? $rawMaterial->supplier->nama : '-' }}</div>
                            </div>
                        </div>

                        <div class="space-y-4">
                            <div class="border-b pb-4">
                                <h3 class="text-lg font-medium text-gray-900">Informasi Tambahan</h3>
                            </div>
                            <div class="grid grid-cols-2 gap-4">
                                <div class="text-sm font-medium text-gray-500">Tanggal Expired</div>
                                <div class="text-sm text-gray-900">{{ $rawMaterial->expired_date ? $rawMaterial->expired_date->format('d/m/Y') : '-' }}</div>

                                <div class="text-sm font-medium text-gray-500">Deskripsi</div>
                                <div class="text-sm text-gray-900">{{ $rawMaterial->deskripsi ?? '-' }}</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout> 