<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

/**
 * One real exam event — a specific date, campus, and category (e.g.
 * "Scholarship Exam — Sep 2026"), with its own list of real time slots.
 * Every exam_states room row belongs to exactly one of these, which is
 * what lets the same room/invigilator/report mechanics serve multiple,
 * independently-schedulable exam programs at once instead of the single
 * hardcoded "3 rounds" the state-exam module used to assume.
 *
 * is_active gates the PUBLIC pages only (attendance entry, invigilator
 * lookup) — a deactivated term's rooms simply can't be found by on-site
 * staff anymore, though registrars can still see/edit them from the
 * admin side and still pick this term when filtering a report.
 */
return new class extends Migration
{
    public function up(): void
    {
        make_fields('exam_terms', function (Blueprint $table) {
            $table->foreignId('exam_category_id')->constrained()->cascadeOnDelete();
            $table->foreignId('campus_id')->constrained()->cascadeOnDelete();
            $table->string('title', 150);
            $table->date('exam_date')->nullable();
            // Ordered list of real time-slot labels, e.g. ["7:30-9:00",
            // "9:10-10:40"] — however many this exam day actually needs,
            // not a fixed count. A room's absences/invigilators arrays
            // are positioned against this same list, by index.
            $table->json('time_slots')->nullable();
            $table->boolean('is_active')->default(false);
        }, false);
    }

    public function down(): void
    {
        \Illuminate\Support\Facades\Schema::dropIfExists('exam_terms');
    }
};
