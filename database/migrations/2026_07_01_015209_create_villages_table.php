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
        make_fields('villages', function (Blueprint $table) {
            $table->unsignedInteger('commune_id');
            $table->foreign('commune_id')->references('id')->on('communes')->cascadeOnDelete();
        }, increment: false);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('villages');
    }
};
