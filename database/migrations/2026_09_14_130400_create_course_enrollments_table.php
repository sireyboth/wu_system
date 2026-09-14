<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Links a student's current academic-history row to a specific class.
 * This is the roster — a class has no major_id of its own, so who's in it
 * is entirely determined by these rows. See attendance schema doc §4.
 */
return new class extends Migration
{
    public function up(): void
    {
        make_fields('course_enrollments', function (Blueprint $table) {
            $table->foreignId('student_academic_history_id')->constrained()->cascadeOnDelete();
            $table->foreignId('class_id')->constrained()->cascadeOnDelete();
            $table->string('status', 20)->default('enrolled')->comment('enrolled / dropped');

            $table->unique(['student_academic_history_id', 'class_id']);
        }, false);
    }

    public function down(): void
    {
        Schema::dropIfExists('course_enrollments');
    }
};
