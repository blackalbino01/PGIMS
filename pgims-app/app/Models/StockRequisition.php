<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class StockRequisition extends Model
{
    protected $fillable = [
        'from_store_id', 'to_store_id', 'status', 'approved_by'
    ];

    public function fromStore(): BelongsTo
    {
        return $this->belongsTo(related: Store::class, foreignKey: 'from_store_id');
    }

    public function toStore(): BelongsTo
    {
        return $this->belongsTo(related: Store::class, foreignKey: 'to_store_id');
    }

    public function approvedBy(): BelongsTo
    {
        return $this->belongsTo(related: User::class, foreignKey: 'approved_by');
    }

    public function items(): HasMany
    {
        return $this->hasMany(related: StockRequisitionItem::class);
    }
}
