<x-app-layout>
    <x-slot name="header">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Tambah Pembelian Bahan Baku') }}
            </h2>
    </x-slot>

    <div class="py-12">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <a href="{{ route('admin.purchases.index') }}"
                class="inline-flex items-center px-4 py-2 bg-gray-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 focus:bg-gray-700 active:bg-gray-900 focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-offset-2 transition">
                <i class="fas fa-arrow-left mr-2"></i>
                Kembali
        </a>
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
            <div class="p-6 bg-white border-b border-gray-200">
                <form action="{{ route('admin.purchases.store') }}" method="POST" id="purchaseForm">
                    @csrf
                        <div class="mb-4">
                            <label for="supplier_id" class="block text-sm font-medium text-gray-700">Supplier</label>
                            <select name="supplier_id" id="supplier_id"
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                                required>
                                <option value="">Pilih Supplier</option>
                                @foreach ($suppliers as $supplier)
                                    <option value="{{ $supplier->id }}">{{ $supplier->nama }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="mb-4">
                            <label for="tanggal_pembelian" class="block text-sm font-medium text-gray-700">Tanggal
                                Pembelian</label>
                            <input type="date" name="tanggal_pembelian" id="tanggal_pembelian"
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                                required>
                        </div>
                        <div class="mb-4">
                        <label for="catatan" class="block text-sm font-medium text-gray-700">Catatan</label>
                            <textarea name="catatan" id="catatan" rows="2"
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"></textarea>
                        </div>
                        <div class="mb-4">
                            <h3 class="text-lg font-semibold mb-2">Detail Produk</h3>
                            <table class="min-w-full mb-2">
                                <thead>
                                    <tr>
                                        <th class="px-2 py-1 text-left text-xs font-medium text-gray-500 uppercase">
                                            Produk</th>
                                        <th class="px-2 py-1 text-left text-xs font-medium text-gray-500 uppercase">
                                            Jumlah</th>
                                        <th class="px-2 py-1 text-left text-xs font-medium text-gray-500 uppercase">
                                            Harga Satuan</th>
                                        <th class="px-2 py-1 text-left text-xs font-medium text-gray-500 uppercase">
                                            Total</th>
                                        <th class="px-2 py-1 text-left text-xs font-medium text-gray-500 uppercase">
                                            Expired Date</th>
                                        <th class="px-2 py-1"></th>
                                    </tr>
                                </thead>
                                <tbody id="produk-table">
                                    <tr>
                                        <td class="px-2 py-1">
                                            <select name="produk[0][raw_material_id]"
                                                class="raw-material-select mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                                                required>
                                                <option value="">Pilih Bahan</option>
                                                @foreach ($rawMaterials as $rawMaterial)
                                                    <option value="{{ $rawMaterial->id }}"
                                                        data-harga="{{ $rawMaterial->harga }}"
                                                        data-expired="{{ \Carbon\Carbon::parse($rawMaterial->expired_date)->format('Y-m-d') }}">
                                                        {{ $rawMaterial->nama }} ({{ $rawMaterial->kode }})</option>
                                                @endforeach
                                            </select>
                                        </td>
                                        <td class="px-2 py-1">
                                            <input type="number" name="produk[0][jumlah]"
                                                class="jumlah-input mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                                                min="1" value="1" required>
                                        </td>
                                        <td class="px-2 py-1">
                                            <input type="number" name="produk[0][harga]"
                                                class="harga-input mt-1 block w-full rounded-md border-gray-300 shadow-sm bg-gray-100"
                                                min="0" required readonly>
                                        </td>
                                        <td class="px-2 py-1">
                                            <input type="text"
                                                class="total-input mt-1 block w-full rounded-md border-gray-300 shadow-sm bg-gray-100"
                                                value="0" readonly>
                                        </td>
                                        <td class="px-2 py-1">
                                            <input type="date" name="produk[0][expired_date]"
                                                class="expired-input mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                                                required readonly>
                                        </td>
                                        <td class="px-2 py-1 text-center">
                                            <button type="button" class="hapus-row text-red-600 hover:text-red-900"><i
                                                    class="fas fa-trash"></i></button>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                            <button type="button" id="tambah-produk"
                                class="bg-purple-500 hover:bg-purple-700 text-white font-bold py-1 px-3 rounded mb-2"><i
                                    class="fas fa-plus"></i> Tambah Produk</button>
                            <div class="text-right font-semibold mt-2">
                                Total Keseluruhan: <input type="text" id="grand-total"
                                    class="bg-gray-100 w-32 text-right rounded px-2" value="0.00" readonly>
                            </div>
                        </div>
                        <div class="flex items-center justify-end mt-4">
                        <button type="submit"
                                class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                            Simpan
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    </div>
    {{-- <script>
        let rowIdx = 1;
        const rawMaterialCodes = [
            @foreach ($rawMaterials as $rm)
                '{{ $rm->kode }}',
            @endforeach
        ];

        function hitungTotalRow(row) {
            const jumlah = parseFloat(row.querySelector('.jumlah-input').value) || 0;
            const harga = parseFloat(row.querySelector('.harga-input').value) || 0;
            row.querySelector('.total-input').value = (jumlah * harga).toFixed(2);
            console.log(`Hitung: ${jumlah} x ${harga} = ${jumlah * harga}`);

        }

        function hitungGrandTotal() {
            let total = 0;
            document.querySelectorAll('.total-input').forEach(input => {
                total += parseFloat(input.value) || 0;
            });
            document.getElementById('grand-total').value = total.toFixed(2);
        }

        document.getElementById('produk-table').addEventListener('input', function(e) {
            if (e.target.classList.contains('raw-material-select')) {
                const row = e.target.closest('tr');
                console.log('Jumlah atau harga berubah');

                const hargaInput = row.querySelector('.harga-input');
                const expiredInput = row.querySelector('.expired-input');
                const selected = e.target.options[e.target.selectedIndex];
                hargaInput.value = selected.getAttribute('data-harga') || '';
                expiredInput.value = selected.getAttribute('data-expired') || '';
                hitungTotalRow(row);
                hitungGrandTotal();
            }
            // ✅ Tambahkan ini untuk jumlah dan harga
            if (e.target.classList.contains('jumlah-input') || e.target.classList.contains('harga-input')) {
                const row = e.target.closest('tr');
                console.log('Jumlah atau harga berubah');
                hitungTotalRow(row);
                hitungGrandTotal();
            }

        });
        document.getElementById('tambah-produk').addEventListener('click', function() {
            const tbody = document.getElementById('produk-table');
            const newRow = document.createElement('tr');
            newRow.innerHTML = `
                <td class="px-2 py-1">
                    <select name="produk[${rowIdx}][raw_material_id]" class="raw-material-select mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500" required>
                        <option value="">Pilih Bahan</option>
                        @foreach ($rawMaterials as $rawMaterial)
                            <option value="{{ $rawMaterial->id }}" data-harga="{{ $rawMaterial->harga }}" data-expired="{{ \Carbon\Carbon::parse($rawMaterial->expired_date)->format('Y-m-d') }}">{{ $rawMaterial->nama }} ({{ $rawMaterial->kode }})</option>
                        @endforeach
                    </select>
                </td>
                <td class="px-2 py-1">
                    <input type="number" name="produk[${rowIdx}][jumlah]" class="jumlah-input mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500" min="1" value="1" required>
                </td>
                <td class="px-2 py-1">
                    <input type="number" name="produk[${rowIdx}][harga]" class="harga-input mt-1 block w-full rounded-md border-gray-300 shadow-sm bg-gray-100" min="0" required readonly>
                </td>
                <td class="px-2 py-1">
                    <input type="text" class="total-input mt-1 block w-full rounded-md border-gray-300 shadow-sm bg-gray-100" value="0" readonly>
                </td>
                <td class="px-2 py-1">
                    <input type="date" name="produk[${rowIdx}][expired_date]" class="expired-input mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500" required readonly>
                </td>
                <td class="px-2 py-1 text-center">
                    <button type="button" class="hapus-row text-red-600 hover:text-red-900"><i class="fas fa-trash"></i></button>
                </td>
            `;
            tbody.appendChild(newRow);
            rowIdx++;
        });
        document.getElementById('produk-table').addEventListener('click', function(e) {
            if (e.target.closest('.hapus-row')) {
                const row = e.target.closest('tr');
                if (document.querySelectorAll('#produk-table tr').length > 1) {
                    row.remove();
                    hitungGrandTotal();
                } else {
                    alert('Minimal satu produk!');
                }
            }
        });
        // Hitung total awal
        document.addEventListener('DOMContentLoaded', function() {
            document.querySelectorAll('.total-input').forEach(input => input.value = '0');
            hitungGrandTotal();
        });
    </script> --}}
    <script>
        let rowIdx = 1;
    
        // Fungsi format angka ke Rupiah
        function formatRupiah(angka) {
            return 'Rp ' + angka.toFixed(0).replace(/\B(?=(\d{3})+(?!\d))/g, '.');
        }
    
        // Hitung total per baris
        function hitungTotalRow(row) {
            const jumlah = parseFloat(row.querySelector('.jumlah-input').value) || 0;
            const harga = parseFloat(row.querySelector('.harga-input').value) || 0;
            const total = jumlah * harga;
            row.querySelector('.total-input').value = formatRupiah(total);
            hitungGrandTotal();
        }
    
        // Hitung total keseluruhan
        function hitungGrandTotal() {
            let total = 0;
            document.querySelectorAll('.total-input').forEach(input => {
                const angka = parseFloat(input.value.replace(/[^\d]/g, '')) || 0;
                total += angka;
            });
            document.getElementById('grand-total').value = formatRupiah(total);
        }
    
        // Saat isi jumlah atau pilih produk berubah
        document.getElementById('produk-table').addEventListener('input', function (e) {
                    const row = e.target.closest('tr');
    
            if (e.target.classList.contains('raw-material-select')) {
                const selected = e.target.options[e.target.selectedIndex];
                const hargaInput = row.querySelector('.harga-input');
                const expiredInput = row.querySelector('.expired-input');
    
                hargaInput.value = selected.getAttribute('data-harga') || '';
                expiredInput.value = selected.getAttribute('data-expired') || '';
                hitungTotalRow(row);
            }
    
            if (e.target.classList.contains('jumlah-input')) {
                hitungTotalRow(row);
            }
        });
    
        // Tambah produk
        document.getElementById('tambah-produk').addEventListener('click', function () {
            const tbody = document.getElementById('produk-table');
            const newRow = document.createElement('tr');
    
            newRow.innerHTML = `
                <td class="px-2 py-1">
                    <select name="produk[${rowIdx}][raw_material_id]" class="raw-material-select mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500" required>
                        <option value="">Pilih Bahan</option>
                        @foreach ($rawMaterials as $rawMaterial)
                            <option value="{{ $rawMaterial->id }}" data-harga="{{ $rawMaterial->harga }}" data-expired="{{ \Carbon\Carbon::parse($rawMaterial->expired_date)->format('Y-m-d') }}">{{ $rawMaterial->nama }} ({{ $rawMaterial->kode }})</option>
                        @endforeach
                    </select>
                </td>
                <td class="px-2 py-1">
                    <input type="number" name="produk[${rowIdx}][jumlah]" class="jumlah-input mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500" min="1" value="1" required>
                                        </td>
                <td class="px-2 py-1">
                    <input type="number" name="produk[${rowIdx}][harga]" class="harga-input mt-1 block w-full rounded-md border-gray-300 shadow-sm bg-gray-100" min="0" required readonly>
                </td>
                <td class="px-2 py-1">
                    <input type="text" class="total-input mt-1 block w-full rounded-md border-gray-300 shadow-sm bg-gray-100" value="Rp 0" readonly>
                </td>
                <td class="px-2 py-1">
                    <input type="date" name="produk[${rowIdx}][expired_date]" class="expired-input mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500" required readonly>
                </td>
                <td class="px-2 py-1 text-center">
                    <button type="button" class="hapus-row text-red-600 hover:text-red-900"><i class="fas fa-trash"></i></button>
                </td>
            `;
    
            tbody.appendChild(newRow);
            rowIdx++;
        });
    
        // Hapus baris produk
        document.getElementById('produk-table').addEventListener('click', function (e) {
            if (e.target.closest('.hapus-row')) {
                const row = e.target.closest('tr');
                if (document.querySelectorAll('#produk-table tr').length > 1) {
                    row.remove();
                    hitungGrandTotal();
                    } else {
                    alert('Minimal satu produk!');
                }
            }
        });
    
        // Hitung total awal saat halaman dimuat
        document.addEventListener('DOMContentLoaded', function () {
            document.querySelectorAll('.total-input').forEach(input => input.value = formatRupiah(0));
            hitungGrandTotal();
        });
    </script>
    
</x-app-layout>
