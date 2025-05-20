<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Contact extends Model
{
    use HasFactory;

    protected $table = 'contacts'; // Thay đổi tên bảng nếu cần
    protected $fillable = [
        'id',
        'email',
        'mobileNumber',
        'FIRSTNAME',
        'LASTNAME',
        'FULLNAME',
        'GENDER',
        'ADDRESS',
        'CITY',
        'Dotdigital_Sync',
        'sapo_store',
        'created_at',
        'updated_at',
        'creat_time'
    ];

}
