<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Connection;
use App\Models\Contact;
use GuzzleHttp\Client;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Http;
use Illuminate\Http\Request;

class SyncDotdigitalCustomersJob extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:sync-dotdigital-customers-job';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Đồng bộ thông tin khách hàng từ MKT-APP về Dotdigital';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $contact = Contact::where('Dotdigital_Sync', 'pending')
            ->whereNotNull('mobileNumber')
            ->orderBy('created_at', 'asc')
            ->first();

        if($contact){

            $connect = Connection::where('store_sapo', $contact->sapo_store)->get()->toArray();
            if (!$connect) {
                return;
            }
            $username = $connect[0]['username_dotdigital'];
            $password = $connect[0]['password_dotdigital'];

            $auth = base64_encode($username . ':' . $password);

            $response = Http::withHeaders([
                'Accept' => 'application/json',
                'Authorization' => 'Basic ' . $auth,
            ])->post('https://r3-api.dotdigital.com/contacts/v3', [
                'identifiers' => [
                    'email' => $contact->email,
                    'mobileNumber' => $contact->mobileNumber,
                ],
                'lists' => [
                    1677545,
                ],
                'consentRecords' => [
                    [
                        'text' => 'Yes, I would like to receive a monthly newsletter',
                        'dateTimeConsented' => $contact->creat_time,
                        'url' => 'http://www.example.com/signup',
                        'ipAddress' => '129.168.0.2',
                        'userAgent' => 'Mozilla/5.0 (X11; OpenBSD i386) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/36.0.1985.125 Safari/537.36',
                    ],
                ],
                'contactDetails' => [
                    'FIRSTNAME' => $contact->FIRSTNAME ?? NULL,
                    'LASTNAME' => $contact->LASTNAME ?? NULL,
                    'FULLNAME' => $contact->FULLNAME ?? NULL,
                    'GENDER' => $contact->GENDER ?? NULL,
                    'ADDRESS' => $contact->ADDRESS ?? NULL,
                    'CITY' => $contact->CITY ?? NULL,
                ],
            ]);

            if ($response->successful()) {
                DB::table('contacts')
                    ->where('id', $contact->id)
                    ->update(['Dotdigital_Sync' => 'synced']);

                $check = base64_encode($username . ':' . $password);
                $response = Http::withHeaders([
                'Accept' => 'application/json',
                'Authorization' => 'Basic ' . $check,
                ])->put("https://r3-api.dotdigital.com/contacts/v3/email/{$contact->email}", [
                    'identifiers' => [
                        'email' => $contact->email,
                        'mobileNumber' => $contact->mobileNumber ?? NULL,
                    ],
                    'lists' => [
                        1677545,
                    ],
                    'dataFields' => [
                        'FIRSTNAME' => $contact->FIRSTNAME ?? NULL,
                        'LASTNAME' => $contact->LASTNAME ?? NULL,
                        'FULLNAME' => $contact->FULLNAME ?? NULL,
                        'GENDER' => $contact->GENDER ?? NULL,
                        'ADDRESS' => $contact->ADDRESS ?? NULL,
                        'CITY' => $contact->CITY ?? NULL,
                    ],
                ]);
            } else{
                $errorData = $response->json();
                DB::table('contacts')
                    ->where('id', $contact->id)
                    ->update(['Dotdigital_Sync' => $errorData['details'][0]['description'] ?? 'error']);

                $check = base64_encode($username . ':' . $password);
                $response = Http::withHeaders([
                'Accept' => 'application/json',
                'Authorization' => 'Basic ' . $check,
                ])->put("https://r3-api.dotdigital.com/contacts/v3/email/{$contact->email}", [
                    'identifiers' => [
                        'email' => $contact->email,
                        'mobileNumber' => $contact->mobileNumber ?? NULL,
                    ],
                    'lists' => [
                        1677545,
                    ],
                    'dataFields' => [
                        'FIRSTNAME' => $contact->FIRSTNAME ?? NULL,
                        'LASTNAME' => $contact->LASTNAME ?? NULL,
                        'FULLNAME' => $contact->FULLNAME ?? NULL,
                        'GENDER' => $contact->GENDER ?? NULL,
                        'ADDRESS' => $contact->ADDRESS ?? NULL,
                        'CITY' => $contact->CITY ?? NULL,
                    ],
                ]);
            }
        }
    }
}
