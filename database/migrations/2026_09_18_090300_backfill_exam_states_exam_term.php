<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

/**
 * Every exam_states row created before Exam Terms existed has no
 * exam_term_id — this gives them all one, so nothing about the existing
 * State Exam attendance/invigilator/report pages breaks. Deliberately a
 * no-op when there's nothing to backfill (fresh installs, dev databases
 * with no exam_states rows yet).
 */
return new class extends Migration
{
    public function up(): void
    {
        if (! DB::table('exam_states')->whereNull('exam_term_id')->exists()) {
            return;
        }

        $categoryId = DB::table('exam_categories')->where('name_en', 'State Exam')->value('id');
        if (! $categoryId) {
            $categoryId = DB::table('exam_categories')->insertGetId([
                'name_en'    => 'State Exam',
                'name_kh'    => 'ប្រឡងបញ្ចប់ការសិក្សា',
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        $campusId = DB::table('campuses')->orderBy('id')->value('id');
        if (! $campusId) {
            // No campus exists at all — nothing sensible to backfill against;
            // leave existing rows term-less rather than guess a campus.
            return;
        }

        $termId = DB::table('exam_terms')->insertGetId([
            'exam_category_id' => $categoryId,
            'campus_id'         => $campusId,
            'title'             => 'State Exam',
            'exam_date'         => DB::table('exam_states')->max('exam_date'),
            // Matches the old hardcoded 3-round system exactly, so
            // existing absences/invigilators arrays (positioned 0/1/2)
            // keep meaning exactly what they meant before this migration.
            'time_slots'        => json_encode(['ម៉ោងទី១ (Session 1)', 'ម៉ោងទី២ (Session 2)', 'ម៉ោងទី៣ (Session 3)']),
            'is_active'         => true,
            'created_at'        => now(),
            'updated_at'        => now(),
        ]);

        DB::table('exam_states')->whereNull('exam_term_id')->update(['exam_term_id' => $termId]);
    }

    public function down(): void
    {
        // Deliberate no-op — reversing this would silently detach real
        // exam rooms from their term again.
    }
};
