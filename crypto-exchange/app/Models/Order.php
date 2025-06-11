<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    use HasFactory;

    protected $fillable = [
        'buyer_id',
        'seller_id',
        'currency_id',
        'payment_currency_id',
        'type',
        'amount',
        'price',
        'total_amount',
        'filled_amount',
        'status',
        'expires_at',
    ];

    protected $casts = [
        'amount' => 'decimal:8',
        'price' => 'decimal:8',
        'total_amount' => 'decimal:8',
        'filled_amount' => 'decimal:8',
        'expires_at' => 'datetime',
    ];

    // Relationships
    public function buyer()
    {
        return $this->belongsTo(User::class, 'buyer_id');
    }

    public function seller()
    {
        return $this->belongsTo(User::class, 'seller_id');
    }

    public function currency()
    {
        return $this->belongsTo(Currency::class);
    }

    public function paymentCurrency()
    {
        return $this->belongsTo(Currency::class, 'payment_currency_id');
    }

    public function transactions()
    {
        return $this->hasMany(Transaction::class);
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    public function scopeBuyOrders($query)
    {
        return $query->where('type', 'buy');
    }

    public function scopeSellOrders($query)
    {
        return $query->where('type', 'sell');
    }

    public function scopeByUser($query, $userId)
    {
        return $query->where(function($q) use ($userId) {
            $q->where('buyer_id', $userId)
              ->orWhere('seller_id', $userId);
        });
    }

    // Helper methods
    public function getRemainingAmountAttribute()
    {
        return bcsub($this->amount, $this->filled_amount, 8);
    }

    public function getProgressPercentageAttribute()
    {
        if ($this->amount == 0) return 0;
        return bcmul(bcdiv($this->filled_amount, $this->amount, 8), 100, 2);
    }

    public function isFullyFilled()
    {
        return bccomp($this->filled_amount, $this->amount, 8) === 0;
    }

    public function isExpired()
    {
        return $this->expires_at && $this->expires_at->isPast();
    }

    public function canBeCancelled()
    {
        return in_array($this->status, ['active', 'partial']);
    }

    public function fillOrder($amount)
    {
        $this->filled_amount = bcadd($this->filled_amount, $amount, 8);
        
        if ($this->isFullyFilled()) {
            $this->status = 'completed';
        } else {
            $this->status = 'partial';
        }
        
        return $this->save();
    }
}