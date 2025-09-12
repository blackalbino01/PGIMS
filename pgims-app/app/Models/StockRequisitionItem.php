<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class StockRequisitionItem extends Model
{
    protected $fillable = [
        'stock_requisition_id', 'product_id', 'quantity'
    ];

    public function stockRequisition(): BelongsTo
    {
        return $this->belongsTo(related: StockRequisition::class);
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(related: Product::class);
    }
}
