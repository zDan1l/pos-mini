<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Menu extends Model
{
    protected $primaryKey = 'idmenu';
    protected $table = 'menus';

    protected $fillable = [
        'nama_menu',
        'harga',
        'path_gambar',
        'idvendor',
        'is_available',
    ];

    protected $casts = [
        'harga' => 'decimal:2',
        'is_available' => 'boolean',
    ];

    protected $hidden = [
        'created_at',
        'updated_at',
    ];

    public function vendor(): BelongsTo
    {
        return $this->belongsTo(Vendor::class, 'idvendor', 'idvendor');
    }

    public function detailPesanan(): HasMany
    {
        return $this->hasMany(DetailPesanan::class, 'idmenu', 'idmenu');
    }
}
