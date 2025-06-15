<?php

namespace App\Models;

use App\Models\User;
use App\Models\Supplier;
use App\Models\PurchaseDetail;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Purchase extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'purchases';

    protected $fillable = [
        'pemasok_id',
        'tanggal_pembelian',
        'nomor_pembelian',
        'total_amount',
        'status_pembelian',
        'catatan',
        'dibuat_oleh',
    ];

    protected $casts = [
        'tanggal_pembelian' => 'date',
        'total_amount' => 'decimal:2',
    ];

    public function supplier()
    {
        return $this->belongsTo(Supplier::class, 'pemasok_id');
    }

    public function details()
    {
        return $this->hasMany(PurchaseDetail::class, 'pembelian_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'dibuat_oleh');
    }


    public function generateInvoiceNumber()
    {
        $lastPurchase = $this->orderBy('id', 'desc')->first();
        $lastNumber = $lastPurchase ? intval(substr($lastPurchase->nomor_pembelian, 3)) : 0;
        $newNumber = $lastNumber + 1;
        return 'PUR' . str_pad($newNumber, 6, '0', STR_PAD_LEFT);
    }

    public function scopeByStatus($query, $status)
    {
        return $query->where('status_pembelian', $status);
    }

    public function scopeByDateRange($query, $startDate, $endDate)
    {
        return $query->whereBetween('tanggal_pembelian', [$startDate, $endDate]);
    }

    public function scopeBySupplier($query, $supplierId)
    {
        return $query->where('pemasok_id', $supplierId);
    }

    public function scopeSearch($query, $search)
    {
        return $query->where('nomor_pembelian', 'like', "%{$search}%");
    }

    public function calculateTotal()
    {
        return $this->details()->sum('total');
    }

    public function updateStatus($status, $userId = null)
    {
        $this->status_pembelian = $status;

        switch ($status) {
            case 'approved':
                $this->disetujui_oleh = $userId;
                $this->disetujui_pada = now();
                break;
            case 'rejected':
                $this->ditolak_oleh = $userId;
                $this->ditolak_pada = now();
                break;
            case 'received':
                $this->diterima_oleh = $userId;
                $this->diterima_pada = now();
                break;
        }

        $this->save();
    }
}
