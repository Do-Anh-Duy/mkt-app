<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Connection extends Model
{
    use HasFactory;

    // Đảm bảo bạn đã xác định bảng và các trường (nếu cần)
    protected $table = 'connections'; // Thay đổi tên bảng nếu cần
    protected $fillable = [
        'id',
        'username_sapo',
        'password_sapo',
        'store_sapo',
        'username_dotdigital',
        'password_dotdigital',
        'active_status',
        'created_by',
        'created_at',
        'customers_sync_time',
        'orders_sync_time'
    ];
}