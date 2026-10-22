<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Pivot for "which majors is this class tagged with" — a class can carry
 * several (multi-select on the Create Class form), same label-only role as
 * batch_id on `classes`: this never restricts or drives enrollment, that
 * still stays entirely course_enrollments (see create_classes_table's
 * docblock). Plain pivot, no own id/timestamps needed.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('class_major', function (Blueprint $table) {
            $table->foreignId('class_id')->constrained('classes')->cascadeOnDelete();
            $table->foreignId('major_id')->constrained()->cascadeOnDelete();
            $table->primary(['class_id', 'major_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('class_major');
    }
};
