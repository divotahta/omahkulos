<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\RawMaterial;
use Carbon\Carbon;

class RawMaterialSeeder extends Seeder
{
    public function run(): void
    {
        RawMaterial::insert([
            [ 'nama' => 'Tepung Terigu', 'kode' => 'BB-001', 'satuan' => 'kg', 'supplier_id' => 1, 'stok' => 100, 'harga' => 12000, 'expired_date' => Carbon::now()->addMonths(6), 'created_at' => now(), 'updated_at' => now(), ],
            [ 'nama' => 'Gula Pasir', 'kode' => 'BB-002', 'satuan' => 'kg', 'supplier_id' => 1, 'stok' => 80, 'harga' => 14000, 'expired_date' => Carbon::now()->addMonths(8), 'created_at' => now(), 'updated_at' => now(), ],
            [ 'nama' => 'Minyak Goreng', 'kode' => 'BB-003', 'satuan' => 'liter', 'supplier_id' => 2, 'stok' => 50, 'harga' => 20000, 'expired_date' => Carbon::now()->addMonths(4), 'created_at' => now(), 'updated_at' => now(), ],
            [ 'nama' => 'Garam', 'kode' => 'BB-004', 'satuan' => 'kg', 'supplier_id' => 2, 'stok' => 60, 'harga' => 5000, 'expired_date' => Carbon::now()->addMonths(12), 'created_at' => now(), 'updated_at' => now(), ],
            [ 'nama' => 'Ragi', 'kode' => 'BB-005', 'satuan' => 'gram', 'supplier_id' => 3, 'stok' => 30, 'harga' => 2500, 'expired_date' => Carbon::now()->addMonths(10), 'created_at' => now(), 'updated_at' => now(), ],
            [ 'nama' => 'Susu Bubuk', 'kode' => 'BB-006', 'satuan' => 'kg', 'supplier_id' => 3, 'stok' => 20, 'harga' => 35000, 'expired_date' => Carbon::now()->addMonths(7), 'created_at' => now(), 'updated_at' => now(), ],
            [ 'nama' => 'Coklat Bubuk', 'kode' => 'BB-007', 'satuan' => 'kg', 'supplier_id' => 4, 'stok' => 15, 'harga' => 40000, 'expired_date' => Carbon::now()->addMonths(9), 'created_at' => now(), 'updated_at' => now(), ],
            [ 'nama' => 'Keju Parut', 'kode' => 'BB-008', 'satuan' => 'kg', 'supplier_id' => 4, 'stok' => 10, 'harga' => 60000, 'expired_date' => Carbon::now()->addMonths(5), 'created_at' => now(), 'updated_at' => now(), ],
            [ 'nama' => 'Mentega', 'kode' => 'BB-009', 'satuan' => 'kg', 'supplier_id' => 5, 'stok' => 25, 'harga' => 25000, 'expired_date' => Carbon::now()->addMonths(6), 'created_at' => now(), 'updated_at' => now(), ],
            [ 'nama' => 'Susu Cair', 'kode' => 'BB-010', 'satuan' => 'liter', 'supplier_id' => 5, 'stok' => 40, 'harga' => 18000, 'expired_date' => Carbon::now()->addMonths(3), 'created_at' => now(), 'updated_at' => now(), ],
            [ 'nama' => 'Kacang Tanah', 'kode' => 'BB-011', 'satuan' => 'kg', 'supplier_id' => 6, 'stok' => 35, 'harga' => 22000, 'expired_date' => Carbon::now()->addMonths(8), 'created_at' => now(), 'updated_at' => now(), ],
            [ 'nama' => 'Kismis', 'kode' => 'BB-012', 'satuan' => 'kg', 'supplier_id' => 6, 'stok' => 12, 'harga' => 45000, 'expired_date' => Carbon::now()->addMonths(7), 'created_at' => now(), 'updated_at' => now(), ],
            [ 'nama' => 'Vanili', 'kode' => 'BB-013', 'satuan' => 'gram', 'supplier_id' => 7, 'stok' => 8, 'harga' => 3000, 'expired_date' => Carbon::now()->addMonths(10), 'created_at' => now(), 'updated_at' => now(), ],
            [ 'nama' => 'Pewarna Makanan', 'kode' => 'BB-014', 'satuan' => 'ml', 'supplier_id' => 7, 'stok' => 18, 'harga' => 7000, 'expired_date' => Carbon::now()->addMonths(12), 'created_at' => now(), 'updated_at' => now(), ],
            [ 'nama' => 'Pasta Pandan', 'kode' => 'BB-015', 'satuan' => 'ml', 'supplier_id' => 8, 'stok' => 14, 'harga' => 8000, 'expired_date' => Carbon::now()->addMonths(11), 'created_at' => now(), 'updated_at' => now(), ],
            [ 'nama' => 'Pasta Coklat', 'kode' => 'BB-016', 'satuan' => 'ml', 'supplier_id' => 8, 'stok' => 16, 'harga' => 8500, 'expired_date' => Carbon::now()->addMonths(11), 'created_at' => now(), 'updated_at' => now(), ],
            [ 'nama' => 'Kacang Mede', 'kode' => 'BB-017', 'satuan' => 'kg', 'supplier_id' => 9, 'stok' => 9, 'harga' => 90000, 'expired_date' => Carbon::now()->addMonths(8), 'created_at' => now(), 'updated_at' => now(), ],
            [ 'nama' => 'Almond', 'kode' => 'BB-018', 'satuan' => 'kg', 'supplier_id' => 9, 'stok' => 7, 'harga' => 95000, 'expired_date' => Carbon::now()->addMonths(8), 'created_at' => now(), 'updated_at' => now(), ],
            [ 'nama' => 'Susu Kental Manis', 'kode' => 'BB-019', 'satuan' => 'kaleng', 'supplier_id' => 10, 'stok' => 20, 'harga' => 12000, 'expired_date' => Carbon::now()->addMonths(6), 'created_at' => now(), 'updated_at' => now(), ],
            [ 'nama' => 'Coklat Batang', 'kode' => 'BB-020', 'satuan' => 'batang', 'supplier_id' => 10, 'stok' => 13, 'harga' => 15000, 'expired_date' => Carbon::now()->addMonths(9), 'created_at' => now(), 'updated_at' => now(), ],
        ]);
    }
} 