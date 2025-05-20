<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use App\Models\Connection;
use App\Models\ConvertedPrices;
use Illuminate\Support\Facades\DB;
class ConnectionController extends Controller
{
    public function saveConnection(Request $request)
    {
        $existingConnection = Connection::where('username_sapo', $request->input('username1'))
            ->where('username_dotdigital', $request->input('username2'))
            ->where('store_sapo', $request->input('store1'))
            ->first();

        if ($existingConnection) {
            return response()->json(['success' => false, 'message' => 'Kết nối đã tồn tại.'], 400);
        }
        ConvertedPrices::updateOrInsert(
            [
                'sapo_name' => $request->input('store1')
            ], 
            [ 
                'name_converted' => $request->input('nameconverted'),
                'gid_converted' => (int) $request->input('gidconverted'),
                'active_status' => $request->input('activeStatus'),
                'created_at' => now(),
                'updated_at' => now(),
            ]
        );

        // Lưu dữ liệu vào database
        $connection = new Connection();
        $connection->username_sapo = $request->input('username1');
        $connection->password_sapo = $request->input('password1');
        $connection->store_sapo = $request->input('store1');
        $connection->username_dotdigital = $request->input('username2');
        $connection->password_dotdigital = $request->input('password2');
        $connection->active_status = $request->input('activeStatus');
        $connection->created_by = Auth::check() ? Auth::user()->id : null; // Ghi nhận người tạo
        $connection->created_at = now();
        $connection->customers_sync_time = now();
        $connection->orders_sync_time = now(); // Thời gian tạo
        $connection->save();

        return response()->json(['success' => true, 'message' => 'Kết nối đã được lưu thành công.']);
    }

    public function updateStatus(Request $request)
    {
        $connection = Connection::find($request->id);
        if ($connection) {
            $connection->active_status = $request->status;
            $connection->save();

            ConvertedPrices::where('sapo_name', $connection->store_sapo)
                ->update([
                    'active_status' => $request->status
                ]);

            return response()->json(['success' => true]);
        }

        return response()->json(['success' => false], 404);
    }

    public function show($id)
    {
        $connection = DB::table('connections')
                ->leftJoin('converted_prices', 'converted_prices.sapo_name', '=', 'connections.store_sapo')
                ->where('connections.id', $id)
                ->select('connections.*', 'converted_prices.name_converted','converted_prices.gid_converted')
                ->get();
        if (!$connection) {
            return response()->json([
                'success' => false,
                'message' => 'Không tìm thấy kết nối'
            ], 404);
        }

        return response()->json([
            'success' => true,
            'connection' => $connection
        ]);
    }

    public function update(Request $request)
    {
        $conn = Connection::find($request->connectionId1);
        if (!$conn) {
            return response()->json(['success' => false, 'message' => 'Không tìm thấy kết nối.']);
        }

        ConvertedPrices::where('sapo_name', $request->store3)
                ->update([
                    'active_status' => $request->activeStatus1,
                    'name_converted' => $request->nameconverted1,
                    'gid_converted' => $request->gidconverted2,
                    'sapo_name' => $request->store3,
                ]);

        ConvertedPrices::updateOrInsert(
            [
                'sapo_name' => $request->store3
            ], 
            [ 
                'name_converted' => $request->nameconverted1,
                'gid_converted' => (int) $request->gidconverted2,
                'active_status' => $request->activeStatus1,
                'created_at' => now(),
                'updated_at' => now(),
            ]
        );

        $conn->username_sapo = $request->username3;
        if (!empty($request->password3)) {
            $conn->password_sapo = $request->password3;
        }
        $conn->store_sapo = $request->store3;
        $conn->username_dotdigital = $request->username4;
        if (!empty($request->password4)) {
            $conn->password_dotdigital = $request->password4;
        }
        $conn->active_status = $request->activeStatus1;
        $conn->save();

        return response()->json(['success' => true, 'message' => 'Cập nhật thành công!']);
    }

}