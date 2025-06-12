<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    protected $primaryKey = 'user_id';

    protected $fillable = [
        'first_name',
        'last_name',
        'email',
        'phone',
        'username',
        'password',
        'kyc_status',
        'is_verified',
        'is_active',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'is_verified' => 'boolean',
        'is_active' => 'boolean',
    ];

    // Relationships
    public function wallets()
    {
        return $this->hasMany(Wallet::class, 'user_id', 'user_id');
    }

    public function buyOrders()
    {
        return $this->hasMany(Order::class, 'buyer_id');
    }

    public function sellOrders()
    {
        return $this->hasMany(Order::class, 'seller_id');
    }

    public function sentTransfers()
    {
        return $this->hasMany(Transfer::class, 'from_user_id');
    }

    public function receivedTransfers()
    {
        return $this->hasMany(Transfer::class, 'to_user_id');
    }

    // Helper methods
    public function getFullNameAttribute()
    {
        return $this->first_name . ' ' . $this->last_name;
    }

    public function getWalletBalance($currencyId)
    {
        $wallet = $this->wallets()->where('currency_id', $currencyId)->first();
        return $wallet ? $wallet->balance : 0;
    }

    public function hasWallet($currencyId)
    {
        return $this->wallets()->where('currency_id', $currencyId)->exists();
    }

}