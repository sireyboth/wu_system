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
            $table->string('name')->nullable();
            $table->string('ranking')->nullable();
            $table->string('alpha2')->nullable();
            $table->string('alpha3')->nullable();
            $table->string('nationality')->nullable();
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
