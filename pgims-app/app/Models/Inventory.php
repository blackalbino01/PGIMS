<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Inventory extends Model
{
    protected $fillable = ['store_id', 'product_id', 'quantity'];

    public function store(): BelongsTo
    {
        return $this->belongsTo(related: Store::class);
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(related: Product::class);
    }
}