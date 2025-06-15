<?php

namespace App\Http\Controllers\Admin;

use Carbon\Carbon;
use App\Models\Product;
use App\Models\Category;
use Milon\Barcode\DNS1D;
use App\Models\StockHistory;
use Illuminate\Http\Request;
use App\Models\StockAdjustment;
use Illuminate\Support\Facades\DB;
use App\Exports\StockForecastExport;
use App\Exports\StockMovementExport;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Barryvdh\DomPDF\Facade\PDF;
use Maatwebsite\Excel\Facades\Excel;
use App\Models\Notification;

class StockController extends Controller
{
    public function index(Request $request)
    {
        $query = Product::with(['category', 'unit'])
            ->when($request->search, function($q) use ($request) {
                return $q->where('nama_produk', 'like', "%{$request->search}%")
                    ->orWhere('kode_produk', 'like', "%{$request->search}%");
            })
            ->when($request->kategori_id, function($q) use ($request) {
                return $q->where('kategori_id', $request->kategori_id);
            })
            ->when($request->status_stok, function($q) use ($request) {
                if ($request->status_stok === 'low') {
                    return $q->where('stok', '<=', 10);
                } elseif ($request->status_stok === 'out') {
                    return $q->where('stok', 0);
                }
                return $q;
            });

        $products = $query->latest()->paginate(10);
        $categories = Category::all();

        return view('Admin.stocks.index', compact('products', 'categories'));
    }

    public function history(Request $request)
    {
        $query = StockHistory::with(['produk', 'user'])
            ->when($request->search, function($q) use ($request) {
                return $q->whereHas('produk', function($q) use ($request) {
                    $q->where('nama_produk', 'like', "%{$request->search}%")
                        ->orWhere('kode_produk', 'like', "%{$request->search}%");
                });
            })
            ->when($request->type, function($q) use ($request) {
                return $q->where('type', $request->type);
            })
            ->when($request->date_start && $request->date_end, function($q) use ($request) {
                return $q->whereBetween('created_at', [$request->date_start, $request->date_end]);
            });

        $histories = $query->latest()->paginate(10);

        return view('Admin.stocks.history', compact('histories'));
    }

    public function adjust(Request $request, Product $product)
    {
        $request->validate([
            'jenis' => 'required|in:masuk,keluar',
            'jumlah' => 'required|integer|min:1',
            'keterangan' => 'required|string|max:255'
        ]);

        try {
            DB::beginTransaction();

            $quantity = $request->jumlah;
            $type = $request->jenis;
            $oldStock = $product->stok;

            if ($type === 'keluar' && $quantity > $oldStock) {
                return response()->json([
                    'success' => false,
                    'message' => 'Jumlah pengurangan tidak boleh melebihi stok saat ini'
                ], 422);
            }

            $newStock = $type === 'masuk' ? $oldStock + $quantity : $oldStock - $quantity;
            
            $product->update(['stok' => $newStock]);

            // Catat history penyesuaian
            StockHistory::create([
                'produk_id' => $product->id,
                'jenis' => $type,
                'jumlah' => $quantity,
                'stok_lama' => $oldStock,
                'stok_baru' => $newStock,
                'keterangan' => $request->keterangan,
                'dibuat_oleh' => Auth::id()
            ]);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Stok berhasil disesuaikan'
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan saat menyesuaikan stok: ' . $e->getMessage()
            ], 500);
        }
    }

    public function report(Request $request)
    {
        $query = StockHistory::with(['produk', 'user']);

        // Filter berdasarkan tanggal
        if ($request->has('start_date') && $request->has('end_date')) {
            $query->whereBetween('created_at', [
                Carbon::parse($request->start_date)->startOfDay(),
                Carbon::parse($request->end_date)->endOfDay()
            ]);
        }

        // Filter berdasarkan produk
        if ($request->has('produk_id')) {
            $query->where('produk_id', $request->produk_id);
        }

        $histories = $query->get();

        if ($request->has('export')) {
            if ($request->export == 'excel') {
                return Excel::download(new StockMovementExport($histories), 'laporan-pergerakan-stok.xlsx');
            } else {
                $pdf = PDF::loadView('admin.stocks.report-pdf', compact('histories'));
                return $pdf->download('laporan-pergerakan-stok.pdf');
            }
        }

        return view('admin.stocks.report', compact('histories'));
    }

    public function forecast(Request $request)
    {
        $query = Product::with(['category', 'unit'])
            ->when($request->kategori_id, function($q) use ($request) {
                return $q->where('kategori_id', $request->kategori_id);
            });

        $products = $query->get();
        $categories = Category::all();

        // Hitung forecast untuk setiap produk
        foreach ($products as $product) {
            // Ambil data penjualan 24 minggu terakhir
            $weeklySales = $product->transactionDetails()
                ->whereBetween('created_at', [now()->subWeeks(52), now()])
                ->selectRaw('WEEK(created_at) as week, YEAR(created_at) as year, SUM(jumlah) as total')
                ->groupBy('year', 'week')
                ->orderBy('year')
                ->orderBy('week')
                ->get();

            // Hitung rata-rata penjualan mingguan
            $avgWeeklySales = $weeklySales->avg('total') ?? 0;

            // Hitung standar deviasi penjualan
            $stdDev = 0;
            if ($weeklySales->count() > 1) {
                $variance = $weeklySales->sum(function ($sale) use ($avgWeeklySales) {
                    return pow($sale->total - $avgWeeklySales, 2);
                }) / ($weeklySales->count() - 1);
                $stdDev = sqrt($variance);
            }

            // Hitung lead time (dalam hari)
            $leadTime = 7; // 7 hari

            // Hitung safety stock
            // Z-score 1.645 untuk service level 95%
            $serviceLevel = 1.645;
            // Lead time dalam hari, konversi ke minggu
            $leadTimeInWeeks = $leadTime / 7;
            
            // Jika stdDev = 0, gunakan 20% dari rata-rata penjualan mingguan sebagai safety stock
            if ($stdDev == 0) {
                $safetyStock = ceil($avgWeeklySales * 0.2);
            } else {
                $safetyStock = ceil($serviceLevel * $stdDev * sqrt($leadTimeInWeeks));
            }

            // Hitung reorder point
            // Reorder Point = (Rata-rata Penjualan Mingguan × Lead Time dalam minggu) + Safety Stock
            $reorderPoint = ceil(($avgWeeklySales * $leadTimeInWeeks) + $safetyStock);

            // Hitung EOQ (Economic Order Quantity)
            // EOQ = √((2 × D × S) / H)
            // D = Permintaan tahunan (rata-rata mingguan × 52)
            // S = Biaya pemesanan (Rp 100.000)
            // H = Biaya penyimpanan per unit per tahun (20% dari harga beli)
            $orderCost = 100000; // Biaya pemesanan
            $holdingCost = $product->harga_beli * 0.2; // 20% dari harga beli
            $annualDemand = $avgWeeklySales * 52;

            if ($holdingCost > 0) {
                $eoq = ceil(sqrt((2 * $annualDemand * $orderCost) / $holdingCost));
            } else {
                $eoq = ceil($avgWeeklySales * 2); // Fallback ke 2x rata-rata penjualan mingguan
            }

            // Hitung forecast minggu depan
            $nextWeekForecast = ceil($avgWeeklySales + $stdDev);

            // Analisis pola penjualan
            $salesPattern = 'Stabil';
            $patternDescription = '';
            
            if ($stdDev > 0) {
                $cv = ($stdDev / $avgWeeklySales) * 100; // Coefficient of Variation
                
                if ($cv < 20) {
                    $salesPattern = 'Stabil';
                    $patternDescription = 'Permintaan relatif stabil dengan variasi rendah';
                } elseif ($cv < 50) {
                    $salesPattern = 'Moderat';
                    $patternDescription = 'Permintaan cukup stabil dengan variasi sedang';
                } else {
                    $salesPattern = 'Tidak Stabil';
                    $patternDescription = 'Permintaan sangat bervariasi';
                }
            }

            // Status stok
            $stockStatus = 'Aman';
            $statusDescription = '';
            $needsReorder = false;
            
            if ($product->stok <= $reorderPoint) {
                $stockStatus = 'Perlu Reorder';
                $statusDescription = "Stok saat ini ({$product->stok} {$product->unit->nama_satuan}) sudah mencapai titik pemesanan ulang ({$reorderPoint} {$product->unit->nama_satuan})";
                $needsReorder = true;
                
                // Buat notifikasi jika perlu reorder
                $this->createReorderNotification($product, $reorderPoint, $eoq);
            } elseif ($product->stok <= $safetyStock) {
                $stockStatus = 'Rendah';
                $statusDescription = "Stok saat ini ({$product->stok} {$product->unit->nama_satuan}) sudah mendekati safety stock ({$safetyStock} {$product->unit->nama_satuan})";
            }

            $product->forecast = [
                'weekly_sales' => $weeklySales,
                'avg_weekly_sales' => $avgWeeklySales,
                'std_dev' => $stdDev,
                'lead_time' => $leadTime,
                'safety_stock' => $safetyStock,
                'reorder_point' => $reorderPoint,
                'eoq' => $eoq,
                'trend' => 0,
                'next_week_forecast' => $nextWeekForecast,
                'sales_pattern' => $salesPattern,
                'pattern_description' => $patternDescription,
                'stock_status' => $stockStatus,
                'status_description' => $statusDescription,
                'needs_reorder' => $needsReorder
            ];
        }

        if ($request->has('export')) {
            return Excel::download(new StockForecastExport($products), 'forecast-stok.xlsx');
        }

        return view('Admin.stocks.forecast', compact('products', 'categories'));
    }

    private function createReorderNotification($product, $reorderPoint, $eoq)
    {
        // Cek apakah sudah ada notifikasi yang sama dalam 24 jam terakhir
        $existingNotification = Notification::where('jenis', 'stock_alert')
            ->where('detail->produk_id', $product->id)
            ->where('created_at', '>=', now()->subHours(24))
            ->first();

        if (!$existingNotification) {
            Notification::create([
                'user_id' => Auth::id(),
                'judul' => 'Perlu Reorder Stok',
                'pesan' => "Stok {$product->nama} saat ini ({$product->stok} {$product->unit->nama_satuan}) sudah mencapai titik pemesanan ulang ({$reorderPoint} {$product->unit->nama_satuan}). Disarankan untuk melakukan pembelian sebesar {$eoq} {$product->unit->nama_satuan}.",
                'jenis' => 'stock_alert',
                'detail' => [
                    'produk_id' => $product->id,
                    'stok_saat_ini' => $product->stok,
                    'reorder_point' => $reorderPoint,
                    'eoq' => $eoq,
                    'unit' => $product->unit->nama_satuan
                ],
                'link' => route('admin.stocks.forecast', $product->id)
            ]);
        }
    }

    public function generateBarcode($id)
    {
        $product = Product::findOrFail($id);
        $barcode = new DNS1D();
        $barcode->setStorPath(storage_path('app/public/barcodes'));
        
        $barcodeImage = $barcode->getBarcodePNG($product->code, 'C128');
        
        return response($barcodeImage)
            ->header('Content-Type', 'image/png');
    }

    public function printBarcode($id)
    {
        $product = Product::findOrFail($id);
        return view('admin.stocks.barcode', compact('product'));
    }

    public function printBulkBarcode(Request $request)
    {
        $request->validate([
            'produk_ids' => 'required|array',
            'produk_ids.*' => 'exists:products,id'
        ]);

        $products = Product::whereIn('id', $request->produk_ids)->get();
        return view('admin.stocks.bulk-barcode', compact('products'));
    }
}
