<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Transfer extends Model
{
    use HasFactory;

    protected $fillable = [
        'from_user_id',
        'to_user_id',
        'currency_id',
        'amount',
        'fee_amount',
        'type',
        'external_address',
        'transaction_hash',
        'status',
        'notes',
    ];

    protected $casts = [
        'amount' => 'decimal:8',
        'fee_amount' => 'decimal:8',
    ];

    // Relationships
    public function fromUser()
    {
        return $this->belongsTo(User::class, 'from_user_id');
    }

    public function toUser()
    {
        return $this->belongsTo(User::class, 'to_user_id');
    }

    public function currency()
    {
        return $this->belongsTo(Currency::class);
    }

    // Scopes
    public function scopeInternal($query)
    {
        return $query->where('type', 'internal');
    }

    public function scopeExternal($query)
    {
        return $query->where('type', 'external');
    }

    public function scopeByUser($query, $userId)
    {
        return $query->where(function($q) use ($userId) {
            $q->where('from_user_id', $userId)
              ->orWhere('to_user_id', $userId);
        });
    }

    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    public function scopeCompleted($query)
    {
        return $query->where('status', 'completed');
    }

    // Helper methods
    public function getNetAmountAttribute()
    {
        return bcsub($this->amount, $this->fee_amount, 8);
    }

    public function isInternal()
    {
        return $this->type === 'internal';
    }

    public function isExternal()
    {
        return $this->type === 'external';
    }

    public function getUserRole($userId)
    {
        if ($this->from_user_id == $userId) {
            return 'sender';
        } elseif ($this->to_user_id == $userId) {
            return 'receiver';
        }
        return null;
    }

    public function isUserSender($userId)
    {
        return $this->from_user_id == $userId;
    }

    public function isUserReceiver($userId)
    {
        return $this->to_user_id == $userId;
    }

    public function getRecipientDisplayAttribute()
    {
        if ($this->isInternal()) {
            return $this->toUser ? $this->toUser->full_name : 'Unknown User';
        } else {
            return $this->external_address;
        }
    }
}