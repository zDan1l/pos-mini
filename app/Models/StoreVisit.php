<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class StoreVisit extends Model
{
    protected $primaryKey = 'idvisit';
    protected $table = 'store_visits';

    protected $fillable = [
        'idtoko',
        'iduser',
        'visit_latitude',
        'visit_longitude',
        'visit_accuracy',
        'distance_from_store',
        'status',
        'visited_at',
    ];

    protected $casts = [
        'visit_latitude' => 'decimal:8',
        'visit_longitude' => 'decimal:8',
        'visit_accuracy' => 'decimal:2',
        'distance_from_store' => 'decimal:2',
        'visited_at' => 'datetime',
    ];

    public function store(): BelongsTo
    {
        return $this->belongsTo(Store::class, 'idtoko', 'idtoko');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'iduser', 'id');
    }
}
