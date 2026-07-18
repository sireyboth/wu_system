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
        Schema::create('number_counters', function (Blueprint $table) {
            $table->id();
            $table->unsignedSmallInteger('year');
            $table->string('type', 50)->default('status');
            $table->unsignedInteger('last_sequence')->default(0);

            $table->unique(['year', 'type']);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('number_counters');
    }
};
