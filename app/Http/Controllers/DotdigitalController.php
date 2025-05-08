<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class DotdigitalController extends Controller
{
    public function checkConnection(Request $request)
    {
        $username = $request->input('username');
        $password = $request->input('password');

        $auth = base64_encode($username . ':' . $password);

        $response = Http::withHeaders([
            'accept' => 'application/json',
            'authorization' => 'Basic ' . $auth,
        ])->get('https://r3-api.dotdigital.com/insightData/v3/collections');

        if ($response->successful()) {
            return response()->json(['success' => true]);
        }

        return response()->json(['success' => false]);
    }
}
