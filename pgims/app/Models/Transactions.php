<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Transactions extends Model
{
    use HasFactory;

    protected $fillable = [
        'bank_account_id',
        'type',
        'amount',
        'reference',
        'description',
        'transaction_date',
    ];

    public function bankAccount(): BelongsTo
    {
        return $this->belongsTo(related: BankAccount::class);
    }
}
