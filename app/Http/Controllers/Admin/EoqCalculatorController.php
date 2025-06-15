<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class EoqCalculatorController extends Controller
{
    public function index()
    {
        return view('Admin.eoq.eoq-calculator');
    }

    public function calculate(Request $request)
    {
        try {
            $request->validate([
                'product_name' => 'required|string|max:255',
                'product_unit' => 'required|string|max:50',
                'purchase_price' => 'required|numeric|min:0.01',
                'demand' => 'required|numeric|min:0.01',
                'order_cost' => 'required|numeric|min:0.01',
                'holding_cost_percentage' => 'required|numeric|min:0.01|max:100',
                'period' => 'required|in:weekly,monthly,yearly'
            ], [
                'purchase_price.min' => 'Harga beli harus lebih besar dari 0',
                'demand.min' => 'Jumlah permintaan harus lebih besar dari 0',
                'order_cost.min' => 'Biaya pemesanan harus lebih besar dari 0',
                'holding_cost_percentage.min' => 'Persentase biaya penyimpanan harus lebih besar dari 0',
                'holding_cost_percentage.max' => 'Persentase biaya penyimpanan tidak boleh lebih dari 100%'
            ]);

            // Konversi demand ke tahunan
            $annualDemand = match($request->period) {
                'weekly' => $request->demand * 52,
                'monthly' => $request->demand * 12,
                'yearly' => $request->demand
            };

            // Hitung holding cost per unit
            $holdingCostPerUnit = ($request->purchase_price * $request->holding_cost_percentage) / 100;

            // Hitung EOQ dengan pembulatan ke atas
            $eoq = ceil(sqrt((2 * $annualDemand * $request->order_cost) / $holdingCostPerUnit));

            // Hitung jumlah order per tahun
            $ordersPerYear = ceil($annualDemand / $eoq);

            // Hitung waktu antar order (dalam hari)
            $timeBetweenOrders = ceil(365 / $ordersPerYear);

            // Hitung total cost
            $orderingCost = $ordersPerYear * $request->order_cost;
            $holdingCost = ($eoq / 2) * $holdingCostPerUnit;
            $totalCost = $orderingCost + $holdingCost;

            return response()->json([
                'product' => [
                    'nama_produk' => $request->product_name,
                    'unit' => ['nama_satuan' => $request->product_unit]
                ],
                'eoq' => $eoq,
                'orders_per_year' => $ordersPerYear,
                'time_between_orders' => $timeBetweenOrders,
                'total_cost' => round($totalCost, 2),
                'ordering_cost' => round($orderingCost, 2),
                'holding_cost' => round($holdingCost, 2),
                'annual_demand' => round($annualDemand, 2),
                'holding_cost_per_unit' => round($holdingCostPerUnit, 2)
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'error' => 'Validasi gagal',
                'messages' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Terjadi kesalahan: ' . $e->getMessage()
            ], 500);
        }
    }
} 