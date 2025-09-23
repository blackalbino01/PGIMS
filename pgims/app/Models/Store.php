<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Store extends Model
{
    protected $fillable = ['name', 'address', 'phone'];

    public function inventory(): HasMany
    {
        return $this->hasMany(related: Inventory::class);
    }

    public function stockRequisitionsFrom(): HasMany
    {
        return $this->hasMany(related: StockRequisition::class, foreignKey: 'from_store_id');
    }

    public function stockRequisitionsTo(): HasMany
    {
        return $this->hasMany(related: StockRequisition::class, foreignKey: 'to_store_id');
    }
}
