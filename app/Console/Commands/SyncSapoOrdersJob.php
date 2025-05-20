<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Connection;
use App\Models\Orders;
use App\Models\Campaign;
use App\Models\OrdersItem;
use App\Models\ConvertedPrices;
use App\Models\Contact;
use GuzzleHttp\Client;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Http;

class SyncSapoOrdersJob extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:sync-sapo-orders-job';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Động bộ đơn hàng từ SAPO về MKT-APP';

    /**
     * Execute the console command.
     */
    public function handle()
    {
       $connect = Connection::where('active_status', 1)
            ->orderBy('orders_sync_time', 'asc')
            ->first();

        if (!$connect) {
            return;
        }

        $convertedPrice = ConvertedPrices::where('sapo_name', $connect->store_sapo)->get();

        if (!$convertedPrice) {
            return;
        }

        // Cập nhật thời gian đồng bộ
        DB::table('connections')
            ->where('id', $connect->id)
            ->update(['orders_sync_time' => now()]);

        $client = new Client();
        $username = $connect->username_sapo;
        $password = $connect->password_sapo;
        $store = $connect->store_sapo;

        // Lấy ngày hiện tại theo định dạng ISO 8601 (giờ UTC)
        $today = now()->format('Y-m-d');
        $created_on_min = $today . 'T00:00:00Z';
        $created_on_max = $today . 'T23:59:59Z';
        // $created_on_min = '2025-05-14T00:00:00Z';
        // $created_on_max = '2025-05-14T23:59:59Z';

        // Tạo URL với tham số ngày
        $url = "https://{$username}:{$password}@{$store}/admin/orders.json";
        $url .= "?fields=created_on,id,name,total_price";
        $url .= "&created_on_min={$created_on_min}&created_on_max={$created_on_max}";

        // Thực hiện gọi API
        try {
            $response = $client->get($url, [
                'timeout' => 180,
                'connect_timeout' => 180,
                'headers' => [
                    'Accept' => 'application/json',
                    'User-Agent' => 'GuzzleClient/7.0'
                ]
            ]);

            $data = json_decode($response->getBody(), true);
            } catch (\Exception $e) {
                // Xử lý lỗi kết nối, ghi log hoặc trả thông báo lỗi
                Log::error('Error fetching data: ' . $e->getMessage());
                return;
            }

        // Kiểm tra xem mảng orders có tồn tại và không rỗng không
        if (!empty($data['orders']) && is_array($data['orders'])) {
            foreach ($data['orders'] as $item) {

                if (empty($item['order_number'])) {
                    continue; // Bỏ qua đơn hàng nếu thiếu order_number
                }

                //Mục update ngược thông tin đặt hàng khách hàng không có số điện thoại
                if (Contact::where('email', $item['email'])->whereNull('mobileNumber')->exists()) {
                    Contact::where('email', $item['email'])
                        ->update([
                            'mobileNumber' => $item['phone'] ?? null,
                            'ADDRESS' => $item['shipping_address']['address1'] ?? null,
                            'FIRSTNAME' => $item['shipping_address']['first_name'] ?? null,
                            'LASTNAME' =>  $item['shipping_address']['last_name'] ?? null,
                            'FULLNAME' => $item['shipping_address']['first_name'] . ' ' . $item['shipping_address']['last_name'] ?? null,
                            'CITY' => $item['shipping_address']['city'] ?? null,
                            'GENDER' => $item['customer']['gender'] ?? null,

                        ]);
                }

                $campaign = Campaign::where('link_sapo', $item['landing_site'])->get()->toArray();
                // Cập nhật hoặc chèn đơn hàng vào bảng 'orders'
                DB::table('orders')->updateOrInsert(
                    [
                        'order_number' => $item['order_number']
                    ],
                    [
                        'email' => $item['email'] ?? null,
                        'phone' => $item['phone'] ?? null,
                        'currency' => $item['currency'] ?? null,
                        'status' => $item['status'] ?? null,
                        'subtotal_price' => $item['subtotal_price'] ?? 0,
                        'order_converted_price' => round($item['subtotal_price'] / $convertedPrice[0]['gid_converted'], 2) ?? 0.00,
                        'sapo_store' => $store ?? null,
                        'created_at' => now(),
                        'created_on'=> now(),
                        'created_time' => $item['processed_on'] ?? null,
                        'landing_site' => $item['landing_site'] ?? null,
                        'campaign_id' => $campaign[0]['campaigns_id'] ?? 0,
                        'campaign_name' => $campaign[0]['campaigns_name'] ?? null,
                        'sapo_name' => $connect->store_sapo ?? null,
                    ]
                );

                // Lặp qua các sản phẩm trong đơn hàng
                foreach ($item['line_items'] as $value) {
                    DB::table('orders_item')->updateOrInsert(
                        [
                            'order_number' => $item['order_number'],
                            'sku' => $value['sku']
                        ],
                        [
                            'name' => $value['name'] ?? null,
                            'discounted_total' => $value['discounted_total'] ?? 0,
                            'order_item_converted_price' => round($value['discounted_total'] / $convertedPrice[0]['gid_converted'], 2) ?? 0.00,
                            'quantity' => $value['quantity'] ?? 0,
                            'created_at' => now(),
                            'updated_at' => now()
                        ]
                    );
                }
            }
        } else {
            // Xử lý khi không có đơn hàng hoặc mảng orders rỗng
            Log::info('No orders found for the given date range.');
        } 
    }
}
