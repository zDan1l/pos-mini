<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Store extends Model
{
    protected $primaryKey = 'idtoko';
    protected $table = 'stores';

    protected $fillable = [
        'barcode',
        'nama_toko',
        'alamat',
        'latitude',
        'longitude',
        'accuracy',
    ];

    protected $hidden = [
        'created_at',
        'updated_at',
    ];

    protected $casts = [
        'latitude' => 'decimal:8',
        'longitude' => 'decimal:8',
        'accuracy' => 'decimal:2',
    ];

    public function visits(): HasMany
    {
        return $this->hasMany(StoreVisit::class, 'idtoko', 'idtoko');
    }

    public function acceptedVisits(): HasMany
    {
        return $this->visits()->where('status', 'diterima');
    }
}
