<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * The attendance score's denominator: (present + excused) sessions out of
 * however many the class actually runs. That count can't be derived from
 * class_sessions rows alone — a lecturer who forgets to start attendance
 * one week just means fewer rows, not a shorter term — so the lecturer
 * states it directly. Defaults (15 weeks, 2 sessions/week) match a
 * standard semester; either is editable per class.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('class_score_configs', function (Blueprint $table) {
            $table->unsignedTinyInteger('total_weeks')->default(15)->after('attendance_max');
            $table->unsignedTinyInteger('sessions_per_week')->default(2)->after('total_weeks');
        });
    }

    public function down(): void
    {
        Schema::table('class_score_configs', function (Blueprint $table) {
            $table->dropColumn(['total_weeks', 'sessions_per_week']);
        });
    }
};
