<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class PurchaseDetail extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'purchase_details';

    protected $fillable = [
        'pembelian_id',
        'raw_material_id',
        'nama',
        'jumlah',
        'harga',
        'total',
        'catatan'
    ];

    protected $casts = [
        'quantity' => 'decimal:2',
        'unit_price' => 'decimal:2',
        'total' => 'decimal:2'
    ];

    public function purchase()
    {
        return $this->belongsTo(Purchase::class, 'pembelian_id');
    }

    public function rawMaterial()
    {
        return $this->belongsTo(RawMaterial::class, 'raw_material_id');
    }

    public function calculateTotal()
    {
        $this->total = $this->quantity * $this->unit_price;
        return $this->total;
    }

    public function updateRawMaterialStock()
    {
        $rawMaterial = $this->rawMaterial;
        if ($rawMaterial) {
            $rawMaterial->stok += $this->quantity;
            $rawMaterial->harga = $this->unit_price;
            $rawMaterial->save();
        }
    }
} 