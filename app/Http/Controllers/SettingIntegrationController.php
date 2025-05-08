<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Connection;
use Illuminate\Support\Facades\DB;

class SettingIntegrationController extends Controller
{
    public function index()
    {
        $connections = DB::table('connections')
            ->leftJoin('users', 'connections.created_by', '=', 'users.id')
            ->select(
                'connections.*',
                'users.name as creator_name'
            )
            ->get();

        return view('setting.setting-integration', compact('connections'));
    }
}
