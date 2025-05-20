<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;
Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

//Đồng bộ khách hàng từ SAPO về MKT-APP (5 phút/ lần)
Schedule::command('app:sync-sapo-customers-job')->everyFiveMinutes();
//Đồng bộ đơn hàng từ SAPO về MKT-APP (5 phút/ lần)
Schedule::command('app:sync-sapo-orders-job')->everyFiveMinutes();
//Đồng bộ khách hàng từ MKT-APP lên Dotdigital (1 phút/ lần)
Schedule::command('app:sync-dotdigital-customers-job')->everyMinute();
//Đồng bộ đơn hàng từ MKT-APP lên Dotdigital (1 phút/ lần)
Schedule::command('app:sync-dotdigital-orders-job')->everyMinute();