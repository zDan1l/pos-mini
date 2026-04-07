<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Pesanan extends Model
{
    protected $primaryKey = 'idpesanan';
    protected $table = 'pesanan';

    protected $fillable = [
        'nama',
        'timestamp',
        'total',
        'metode_bayar',
        'status_bayar',
        'payment_reference',
        'idcustomer',
        'user_id',
        'idvendor',
    ];

    protected $casts = [
        'total' => 'decimal:2',
        'timestamp' => 'datetime',
    ];

    protected $hidden = [
        'created_at',
        'updated_at',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function vendor(): BelongsTo
    {
        return $this->belongsTo(Vendor::class, 'idvendor', 'idvendor');
    }

    public function detailPesanan(): HasMany
    {
        return $this->hasMany(DetailPesanan::class, 'idpesanan', 'idpesanan');
    }

    public function isLunas(): bool
    {
        return $this->status_bayar === 'lunas';
    }

    public function getCustomerNameAttribute(): string
    {
        return $this->user ? $this->user->name : ($this->nama ?? 'Guest');
    }
}
