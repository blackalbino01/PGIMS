<?php

namespace App\Models;


use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphOne;
use Illuminate\Notifications\Notifiable;

class Notification extends Model
{
    protected $fillable = [
        'type',
        'title',
        'message',
        'is_read',
    ];

    public function notifiable()
    {
        return $this->morphTo();
    }
}
