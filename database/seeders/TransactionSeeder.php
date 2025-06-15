<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Transaction;
use App\Models\TransactionDetail;
use App\Models\Product;
use App\Models\User;
use App\Models\Customer;
use Carbon\Carbon;
use Faker\Factory as Faker;
use Illuminate\Support\Facades\DB;

class TransactionSeeder extends Seeder
{
    public function run()
    {
        $faker = Faker::create('id_ID');
        $users = User::all();
        $customers = Customer::all();
        $products = Product::all();
        
        // Hapus transaksi lama jika ada
        // Nonaktifkan foreign key checks sementara
        DB::statement('SET FOREIGN_KEY_CHECKS=0');
        
        // Hapus data dengan urutan yang benar
        TransactionDetail::truncate();
        Transaction::truncate();
        
        // Aktifkan kembali foreign key checks
        DB::statement('SET FOREIGN_KEY_CHECKS=1');

        // Generate transaksi selama 6 bulan
        $startDate = Carbon::now()->subMonths(6);
        $endDate = Carbon::now();

        // Pola penjualan per hari
        $dailyPatterns = [
            'Monday' => 0.8,    // 80% dari rata-rata
            'Tuesday' => 0.9,   // 90% dari rata-rata
            'Wednesday' => 1.0, // 100% dari rata-rata
            'Thursday' => 1.1,  // 110% dari rata-rata
            'Friday' => 1.3,    // 130% dari rata-rata
            'Saturday' => 1.5,  // 150% dari rata-rata
            'Sunday' => 0.7     // 70% dari rata-rata
        ];

        // Pola musiman (contoh: peningkatan penjualan di akhir bulan)
        $monthlyPatterns = [
            1 => 1.0,   // Januari
            2 => 0.9,   // Februari
            3 => 1.0,   // Maret
            4 => 1.1,   // April
            5 => 1.0,   // Mei
            6 => 1.2,   // Juni
            7 => 1.1,   // Juli
            8 => 1.0,   // Agustus
            9 => 1.2,   // September
            10 => 1.3,  // Oktober
            11 => 1.4,  // November
            12 => 1.5   // Desember
        ];

        $currentDate = $startDate;
        while ($currentDate <= $endDate) {
            // Tentukan jumlah transaksi per hari
            $dayOfWeek = $currentDate->format('l');
            $month = $currentDate->month;
            
            // Jumlah transaksi dasar per hari
            $baseTransactions = rand(5, 15);
            
            // Terapkan pola harian dan bulanan
            $dailyMultiplier = $dailyPatterns[$dayOfWeek] ?? 1.0;
            $monthlyMultiplier = $monthlyPatterns[$month] ?? 1.0;
            
            $transactionsPerDay = ceil($baseTransactions * $dailyMultiplier * $monthlyMultiplier);

            // Generate transaksi untuk hari ini
            for ($i = 0; $i < $transactionsPerDay; $i++) {
                // Buat transaksi
                $transaction = Transaction::create([
                    'kode_transaksi' => 'TRX-' . $currentDate->format('YmdHis') . rand(100, 999),
                    'user_id' => $users->random()->id,
                    'pelanggan_id' => $customers->random()->id,
                    'total_harga' => 0,
                    'total_bayar' => 0,
                    'total_kembali' => 0,
                    'metode_pembayaran' => collect(['cash', 'transfer', 'qris'])->random(),
                    'status' => 'selesai',
                    'catatan' => 'Transaksi test',
                    'created_at' => $currentDate->format('Y-m-d H:i:s'),
                    'updated_at' => $currentDate->format('Y-m-d H:i:s')
                ]);

                // Generate detail transaksi
                $total = 0;
                $numItems = rand(1, 5); // 1-5 item per transaksi
                
                for ($j = 0; $j < $numItems; $j++) {
                    $product = $products->random();
                    $quantity = rand(1, 5);
                    $price = $product->harga_jual;
                    $subtotal = $quantity * $price;
                    
                    TransactionDetail::create([
                        'transaksi_id' => $transaction->id,
                        'produk_id' => $product->id,
                        'jumlah' => $quantity,
                        'harga' => $price,
                        'subtotal' => $subtotal,
                        'created_at' => $currentDate->format('Y-m-d H:i:s'),
                        'updated_at' => $currentDate->format('Y-m-d H:i:s')
                    ]);

                    $total += $subtotal;
                }

                // Update total transaksi
                $transaction->update([
                    'total_harga' => $total,
                    'total_bayar' => $total,
                    'total_kembali' => 0
                ]);
            }

            // Pindah ke hari berikutnya
            $currentDate->addDay();
        }
    }
} 