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
        make_fields('events', function (Blueprint $table) {
            $table->string('title');
            $table->string('subtitle')->nullable();
            $table->dateTime('start');
            $table->dateTime('end')->nullable();
            $table->string('color')->nullable();
            $table->string('repeat_freq')->nullable();      // daily, weekly, monthly, null = no repeat
            $table->integer('repeat_interval')->default(1); // every N days/weeks/months
            $table->dateTime('repeat_until')->nullable();   // when repetition stops
        }, false);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('events');
    }
};
