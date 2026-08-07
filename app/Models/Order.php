<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Order extends Model
{
    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'order_number',
        'customer_name',
        'total_amount',
        'status',
        'payment_status',
    ];

    /**
     * Attribute casting.
     */
    protected function casts(): array
    {
        return [
            'total_amount' => 'decimal:2',
        ];
    }

    /**
     * An order has many order items.
     */
    public function items(): HasMany
    {
        return $this->hasMany(OrderItem::class);
    }
}