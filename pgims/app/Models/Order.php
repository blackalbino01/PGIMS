<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Order extends Model
{
    use HasFactory;

    protected $fillable = [
        'customer_id',
        'status',
        'total_amount',
        'notes',
    ];

    public function customer(): BelongsTo
    {
        return $this->belongsTo(related: Customer::class);
    }

    public function items(): HasMany
    {
        return $this->hasMany(related: OrderItem::class);
    }
}
