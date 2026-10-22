<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/** Recurring weekly meeting pattern for a class. See attendance schema doc §4. */
return new class extends Migration
{
    public function up(): void
    {
        make_fields('class_schedules', function (Blueprint $table) {
            $table->foreignId('class_id')->constrained()->cascadeOnDelete();
            $table->foreignId('room_id')->nullable()->constrained()->nullOnDelete();
            $table->unsignedTinyInteger('day_of_week')->comment('0-6, Sunday = 0');
            $table->time('starts_at');
            $table->time('ends_at');

            $table->unique(['room_id', 'day_of_week', 'starts_at']);
        }, false);
    }

    public function down(): void
    {
        Schema::dropIfExists('class_schedules');
    }
};
