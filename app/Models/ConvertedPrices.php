<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ConvertedPrices extends Model
{
    protected $table = 'converted_prices';
    protected $fillable = [
        'id',
        'sapo_name',
        'active_status',
        'name_converted',
        'gid_converted',
        'created_at',
        'updated_at'
    ];
}
