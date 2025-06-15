<?php

namespace App\Http\Controllers\Admin;

use App\Models\Product;
use App\Models\Purchase;
use App\Models\Supplier;
use App\Models\PurchaseDetail;
use App\Models\StockHistory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use App\Models\Notification;
use Illuminate\Support\Facades\Log;
use App\Models\RawMaterial;
use Illuminate\Support\Str;

class PurchaseController extends Controller
{
    public function index()
    {
        $purchases = Purchase::with(['supplier', 'details.rawMaterial'])->paginate(10);
        return view('Admin.purchases.index', compact('purchases'));
    }

    public function create()
    {
        $suppliers = Supplier::all();
        $rawMaterials = RawMaterial::all();
        return view('Admin.purchases.create', compact('suppliers', 'rawMaterials'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'supplier_id' => 'required|exists:suppliers,id',
            'tanggal_pembelian' => 'required|date',
            'produk' => 'required|array|min:1',
            'produk.*.raw_material_id' => 'required|exists:raw_materials,id',
            'produk.*.jumlah' => 'required|numeric|min:1',
            'produk.*.harga' => 'required|numeric|min:0',
        ]);

        DB::beginTransaction();
        try {
            $purchaseModel = new Purchase();
            $nomorPembelian = $purchaseModel->generateInvoiceNumber();
            $purchase = Purchase::create([
                'pemasok_id' => $request->supplier_id,
                'tanggal_pembelian' => $request->tanggal_pembelian,
                'nomor_pembelian' => $nomorPembelian,
                'total_amount' => 0,
                'status_pembelian' => 'pending',
                'catatan' => $request->catatan,
                'dibuat_oleh' => Auth::id(),
            ]);

            $totalAmount = 0;
            foreach ($request->produk as $produk) {
                $rawMaterial = RawMaterial::findOrFail($produk['raw_material_id']);
                $total = $produk['jumlah'] * $produk['harga'];
                $totalAmount += $total;
                PurchaseDetail::create([
                    'pembelian_id' => $purchase->id,
                    'raw_material_id' => $rawMaterial->id,
                    'nama' => $rawMaterial->nama,
                    'jumlah' => $produk['jumlah'],
                    'harga' => $produk['harga'],
                    'total' => $total,
                    'catatan' => $request->catatan ?? null,
                ]);
            }
            $purchase->update(['total_amount' => $totalAmount]);
            DB::commit();
            return redirect()->route('admin.purchases.index')->with('success', 'Pembelian berhasil ditambahkan.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    public function edit($id)
    {
        $purchase = Purchase::with(['details.rawMaterial', 'supplier'])->findOrFail($id);
        if (in_array($purchase->status_pembelian, ['approved', 'received'])) {
            return redirect()->route('admin.purchases.index')->with('error', 'Pembelian dengan status approved atau received tidak dapat diedit.');
        }
        $suppliers = Supplier::all();
        $rawMaterials = RawMaterial::all();
        return view('Admin.purchases.edit', compact('purchase', 'suppliers', 'rawMaterials'));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'supplier_id' => 'required|exists:suppliers,id',
            'tanggal_pembelian' => 'required|date',
            'produk' => 'required|array|min:1',
            'produk.*.raw_material_id' => 'required|exists:raw_materials,id',
            'produk.*.jumlah' => 'required|numeric|min:1',
            'produk.*.harga' => 'required|numeric|min:0',
            'catatan' => 'nullable|string',
            'status_pembelian' => 'required|in:pending,approved,rejected,received',
        ]);

        DB::beginTransaction();
        try {
            $purchase = Purchase::findOrFail($id);
            $oldStatus = $purchase->status_pembelian;

            // Update data purchase
            $purchase->update([
                'pemasok_id' => $request->supplier_id,
                'tanggal_pembelian' => $request->tanggal_pembelian,
                'catatan' => $request->catatan,
                'status_pembelian' => $request->status_pembelian,
                'dibuat_oleh' => Auth::id(),
            ]);

            // Hapus semua detail lama
            PurchaseDetail::where('pembelian_id', $purchase->id)->delete();

            // Insert detail baru
            $totalAmount = 0;
            foreach ($request->produk as $produk) {
                $rawMaterial = RawMaterial::findOrFail($produk['raw_material_id']);
                $total = $produk['jumlah'] * $produk['harga'];
                $totalAmount += $total;
                PurchaseDetail::create([
                    'pembelian_id' => $purchase->id,
                    'raw_material_id' => $rawMaterial->id,
                    'nama' => $rawMaterial->nama,
                    'jumlah' => $produk['jumlah'],
                    'harga' => $produk['harga'],
                    'total' => $total,
                    'catatan' => $request->catatan ?? null,
                ]);
            }

            // Update total amount
            $purchase->update(['total_amount' => $totalAmount]);

            // Jika status berubah menjadi received, update stok
            if ($request->status_pembelian === 'received' && $oldStatus !== 'received') {
                foreach ($request->produk as $produk) {
                    $rawMaterial = RawMaterial::findOrFail($produk['raw_material_id']);
                    $rawMaterial->update([
                        'stok' => $rawMaterial->stok + $produk['jumlah']
                    ]);
                }
            }

            DB::commit();
            return redirect()->route('admin.purchases.index')->with('success', 'Pembelian berhasil diperbarui.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    public function destroy(Purchase $purchase)
    {
        $purchase->delete();
        return redirect()->route('admin.purchases.index')->with('success', 'Pembelian berhasil dihapus.');
    }

    public function approve(Purchase $purchase)
    {
        try {
            Log::info('Memulai proses persetujuan pembelian', [
                'purchase_id' => $purchase->id,
                'user_id' => Auth::id()
            ]);

            // Cek apakah pembelian masih pending
            if ($purchase->status_pembelian !== 'pending') {
                Log::warning('Status pembelian tidak valid untuk disetujui', [
                    'purchase_id' => $purchase->id,
                    'current_status' => $purchase->status_pembelian
                ]);
                return redirect()->route('admin.purchases.index')
                    ->with('error', 'Pembelian ini tidak dapat disetujui karena statusnya bukan pending.');
            }

            DB::beginTransaction();
            Log::info('Memulai transaksi database');

            try {
                // Update status pembelian
                $updateResult = $purchase->update([
                    'status_pembelian' => 'approved',
                ]);

                if (!$updateResult) {
                    throw new \Exception('Gagal mengupdate status pembelian');
                }

                Log::info('Status pembelian berhasil diupdate', [
                    'purchase_id' => $purchase->id,
                    'new_status' => 'approved'
                ]);

                // Buat notifikasi untuk admin
                try {
                    $notification = Notification::create([
                        'user_id' => Auth::id(),
                        'judul' => 'Pembelian Disetujui',
                        'pesan' => "Pembelian #{$purchase->nomor_pembelian} dari {$purchase->supplier->nama_supplier} telah disetujui oleh " . Auth::user()->name,
                        'jenis' => 'purchase_approved',
                        'dibaca' => false,
                        'detail' => [
                            'purchase_id' => $purchase->id,
                            'nomor_pembelian' => $purchase->nomor_pembelian,
                            'supplier' => $purchase->supplier->nama_supplier
                        ],
                        'link' => route('admin.purchases.show', $purchase->id)
                    ]);
                    Log::info('Notifikasi berhasil dibuat', ['notification_id' => $notification->id]);
                } catch (\Exception $e) {
                    Log::warning('Gagal membuat notifikasi', [
                        'error' => $e->getMessage(),
                        'purchase_id' => $purchase->id
                    ]);
                }

                DB::commit();
                Log::info('Transaksi berhasil diselesaikan', ['purchase_id' => $purchase->id]);

                return redirect()->route('admin.purchases.index')
                    ->with('success', 'Pembelian berhasil disetujui.');
            } catch (\Exception $e) {
                DB::rollBack();
                Log::error('Error dalam transaksi database', [
                    'purchase_id' => $purchase->id,
                    'error' => $e->getMessage(),
                    'trace' => $e->getTraceAsString()
                ]);
                throw $e;
            }
        } catch (\Exception $e) {
            Log::error('Error dalam proses persetujuan pembelian', [
                'purchase_id' => $purchase->id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return redirect()->route('admin.purchases.index')
                ->with('error', 'Terjadi kesalahan saat menyetujui pembelian: ' . $e->getMessage());
        }
    }

    public function reject(Request $request, Purchase $purchase)
    {
        try {
            Log::info('Memulai proses penolakan pembelian', [
                'purchase_id' => $purchase->id,
                'user_id' => Auth::id(),
                'request_data' => $request->all()
            ]);

            // Cek apakah pembelian masih pending
            if ($purchase->status_pembelian !== 'pending') {
                Log::warning('Status pembelian tidak valid untuk ditolak', [
                    'purchase_id' => $purchase->id,
                    'current_status' => $purchase->status_pembelian
                ]);
                return redirect()->route('admin.purchases.index')
                    ->with('error', 'Pembelian ini tidak dapat ditolak karena statusnya bukan pending.');
            }

            DB::beginTransaction();
            Log::info('Memulai transaksi database');

            try {
                // Update status pembelian
                $updateResult = $purchase->update([
                    'status_pembelian' => 'rejected',
                ]);

                if (!$updateResult) {
                    throw new \Exception('Gagal mengupdate status pembelian');
                }

                Log::info('Status pembelian berhasil diupdate', [
                    'purchase_id' => $purchase->id,
                    'new_status' => 'rejected'
                ]);

                // Buat notifikasi untuk admin
                try {
                    $notification = Notification::create([
                        'user_id' => Auth::id(),
                        'judul' => 'Pembelian Ditolak',
                        'pesan' => "Pembelian #{$purchase->nomor_pembelian} dari {$purchase->supplier->nama_supplier} telah ditolak oleh " . Auth::user()->nama,
                        'jenis' => 'purchase_rejected',
                        'dibaca' => false,
                        'detail' => [
                            'purchase_id' => $purchase->id,
                            'nomor_pembelian' => $purchase->nomor_pembelian,
                            'supplier' => $purchase->supplier->nama_supplier,
                        ],
                        'link' => route('admin.purchases.show', $purchase->id)
                    ]);
                    Log::info('Notifikasi berhasil dibuat', ['notification_id' => $notification->id]);
                } catch (\Exception $e) {
                    Log::warning('Gagal membuat notifikasi', [
                        'error' => $e->getMessage(),
                        'purchase_id' => $purchase->id
                    ]);
                }

                DB::commit();
                Log::info('Transaksi berhasil diselesaikan', ['purchase_id' => $purchase->id]);

                return redirect()->route('admin.purchases.index')
                    ->with('success', 'Pembelian berhasil ditolak.');
            } catch (\Exception $e) {
                DB::rollBack();
                Log::error('Error dalam transaksi database', [
                    'purchase_id' => $purchase->id,
                    'error' => $e->getMessage(),
                    'trace' => $e->getTraceAsString()
                ]);
                throw $e;
            }
        } catch (\Exception $e) {
            Log::error('Error dalam proses penolakan pembelian', [
                'purchase_id' => $purchase->id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return redirect()->route('admin.purchases.index')
                ->with('error', 'Terjadi kesalahan saat menolak pembelian: ' . $e->getMessage());
        }
    }

    public function receive($id)
    {
        DB::beginTransaction();
        try {
            $purchase = Purchase::with('details')->findOrFail($id);
            
            if ($purchase->status_pembelian !== 'approved') {
                return back()->with('error', 'Hanya pembelian yang sudah disetujui yang dapat diterima.');
            }

            // Update status pembelian
            $purchase->update([
                'status_pembelian' => 'received',
            ]);

            // Update stok bahan baku
            foreach ($purchase->details as $detail) {
                $rawMaterial = RawMaterial::findOrFail($detail->raw_material_id);
                $rawMaterial->update([
                    'stok' => $rawMaterial->stok + $detail->jumlah
                ]);
            }

            DB::commit();
            return redirect()->route('admin.purchases.index')->with('success', 'Pembelian berhasil diterima dan stok telah diperbarui.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    public function show($id)
    {
        $purchase = Purchase::with(['details.rawMaterial', 'supplier'])->findOrFail($id);
        return view('Admin.purchases.show', compact('purchase'));
    }
}
