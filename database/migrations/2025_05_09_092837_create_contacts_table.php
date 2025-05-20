<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('contacts', function (Blueprint $table) {
            $table->id();
            $table->string('email')->unique();
            $table->string('mobileNumber')->nullable();
            $table->string('FIRSTNAME')->nullable();
            $table->string('LASTNAME')->nullable();
            $table->string('FULLNAME')->nullable();
            $table->string('GENDER')->nullable();
            $table->string('ADDRESS')->nullable();
            $table->string('CITY')->nullable();

            $table->string('Dotdigital_Sync')->default('pending'); 
            // e.g. 'pending', 'synced', 'error'

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('contacts');
    }
};
