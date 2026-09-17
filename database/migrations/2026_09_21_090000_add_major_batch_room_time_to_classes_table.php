<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Three optional descriptive fields, added on request — none of them
 * change how enrollment/attendance actually works (still driven by
 * course_enrollments and class_schedules respectively):
 *   - batch_id: a label only. Mixed-batch classes still work exactly as
 *     before (see create_classes_table's docblock) — this doesn't filter
 *     or restrict enrollment, it's just what shows on the class row.
 *   - room_number: plain text, NOT a foreign key to `rooms` — deliberately
 *     simpler than the existing class_schedules.room_id (which has no UI
 *     yet), since this is "one room for this class," not a weekly
 *     schedule.
 *   - time_slot: one of three fixed preset strings, not a real time()
 *     column — same reasoning, simpler than class_schedules' starts_at/
 *     ends_at (which also need a day_of_week this doesn't ask for).
 *
 * Major is NOT here — a class can be tagged with several majors at once
 * (see the class_major pivot migration right after this one), so it can't
 * be a single nullable column the way batch/room/time are.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('classes', function (Blueprint $table) {
            $table->foreignId('batch_id')->nullable()->after('subject_id')->constrained()->nullOnDelete();
            $table->string('room_number', 50)->nullable()->after('capacity');
            $table->string('time_slot', 50)->nullable()->after('room_number');
        });
    }

    public function down(): void
    {
        Schema::table('classes', function (Blueprint $table) {
            $table->dropConstrainedForeignId('batch_id');
            $table->dropColumn(['room_number', 'time_slot']);
        });
    }
};
