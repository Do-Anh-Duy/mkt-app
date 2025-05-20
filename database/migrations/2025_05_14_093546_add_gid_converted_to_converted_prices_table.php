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
        Schema::table('converted_prices', function (Blueprint $table) {
            $table->bigInteger('gid_converted')->after('name_converted')->default(0);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('converted_prices', function (Blueprint $table) {
            $table->dropColumn('gid_converted');
        });
    }
};
