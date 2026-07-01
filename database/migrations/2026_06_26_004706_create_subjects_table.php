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
        make_fields('subjects', function (Blueprint $table) {
            $table->string('code', 50)->unique()->nullable();
            $table->enum('year', ['year 1', 'year 2', 'year 3', 'year 4'])->default('year 1');
            $table->enum('semester', ['semester 1', 'semester 2'])->default('semester 1');
            $table->integer('credit')->default(0);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('subjects');
    }
};
