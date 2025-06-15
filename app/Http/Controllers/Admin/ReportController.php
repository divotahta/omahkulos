<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Transaction;
use App\Models\TransactionDetail;
use App\Models\Purchase;
use App\Models\PurchaseDetail;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class ReportController extends Controller
{
    public function index(Request $request)
    {
        // Filter tanggal
        $startDate = $request->start_date ? Carbon::parse($request->start_date) : Carbon::now()->startOfMonth();
        $endDate = $request->end_date ? Carbon::parse($request->end_date) : Carbon::now()->endOfMonth();

        // Ringkasan Penjualan
        $salesSummary = Transaction::whereBetween('created_at', [$startDate, $endDate])
            ->select(
                DB::raw('COUNT(*) as total_transactions'),
                DB::raw('SUM(total_harga) as total_sales'),
                DB::raw('AVG(total_harga) as average_sales')
            )
            ->first();

        // Ringkasan Pembelian
        $purchaseSummary = DB::table('purchases')
            ->leftJoin('purchase_details', 'purchases.id', '=', 'purchase_details.pembelian_id')
            ->whereBetween('purchases.created_at', [$startDate, $endDate])
            ->whereNull('purchases.deleted_at')
            ->select(
                DB::raw('COUNT(DISTINCT purchases.id) as total_purchases'),
                DB::raw('SUM(purchase_details.total) as total_purchases_amount'),
                DB::raw('AVG(purchase_details.total) as average_purchase')
            )
            ->first();

        // Produk Terlaris
        $topProducts = TransactionDetail::whereBetween('created_at', [$startDate, $endDate])
            ->select('produk_id', DB::raw('SUM(jumlah) as total_quantity'))
            ->with('product')
            ->groupBy('produk_id')
            ->orderBy('total_quantity', 'desc')
            ->limit(5)
            ->get();

        // Grafik Penjualan Harian
        $dailySales = Transaction::whereBetween('created_at', [$startDate, $endDate])
            ->select(
                DB::raw('DATE(created_at) as date'),
                DB::raw('SUM(total_harga) as total_sales'),
                DB::raw('COUNT(*) as total_transactions')
            )
            ->groupBy('date')
            ->orderBy('date')
            ->get();

        // Grafik Pembelian Harian
        $dailyPurchases = DB::table('purchases')
            ->leftJoin('purchase_details', 'purchases.id', '=', 'purchase_details.pembelian_id')
            ->whereBetween('purchases.created_at', [$startDate, $endDate])
            ->whereNull('purchases.deleted_at')
            ->select(
                DB::raw('DATE(purchases.created_at) as date'),
                DB::raw('SUM(purchase_details.total) as total_purchases'),
                DB::raw('COUNT(DISTINCT purchases.id) as total_purchases_count')
            )
            ->groupBy('date')
            ->orderBy('date')
            ->get();

        // Metode Pembayaran
        $paymentMethods = Transaction::whereBetween('created_at', [$startDate, $endDate])
            ->select('metode_pembayaran', DB::raw('COUNT(*) as total'))
            ->groupBy('metode_pembayaran')
            ->get();

        return view('Admin.reports.index', compact(
            'startDate',
            'endDate',
            'salesSummary',
            'purchaseSummary',
            'topProducts',
            'dailySales',
            'dailyPurchases',
            'paymentMethods'
        ));
    }
} 