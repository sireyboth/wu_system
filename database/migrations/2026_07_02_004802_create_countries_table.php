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
        make_fields('countries', function (Blueprint $table) {
            $table->string('name', 100)->nullable();
            $table->string('ranking', 8)->nullable();
            $table->string('alpha2', 10)->nullable();
            $table->string('alpha3', 10)->nullable();
            $table->string('nationality', 50)->nullable();
        }, false);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('countries');
    }
};
