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
        Schema::create('converted_prices', function (Blueprint $table) {
            $table->id();
            $table->string('sapo_name')->nullable();
            $table->string('name_converted')->nullable();
            $table->boolean('active_status')->default(0); // 0 là không kích hoạt, 1 là kích hoạt
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('converted_prices');
    }
};
