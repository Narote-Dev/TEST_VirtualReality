<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Currency extends Model
{
    use HasFactory;

    protected $primaryKey = 'currency_id';

    protected $fillable = [
        'code',
        'name',
        'type',
        'symbol',
        'decimal_places',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'decimal_places' => 'integer',
    ];

    // Relationships
    public function wallets()
    {
        return $this->hasMany(Wallet::class);
    }

    public function orders()
    {
        return $this->hasMany(Order::class);
    }

    public function transfers()
    {
        return $this->hasMany(Transfer::class);
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeCrypto($query)
    {
        return $query->where('type', 'crypto');
    }

    public function scopeFiat($query)
    {
        return $query->where('type', 'fiat');
    }

    // Helper methods
    public function isCrypto()
    {
        return $this->type === 'crypto';
    }

    public function isFiat()
    {
        return $this->type === 'fiat';
    }

    public function formatAmount($amount)
    {
        return number_format($amount, $this->decimal_places) . ' ' . $this->code;
    }
}