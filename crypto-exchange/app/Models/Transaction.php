<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Transaction extends Model
{
    use HasFactory;

    protected  $primaryKey = 'order_id';
    protected $fillable = [
        'buyer_id',
        'seller_id',
        'currency_id',
        'amount',
        'price',
        'total_amount',
        'fee_amount',
        'status',
        'transaction_hash',
    ];

    protected $casts = [
        'amount' => 'decimal:8',
        'price' => 'decimal:8',
        'total_amount' => 'decimal:8',
        'fee_amount' => 'decimal:8',
    ];

    // Relationships
    public function order()
    {
        return $this->belongsTo(Order::class);
    }

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
        return $this->belongsTo(Currency::class,'currency_id');
    }

    // Scopes
    public function scopeCompleted($query)
    {
        return $query->where('status', 'completed');
    }

    public function scopeByUser($query, $userId)
    {
        return $query->where(function($q) use ($userId) {
            $q->where('buyer_id', $userId)
              ->orWhere('seller_id', $userId);
        });
    }

    public function scopeByDateRange($query, $startDate, $endDate)
    {
        return $query->whereBetween('created_at', [$startDate, $endDate]);
    }

    // Helper methods
    public function getNetAmountAttribute()
    {
        return bcsub($this->total_amount, $this->fee_amount, 8);
    }

    public function getUserRole($userId)
    {
        if ($this->buyer_id == $userId) {
            return 'buyer';
        } elseif ($this->seller_id == $userId) {
            return 'seller';
        }
        return null;
    }

    public function isUserBuyer($userId)
    {
        return $this->buyer_id == $userId;
    }

    public function isUserSeller($userId)
    {
        return $this->seller_id == $userId;
    }
}