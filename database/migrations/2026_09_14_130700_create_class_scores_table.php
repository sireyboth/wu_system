<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * One row per student per class per component, except attendance —
 * attendance is computed live from attendance_records rather than typed
 * in here (still an open question per attendance schema doc §8, so no
 * attendance component is stored yet). See doc §4 "Scoring".
 *
 * Named class_scores, not scores — the retake-exam module already owns a
 * "scores" table (one row per retake_registration), unrelated to this.
 */
return new class extends Migration
{
    public function up(): void
    {
        make_fields('class_scores', function (Blueprint $table) {
            $table->foreignId('course_enrollment_id')->constrained()->cascadeOnDelete();
            $table->string('component', 20)->comment('homework / quiz / assignment / midterm / final');
            $table->decimal('points', 5, 2);
            $table->foreignId('recorded_by')->nullable()->constrained('users')->nullOnDelete();

            $table->unique(['course_enrollment_id', 'component']);
        }, false);
    }

    public function down(): void
    {
        Schema::dropIfExists('class_scores');
    }
};
