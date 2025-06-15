<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\RawMaterial;
use App\Models\Category;
use App\Models\Unit;
use App\Models\Supplier;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Log;

class RawMaterialController extends Controller
{
    public function index()
    {
        $rawMaterials = RawMaterial::with('supplier')->paginate(10);
        $suppliers = Supplier::all();
        return view('Admin.raw-materials.index', compact('rawMaterials', 'suppliers'));
    }

    public function create()
    {
        $lastRawMaterial = RawMaterial::latest()->first();
        $suppliers = Supplier::all();
        return view('Admin.raw-materials.create', compact('lastRawMaterial', 'suppliers'));
    }
    public function store(Request $request)
    {
        $request->validate([
            'nama' => 'required|string|max:255',
            'kode' => 'required|string|unique:raw_materials',
            'satuan' => 'required|string|max:255',
            'supplier_id' => 'nullable|exists:suppliers,id',
            'stok' => 'required|integer',
            'harga' => 'required|numeric',
            // 'deskripsi' => 'nullable|string',
            'expired_date' => 'nullable|date',
        ]);

        RawMaterial::create($request->all());
        return redirect()->route('admin.raw-materials.index')->with('success', 'Bahan baku berhasil ditambahkan.');
    }

    public function edit(RawMaterial $rawMaterial)
    {
        $suppliers = Supplier::all();
        return view('Admin.raw-materials.edit', compact('rawMaterial', 'suppliers'));
    }

    public function update(Request $request, RawMaterial $rawMaterial)
    {
        $request->validate([
            'nama' => 'required|string|max:255',
            'kode' => 'required|string|unique:raw_materials,kode,' . $rawMaterial->id,
            'satuan' => 'required|string|max:255',
            'supplier_id' => 'nullable|exists:suppliers,id',
            'stok' => 'required|integer',
            'harga' => 'required|numeric',
            'deskripsi' => 'nullable|string',
            'expired_date' => 'nullable|date',
        ]);

        $rawMaterial->update($request->all());
        return redirect()->route('admin.raw-materials.index')->with('success', 'Bahan baku berhasil diperbarui.');
    }

    public function destroy(RawMaterial $rawMaterial)
    {
        $rawMaterial->delete();
        return redirect()->route('admin.raw-materials.index')->with('success', 'Bahan baku berhasil dihapus.');
    }

    private function generateKodeBahan()
    {
        $prefix = 'BB'; // Bahan Baku
        $lastBahan = RawMaterial::orderBy('id', 'desc')->first();

        if ($lastBahan) {
            $lastNumber = (int) substr($lastBahan->kode_bahan, 2);
            $newNumber = $lastNumber + 1;
        } else {
            $newNumber = 1;
        }

        return $prefix . str_pad($newNumber, 4, '0', STR_PAD_LEFT);
    }

    public function updateStok(Request $request, RawMaterial $bahanBaku)
    {
        $request->validate([
            'jumlah' => 'required|numeric',
            'tipe' => 'required|in:tambah,kurang'
        ]);

        if ($request->tipe === 'tambah') {
            $bahanBaku->stok += $request->jumlah;
        } else {
            if ($bahanBaku->stok < $request->jumlah) {
                return back()->with('error', 'Stok tidak mencukupi');
            }
            $bahanBaku->stok -= $request->jumlah;
        }

        $bahanBaku->save();

        return redirect()->route('admin.raw-materials.index')
            ->with('success', 'Stok berhasil diperbarui');
    }
}
