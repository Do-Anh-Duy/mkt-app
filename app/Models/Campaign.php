<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Campaign extends Model
{
    protected $fillable = [
        'campaigns_id',
        'campaigns_name',
        'link_sapo',
        'sapo_store',
    ];
}
