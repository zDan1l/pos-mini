<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
        'idvendor',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
    ];

    public function vendor(): BelongsTo
    {
        return $this->belongsTo(Vendor::class, 'idvendor', 'idvendor');
    }

    public function pesanan(): HasMany
    {
        return $this->hasMany(Pesanan::class, 'user_id');
    }

    public function isAdmin(): bool
    {
        return $this->role === 'admin';
    }

    public function isVendor(): bool
    {
        return $this->role === 'vendor';
    }

    public function isCustomer(): bool
    {
        return $this->role === 'customer';
    }

    public static function createGuest(?string $name = null): self
    {
        $lastGuest = self::where('role', 'customer')
            ->whereNotNull('idvendor')
            ->orderBy('id', 'desc')
            ->first();

        $nextNumber = $lastGuest
            ? (int) filter_var($lastGuest->name, FILTER_SANITIZE_NUMBER_INT) + 1
            : 1;

        return self::create([
            'name' => $name ?? 'Guest_' . str_pad($nextNumber, 7, '0', STR_PAD_LEFT),
            'email' => 'guest_' . $nextNumber . '@dummy.com',
            'password' => bcrypt('guest_password'),
            'role' => 'customer',
        ]);
    }
}
