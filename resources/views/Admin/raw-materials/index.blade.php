<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Daftar Bahan Baku') }}
        </h2>
           
        </div>
    </x-slot>

        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <a href="{{ route('admin.raw-materials.create') }}" 
            class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded transition duration-300 ease-in-out transform hover:scale-105">
             <i class="fas fa-plus mr-2"></i>Tambah Bahan Baku
         </a>
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 bg-white border-b border-gray-200">
                    @if(session('success'))
                        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative mb-4 animate-fade-in-down" role="alert">
                            <span class="block sm:inline">{{ session('success') }}</span>
                        </div>
                    @endif

                    @if(session('error'))
                        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4 animate-fade-in-down" role="alert">
                            <span class="block sm:inline">{{ session('error') }}</span>
                        </div>
                    @endif

                    <!-- Search and Filter Section -->
                    <div class="mb-6 flex flex-col md:flex-row gap-4">
                        <div class="flex-1">
                            <input type="text" id="search" placeholder="Cari bahan baku..." 
                                   class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500 transition duration-300">
                        </div>
                        <div class="flex gap-2">
                            <select id="filter-supplier" class="px-4 py-2 border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500 transition duration-300">
                                <option value="">Semua Supplier</option>
                                @foreach($suppliers as $supplier)
                                    <option value="{{ $supplier->id }}">{{ $supplier->nama }}</option>
                                @endforeach
                            </select>
                            <select id="filter-stock" class="px-4 py-2 border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500 transition duration-300">
                                <option value="">Semua Stok</option>
                                <option value="low">Stok Rendah</option>
                                <option value="out">Stok Habis</option>
                            </select>
                        </div>
                    </div>

                    <!-- Table Section -->
                    <div class="overflow-x-auto rounded-lg shadow">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Kode</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Nama Bahan</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Supplier</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Stok</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Satuan</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Harga</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Aksi</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @foreach($rawMaterials as $material)
                                <tr class="hover:bg-gray-50 transition duration-300 animate-fade-in">
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                        {{ $material->kode }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                        {{ $material->nama }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                        {{ $material->supplier->nama }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm">
                                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full 
                                            @if($material->stok <= 5) bg-red-100 text-red-800
                                            @elseif($material->stok <= 10) bg-yellow-100 text-yellow-800
                                            @else bg-green-100 text-green-800
                                            @endif">
                                            {{ $material->stok }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                        {{ $material->satuan }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                        Rp {{ number_format($material->harga, 0, ',', '.') }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                        <div class="flex space-x-2">
                                            <a href="{{ route('admin.raw-materials.show', $material->id) }}" 
                                               class="text-indigo-600 hover:text-indigo-900 transition duration-300">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                            <a href="{{ route('admin.raw-materials.edit', $material->id) }}" 
                                               class="text-yellow-600 hover:text-yellow-900 transition duration-300">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            <form action="{{ route('admin.raw-materials.destroy', $material->id) }}" method="POST" class="inline">
                                            @csrf
                                            @method('DELETE')
                                                <button type="submit" 
                                                        class="text-red-600 hover:text-red-900 transition duration-300"
                                                        onclick="return confirm('Apakah Anda yakin ingin menghapus bahan baku ini?')">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                        </form>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                    </div>

                    <div class="mt-4">
                        {{ $rawMaterials->links() }}
                    </div>
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

        .transition {
            transition: all 0.3s ease;
        }

        .transform {
            transform: translateZ(0);
        }

        .hover\:scale-105:hover {
            transform: scale(1.05);
        }
    </style>

    <script>
        // Search functionality
        document.getElementById('search').addEventListener('keyup', function() {
            let searchText = this.value.toLowerCase();
            let rows = document.querySelectorAll('tbody tr');
            
            rows.forEach(row => {
                let text = row.textContent.toLowerCase();
                row.style.display = text.includes(searchText) ? '' : 'none';
            });
        });

        // Filter by supplier
        document.getElementById('filter-supplier').addEventListener('change', function() {
            let supplierId = this.value;
            let rows = document.querySelectorAll('tbody tr');
            
            rows.forEach(row => {
                if (!supplierId) {
                    row.style.display = '';
                    return;
                }
                let supplierCell = row.querySelector('td:nth-child(3)');
                row.style.display = supplierCell.textContent.includes(supplierId) ? '' : 'none';
            });
        });

        // Filter by stock
        document.getElementById('filter-stock').addEventListener('change', function() {
            let stockFilter = this.value;
            let rows = document.querySelectorAll('tbody tr');
            
            rows.forEach(row => {
                if (!stockFilter) {
                    row.style.display = '';
                    return;
                }
                let stockCell = row.querySelector('td:nth-child(4)');
                let stock = parseInt(stockCell.textContent.trim());
                
                if (stockFilter === 'low' && stock > 0 && stock <= 10) {
                    row.style.display = '';
                } else if (stockFilter === 'out' && stock === 0) {
                    row.style.display = '';
                } else {
                    row.style.display = 'none';
                }
            });
        });
    </script>
</x-app-layout>
