<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Who teaches a class, historically (supports substitutions). teacher_id
 * points at the existing lecturers table rather than a new "teachers"
 * table — the attendance schema doc's draft used "teachers" before this
 * was wired up against the real shipped Lecturer model.
 */
return new class extends Migration
{
    public function up(): void
    {
        make_fields('teacher_assignments', function (Blueprint $table) {
            $table->foreignId('lecturer_id')->constrained()->cascadeOnDelete();
            $table->foreignId('class_id')->constrained()->cascadeOnDelete();
            $table->string('role', 20)->default('primary')->comment('primary / substitute');
            $table->date('assigned_from');
            $table->date('assigned_to')->nullable()->comment('open-ended until a substitute takes over');
        }, false);
    }

    public function down(): void
    {
        Schema::dropIfExists('teacher_assignments');
    }
};
