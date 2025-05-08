<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::create('connections', function (Blueprint $table) {
            $table->id();
            $table->string('username_sapo');
            $table->string('password_sapo');
            $table->string('store_sapo');
            $table->string('username_dotdigital');
            $table->string('password_dotdigital');
            $table->boolean('active_status')->default(0); // 0 là không kích hoạt, 1 là kích hoạt
            $table->unsignedBigInteger('created_by')->nullable();
            $table->timestamps(); // created_at và updated_at
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down()
    {
        Schema::dropIfExists('connections');
    }
};
