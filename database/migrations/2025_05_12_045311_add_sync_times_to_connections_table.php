<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('connections', function (Blueprint $table) {
            $table->timestamp('customers_sync_time')->nullable()->after('updated_at');
            $table->timestamp('orders_sync_time')->nullable()->after('customers_sync_time');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('connections', function (Blueprint $table) {
            $table->dropColumn(['customers_sync_time', 'orders_sync_time']);
        });
    }
};
