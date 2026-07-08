<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Order extends Model
{
    protected $fillable = [
        'name',
        'user_id',
        'order_status_id',
        'total',
        'comment'
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function order_status(): BelongsTo
    {
        return $this->belongsTo(OrderStatus::class);
    }
}
