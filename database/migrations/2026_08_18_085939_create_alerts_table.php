<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        make_fields('alerts', function (Blueprint $table) {
            $table->string('title', 150);
            $table->string('sub_title', 150)->nullable();
            $table->text('content');
            $table->string('category', 50)->nullable();

            $table->dateTime('start_date');
            $table->dateTime('end_date');
            $table->text('note')->nullable();

            // pending|completed
            $table->string('status', 20)->default('pending');
            $table->dateTime('completed_at')->nullable();

            // One-shot "heads up" pings — 1 day before start / end, togglable.
            $table->boolean('remind_before_start')->default(false);
            $table->boolean('remind_before_end')->default(false);

            // Recurrence: when completed (or lapsed), re-arm the same alert to
            // its next occurrence instead of creating a new row. none|daily|weekly|monthly|yearly
            $table->string('repeat_type', 20)->default('none');
            $table->unsignedInteger('repeat_interval')->default(1);
            $table->date('repeat_until')->nullable();

            // Repeating nag while active: fires every N minutes from start_date
            // onward (through overdue) until marked complete or snoozed.
            $table->boolean('remind_enabled')->default(false);
            $table->unsignedInteger('remind_interval_minutes')->nullable();

            $table->boolean('telegram_enabled')->default(true);
            $table->dateTime('snoozed_until')->nullable();
        }, false);
    }

    public function down(): void
    {
        Schema::dropIfExists('alerts');
    }
};
