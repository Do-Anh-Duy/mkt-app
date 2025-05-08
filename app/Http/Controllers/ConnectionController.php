<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use App\Models\Connection;
class ConnectionController extends Controller
{
    public function saveConnection(Request $request)
    {
        // Validate input
        $request->validate([
            'username1' => 'required',
            'password1' => 'required',
            'store1' => 'required',
            'username2' => 'required',
            'password2' => 'required',
        ]);
        

        // Kiểm tra trùng lặp trước khi lưu
        $existingConnection = Connection::where('username_sapo', $request->input('username1'))
            ->where('username_dotdigital', $request->input('username2'))
            ->where('store_sapo', $request->input('store1'))
            ->first();

        if ($existingConnection) {
            return response()->json(['success' => false, 'message' => 'Kết nối đã tồn tại.'], 400);
        }

        // Lưu dữ liệu vào database
        $connection = new Connection();
        $connection->username_sapo = $request->input('username1');
        $connection->password_sapo = $request->input('password1');
        $connection->store_sapo = $request->input('store1');
        $connection->username_dotdigital = $request->input('username2');
        $connection->password_dotdigital = $request->input('password2');
        $connection->active_status = $request->input('activeStatus');
        $connection->created_by = Auth::check() ? Auth::user()->id : null; // Ghi nhận người tạo
        $connection->created_at = now(); // Thời gian tạo
        $connection->save();

        // Trả về response dưới dạng JSON
        return response()->json(['success' => true, 'message' => 'Kết nối đã được lưu thành công.']);
    }

    public function updateStatus(Request $request)
    {
        $connection = Connection::find($request->id);
        if ($connection) {
            $connection->active_status = $request->status;
            $connection->save();

            return response()->json(['success' => true]);
        }

        return response()->json(['success' => false], 404);
    }

    public function show($id)
    {
        $connection = Connection::find($id);

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