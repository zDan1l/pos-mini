<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Storage;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
        'idvendor',
        'photo_path',
        'photo_blob',
        'photo_mime_type',
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

    public function getPhotoUrlAttribute(): string
    {
        if ($this->photo_path) {
            return Storage::url($this->photo_path);
        }

        if ($this->photo_blob) {
            return route('customer-management.photo', $this->id);
        }

        return 'https://ui-avatars.com/api/?name='.urlencode($this->name).'&background=orange&color=fff';
    }

    public static function createGuest(?string $name = null): self
    {
        $lastGuest = self::where('role', 'customer')
            ->whereNull('idvendor')
            ->orderBy('id', 'desc')
            ->first();

        $nextNumber = 1;

        if ($lastGuest && preg_match('/Guest_(\d+)/', $lastGuest->name, $matches)) {
            $nextNumber = (int) $matches[1] + 1;
        }

        // Generate unique email - add random suffix if custom name is provided
        $guestName = $name ?? 'Guest_' . str_pad($nextNumber, 7, '0', STR_PAD_LEFT);

        if ($name) {
            // For custom names, add timestamp to ensure uniqueness
            $email = 'guest_' . $nextNumber . '_' . time() . '@dummy.com';
        } else {
            $email = 'guest_' . $nextNumber . '@dummy.com';
        }

        return self::create([
            'name' => $guestName,
            'email' => $email,
            'password' => bcrypt('guest_password'),
            'role' => 'customer',
            'idvendor' => null,
        ]);
    }
}
