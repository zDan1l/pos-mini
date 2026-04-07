<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DetailPesanan extends Model
{
    protected $primaryKey = 'iddetail_pesanan';
    protected $table = 'detail_pesanan';

    protected $fillable = [
        'idmenu',
        'idpesanan',
        'jumlah',
        'harga',
        'subtotal',
        'timestamp',
        'catatan',
    ];

    protected $casts = [
        'harga' => 'decimal:2',
        'subtotal' => 'decimal:2',
        'timestamp' => 'datetime',
    ];

    protected $hidden = [
        'created_at',
        'updated_at',
    ];

    public function menu(): BelongsTo
    {
        return $this->belongsTo(Menu::class, 'idmenu', 'idmenu');
    }

    public function pesanan(): BelongsTo
    {
        return $this->belongsTo(Pesanan::class, 'idpesanan', 'idpesanan');
    }
}
