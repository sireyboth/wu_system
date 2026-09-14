<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Students already log every field change via activity_log (see IModel's
 * getActivitylogOptions), but that's raw per-event JSON diffs — answering
 * "what was this student's batch/major/status as of last semester" would
 * mean replaying every diff in order. This table keeps one directly
 * queryable row per academic snapshot instead: whenever batch/major/group/
 * shift/campus/status/year_level actually changes, the previous row is
 * marked is_current = false and a new one is inserted, so past terms are
 * never rewritten (see StudentController::advanceAcademicHistory()).
 */
return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        make_fields('student_academic_histories', function (Blueprint $table) {
            $table->foreignId('student_id')->constrained()->cascadeOnDelete();
            $table->foreignId('term_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('batch_id')->constrained();
            $table->foreignId('major_id')->constrained();
            $table->foreignId('group_id')->constrained();
            $table->foreignId('shift_id')->constrained();
            $table->foreignId('campus_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('status_id')->constrained();
            $table->unsignedTinyInteger('year_level')->default(1);
            $table->date('effective_date');
            $table->boolean('is_current')->default(true);
        }, false);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('student_academic_histories');
    }
};
