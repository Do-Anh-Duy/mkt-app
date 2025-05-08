<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class SapoController extends Controller
{
    public function checkConnection(Request $request)
    {
        $request->validate([
            'username' => 'required',
            'password' => 'required',
            'store'    => 'required',
        ]);

        $username = $request->input('username');
        $password = $request->input('password');
        $store = $request->input('store');

        // Lấy ngày hiện tại theo định dạng ISO 8601 (giờ UTC)
        $today = now()->format('Y-m-d');
        $created_on_min = $today . 'T00:00:00Z';
        $created_on_max = $today . 'T23:59:59Z';

        // Tạo URL với tham số ngày
        $url = "https://{$username}:{$password}@{$store}.mysapo.net/admin/orders.json";
        $url .= "?fields=created_on,id,name,total_price";
        $url .= "&created_on_min={$created_on_min}&created_on_max={$created_on_max}";

        try {
            $response = Http::get($url);

            if ($response->successful()) {
                return response()->json(['success' => true, 'message' => 'Kết nối thành công']);
            } else {
                return response()->json(['success' => false, 'message' => 'Kết nối thất bại'], $response->status());
            }
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }
}
