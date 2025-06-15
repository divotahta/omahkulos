<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RawMaterial extends Model
{
    use HasFactory;

    protected $table = 'raw_materials';

    protected $fillable = [
        'nama',
        'kode',
        'satuan',
        'supplier_id',
        'stok',
        'harga',
        'deskripsi',
        'expired_date'
    ];

    protected $casts = [
        'expired_date' => 'date',
        'stok' => 'integer',
        'harga' => 'decimal:2'
    ];

    public function supplier()
    {
        return $this->belongsTo(Supplier::class);
    }

    public function stockHistories()
    {
        return $this->hasMany(StockHistory::class, 'bahan_baku_id');
    }
} 