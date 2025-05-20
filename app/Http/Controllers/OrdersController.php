<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class OrdersController extends Controller
{
        public function index()
    {
        $date = [
            'firstDay' => Carbon::now()->startOfMonth()->toDateString(),
            'lastDay' => Carbon::now()->endOfMonth()->toDateString(),
        ];
        
        $orders = DB::table('orders')
                ->where('created_at', '>=', $date['firstDay'])
                ->where('created_at', '<=', $date['lastDay'])
                ->orderBy('id', 'desc')
                ->get();

        $sapos = DB::table('connections')
                ->where('active_status', 1)
                ->get();

        return view('dashboard.orders', compact('orders','date','sapos'));
    }

        public function searchIndex(Request $request)
    {
        $date = [
            'firstDay' => Carbon::now()->startOfMonth()->toDateString(),
            'lastDay' => Carbon::now()->endOfMonth()->toDateString(),
        ];
        $query = DB::table('contacts');

        if ($request->input('start_date')) {
            $query->where('created_at', '>=', $request->input('start_date'));
        } else {
            $query->where('created_at', '>=', $date['firstDay']);
        }

        if ($request->input('end_date')) {
            $query->where('created_at', '<=', $request->input('end_date'));
        } else {
            $query->where('created_at', '<=', $date['lastDay']);
        }

        if ($request->input('store_sapo')) {
            $query->where('sapo_store', $request->input('store_sapo'));
        }

        $contacts = $query->orderBy('id', 'desc')->get();

        $sapos = DB::table('connections')
                ->where('active_status', 1)
                ->get();

        return view('dashboard.contacts', compact('contacts','date','sapos'));
    }
}
