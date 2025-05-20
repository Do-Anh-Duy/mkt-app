<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OrdersItem extends Model
{
    protected $table = 'orders_item';
    protected $fillable = [
        'id',
        'order_number',
        'sku',
        'name',
        'discounted_total',
        'quantity',
        'created_at',
        'updated_at',
        'order_item_converted_price'
    ];
}
