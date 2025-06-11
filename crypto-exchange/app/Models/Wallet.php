<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Wallet extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'currency_id',
        'balance',
        'frozen_balance',
        'wallet_address',
    ];

    protected $casts = [
        'balance' => 'decimal:8',
        'frozen_balance' => 'decimal:8',
    ];

    // Relationships
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function currency()
    {
        return $this->belongsTo(Currency::class);
    }

    // Helper methods
    public function getAvailableBalanceAttribute()
    {
        return $this->balance - $this->frozen_balance;
    }

    public function hasEnoughBalance($amount)
    {
        return $this->available_balance >= $amount;
    }

    public function addBalance($amount)
    {
        $this->balance = bcadd($this->balance, $amount, 8);
        return $this->save();
    }

    public function subtractBalance($amount)
    {
        if (!$this->hasEnoughBalance($amount)) {
            throw new \Exception('Insufficient balance');
        }
        
        $this->balance = bcsub($this->balance, $amount, 8);
        return $this->save();
    }

    public function freezeAmount($amount)
    {
        if (!$this->hasEnoughBalance($amount)) {
            throw new \Exception('Insufficient balance to freeze');
        }
        
        $this->frozen_balance = bcadd($this->frozen_balance, $amount, 8);
        return $this->save();
    }

    public function unfreezeAmount($amount)
    {
        $this->frozen_balance = bcsub($this->frozen_balance, $amount, 8);
        return $this->save();
    }
}