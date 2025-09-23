<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PurchaseOrder extends Model
{
    use HasFactory;

    protected $fillable = [
        'supplier_id',
        'order_number',
        'status',
        'total_amount',
        'order_date',
        'expected_date',
        'notes',
    ];

    public function supplier(): BelongsTo
    {
        return $this->belongsTo(related: Supplier::class);
    }
}