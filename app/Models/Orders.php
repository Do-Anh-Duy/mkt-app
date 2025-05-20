<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Orders extends Model
{
    protected $table = 'orders';
    protected $fillable = [
        'id',
        'order_number',
        'email',
        'phone',
        'currency',
        'status',
        'subtotal_price',
        'Dotdigital_Sync',
        'sapo_store',
        'created_time',
        'created_at',
        'created_on',
        'campaign_id',
        'campaign_name',
        'sapo_name',
        'landing_site',
        'order_converted_price'
    ];
}
