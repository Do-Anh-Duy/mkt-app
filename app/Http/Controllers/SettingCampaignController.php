<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use App\Models\Campaign;
use App\Models\Connection;
use Illuminate\Support\Facades\DB;

class SettingCampaignController extends Controller
{
    public function index()
    {
        $connections = DB::table('campaigns')
                ->orderBy('campaigns_id', 'desc')
                ->get();
                
        return view('setting.campaign', compact('connections'));
    }

    public function sync(Request $request)
    {

        $info = Connection::find($request->campaignId);

        $username = $info->username_dotdigital;
        $password = $info->password_dotdigital;
        $store = $info->store_sapo;

        $auth = base64_encode($username . ':' . $password);

        $response = Http::withHeaders([
            'accept' => 'application/json',
            'authorization' => 'Basic ' . $auth,
        ])->get('https://r3-api.dotdigital.com/v2/campaigns');

        $campaigns = $response->json();

        foreach ($campaigns as $item) {
        Campaign::updateOrCreate(
            ['campaigns_id' => $item['id']],
                [
                    'campaigns_id' => $item['id'],
                    'campaigns_name' => $item['name'],
                    'sapo_store' => $store,
                ]
            );
        }

        if ($response->successful()) {
            return response()->json(['success' => true]);
        }

        return response()->json(['success' => false]);
        if ($response->failed()) {
            return response()->json(['success' => false, 'message' => 'API không phản hồi'], 500);
        }
    }

    public function show($id)
    {
        // Lấy dữ liệu chiến dịch từ database
        $campaign = Campaign::find($id);

        if ($campaign) {
            return response()->json([
                'success' => true,
                'data' => [
                    'id' => $campaign->id,
                    'campaigns_name' => $campaign->campaigns_name,
                    'link_sapo' => json_decode($campaign->link_sapo, true),
                    'sapo_store' => $campaign->sapo_store,
                ]
            ]);
        }

        return response()->json([
            'success' => false,
            'message' => 'Campaign not found'
        ]);
    }

    public function update(Request $request)
    {
        $campaign = Campaign::find($request->id);

        if (!$campaign) {
            return response()->json(['success' => false, 'message' => 'Không tìm thấy chiến dịch']);
        }
        $campaign->campaigns_name = $request->campaigns_name;
        $campaign->link_sapo = json_encode($request->link_sapo);
        $campaign->save();
        return response()->json(['success' => true]);
    }

    
    public function showStoreName()
    {
        $stores = Connection::where('active_status', 1)->get();
        if ($stores) {
            return response()->json([
                'success' => true,  
                'data' => [
                    'stores' => $stores, 
                ]
            ]);
        }

        return response()->json([
            'success' => false,
            'message' => 'Campaign not found'
        ]);
    }
}
