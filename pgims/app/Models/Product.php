<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Product extends Model
{
    protected $fillable = ['sku', 'name', 'description', 'price'];

    public function inventory(): HasMany
    {
        return $this->hasMany(related: Inventory::class);
    }

    public function stockRequisitionItems(): HasMany
    {
        return $this->hasMany(related: StockRequisitionItem::class);
    }

    public function category()
    {
        return $this->belongsTo(ProductCategory::class);
    }

}