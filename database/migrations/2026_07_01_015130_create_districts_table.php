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
        make_fields('districts', function (Blueprint $table) {
            $table->unsignedInteger('province_id');
            $table->foreign('province_id')->references('id')->on('provinces')->cascadeOnDelete();
        }, increment: false);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('districts');
    }
};
