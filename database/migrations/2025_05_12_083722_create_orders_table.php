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
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->string('order_number', 100)->nullable();
            $table->string('email')->nullable();
            $table->string('phone', 50)->nullable();
            $table->string('currency', 10)->nullable();
            $table->string('status', 50)->nullable();
            $table->bigInteger('subtotal_price')->nullable();
            $table->string('Dotdigital_Sync')->default('pending');
            $table->string('sapo_store')->nullable();
            $table->string('created_time')->nullable();
            $table->timestamp('created_at')->useCurrent()->nullable();
            $table->dateTime('created_on')->nullable();
            $table->integer('campaign_id')->default(0);
            $table->string('campaign_name')->nullable();
            $table->string('sapo_name')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
};
