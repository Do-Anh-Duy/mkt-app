<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Connection;
use App\Models\Orders;
use App\Models\OrdersItem;
use App\Models\Campaign;
use GuzzleHttp\Client;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Http;

class SyncDotdigitalOrdersJob extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:sync-dotdigital-orders-job';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Đồng bộ đơn hàng từ SAPO lên Dotdigital';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $order = Orders::where('Dotdigital_Sync', 'pending')
            ->where('campaign_id', '!=', 0)
            ->whereNotNull('campaign_name')
            ->orderBy('created_at', 'asc')
            ->first();
        if($order){
            $order_items = OrdersItem::where('order_number', $order->order_number)->get()->toArray();
            $products = [];
            foreach ($order_items as $value) {
                $products[] = [
                    'sku' => $value['sku'],
                    'name' => $value['name'],
                    'qty' => $value['quantity'],
                    'price' => $value['discounted_total'],
                ];
            }
            $connect = Connection::where('store_sapo', $order->sapo_store)->get()->toArray();
            if (!$connect) {
                // Xử lý khi không tìm thấy kết nối
                return;
            }
            $username = $connect[0]['username_dotdigital'];
            $password = $connect[0]['password_dotdigital'];

            $auth = base64_encode($username . ':' . $password);

            $response = Http::withHeaders([
                'Accept' => 'application/json',
                'Authorization' => 'Basic ' . $auth,
            ])->put('https://r3-api.dotdigital.com/insightData/v3/import', [
                'collectionScope' => 'contact',
                'collectionType' => 'orders',
                'collectionName' => 'orders',
                'records' => [
                    [
                        'contactIdentity' => [
                            'identifier' => 'email',
                            'value' => $order->email,
                        ],
                        'key' => $order->order_number,
                        'json' => [
                            'id' => (string) $order->order_number,
                            'order_total' => $order->subtotal_price,
                            'order_subtotal' => $order->subtotal_price,
                            'currency' => 'VND',
                            'purchase_date' => $order->created_time,
                            'order_status' => 'complete',
                            'campaign_id' => $order->campaign_id,
                            'campaign_name' => $order->campaign_name,
                            'products' => $products,
                        ],
                    ],
                ],
            ]);

            if ($response->successful()) {
                DB::table('orders')
                    ->where('id', $order->id)
                    ->update(['Dotdigital_Sync' => 'synced']);
            } else{
                $errorData = $response->json();
                DB::table('orders')
                    ->where('id', $order->id)
                    ->update(['Dotdigital_Sync' => $errorData['details'][0]['description'] ?? 'error']);
            }
        }
    }
}
