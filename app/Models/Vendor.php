<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Vendor extends Model
{
    protected $primaryKey = 'idvendor';
    protected $table = 'vendors';

    protected $fillable = [
        'nama_vendor',
        'kode_vendor',
        'user_id',
    ];

    protected $hidden = [
        'created_at',
        'updated_at',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }

    /**
     * Get vendor account users (users that belong to this vendor)
     */
    public function users(): HasMany
    {
        return $this->hasMany(User::class, 'idvendor', 'idvendor');
    }

    /**
     * Get the primary vendor account
     */
    public function vendorAccount(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function menus(): HasMany
    {
        return $this->hasMany(Menu::class, 'idvendor', 'idvendor');
    }

    public function pesanan(): HasMany
    {
        return $this->hasMany(Pesanan::class, 'idvendor', 'idvendor');
    }
}
