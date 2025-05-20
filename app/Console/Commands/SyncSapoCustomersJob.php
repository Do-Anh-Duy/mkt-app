<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Connection;
use GuzzleHttp\Client;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class SyncSapoCustomersJob extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:sync-sapo-customers-job';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Đồng bộ thông tin khách hàng từ SAPO về MKT-APP';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $connect = Connection::where('active_status', 1)
            ->orderBy('customers_sync_time', 'asc')
            ->first();
        if (!$connect) {
            // Xử lý khi không tìm thấy kết nối
            return;
        }
        DB::table('connections')
            ->where('id', $connect->id)
            ->update(['customers_sync_time' => now()]);

        if ($connect) {
            $client = new Client();
            $username = $connect->username_sapo;
            $password = $connect->password_sapo;
            $store = $connect->store_sapo;

            $today = now()->format('Y-m-d');
            $created_on = $today . 'T00:00:00Z';
            // $created_on = '2025-05-11T00:00:00Z';
            $url = "https://{$username}:{$password}@{$store}/admin/customers.json";
            $url .= "?query=created_on:>={$created_on}"; 

            try {
                Log::info("Đang gửi request đến SAPO: {$url}");

                $response = $client->get($url, [
                    'timeout' => 180,
                    'connect_timeout' => 180,
                    'headers' => [
                        'Accept' => 'application/json',
                        'User-Agent' => 'GuzzleClient/7.0'
                    ]
                ]);

                $data = json_decode($response->getBody(), true);

                Log::info('Số lượng khách hàng nhận được từ SAPO: ' . count($data['customers'] ?? []));

                if (!empty($data['customers'])) {
                    foreach ($data['customers'] as $item) {
                        if (empty($item['email'])) {
                            continue;
                        }

                        DB::table('contacts')->updateOrInsert(
                            ['email' => $item['email']],
                            [
                                'email' => $item['email'] ?? null,
                                'mobileNumber' => $item['phone'] ?? null,
                                'FIRSTNAME' => $item['first_name'] ?? null,
                                'LASTNAME' => $item['last_name'] ?? null,
                                'FULLNAME' => trim($item['first_name'] . ' ' . $item['last_name']) ?? null,
                                'GENDER' => $item['gender'] ?? null,
                                'ADDRESS' => $item['default_address']['address1'] ?? null,
                                'CITY' => $item['default_address']['city'] ?? null,
                                'sapo_store' => $store,
                                'created_at' => now(),
                                'updated_at' => now(),
                                'creat_time' => $item['created_on']
                            ]
                        );
                    }
                } else {
                    Log::info('Không có khách hàng mới từ SAPO.');
                }

            } catch (\GuzzleHttp\Exception\RequestException $e) {
                Log::error('Lỗi RequestException khi gọi SAPO: ' . $e->getMessage());

                if ($e->hasResponse()) {
                    $body = (string) $e->getResponse()->getBody();
                    Log::error('Phản hồi từ SAPO: ' . $body);
                }

            } catch (\Exception $e) {
                Log::error('Lỗi hệ thống khi đồng bộ SAPO: ' . $e->getMessage());
                Log::error($e->getTraceAsString());
            }
        }
    }
}
