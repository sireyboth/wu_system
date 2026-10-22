<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * One offering/section of a subject, one term. Deliberately has no
 * major_id — who's in it is entirely determined by course_enrollments
 * rows, which is what makes mixed-major classes just work (a shared
 * elective across 2-3 majors, say). See the attendance schema doc §4.
 */
return new class extends Migration
{
    public function up(): void
    {
        make_fields('classes', function (Blueprint $table) {
            $table->foreignId('subject_id')->constrained()->cascadeOnDelete();
            $table->foreignId('term_id')->constrained()->cascadeOnDelete();
            $table->foreignId('campus_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('shift_id')->nullable()->constrained()->nullOnDelete();
            $table->unsignedSmallInteger('capacity')->nullable();
            $table->string('code', 50);

            $table->unique(['term_id', 'code']);
        }, false);
    }

    public function down(): void
    {
        Schema::dropIfExists('classes');
    }
};
