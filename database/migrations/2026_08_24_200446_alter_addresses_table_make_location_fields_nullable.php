<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Staff can now leave province/district/commune/village as "មិនមាន"
     * (not available) when a student doesn't remember their address,
     * instead of the form being blocked until every level is filled in.
     */
    public function up(): void
    {
        Schema::table('addresses', function (Blueprint $table) {
            $table->foreignId('province_id')->nullable()->change();
            $table->foreignId('district_id')->nullable()->change();
            $table->foreignId('commune_id')->nullable()->change();
            $table->foreignId('village_id')->nullable()->change();
        });
    }

    public function down(): void
    {
        Schema::table('addresses', function (Blueprint $table) {
            $table->foreignId('province_id')->nullable(false)->change();
            $table->foreignId('district_id')->nullable(false)->change();
            $table->foreignId('commune_id')->nullable(false)->change();
            $table->foreignId('village_id')->nullable(false)->change();
        });
    }
};
