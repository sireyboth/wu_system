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
            $table->foreignId('major_id')->constrained()->cascadeOnDelete();
            $table->string('code', 50)->unique()->nullable();
            $table->unsignedTinyInteger('year_level')->default(1);
            $table->unsignedTinyInteger('semester')->default(1);
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
