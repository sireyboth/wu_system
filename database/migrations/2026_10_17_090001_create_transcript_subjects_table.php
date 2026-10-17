<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * One course line on a transcript. code/title are snapshots of the
 * subject at save time, so renaming a subject later never rewrites an
 * issued transcript. score null = "IP" (in progress): no grade, and its
 * credit is left out of the totals.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('transcript_subjects', function (Blueprint $table) {
            $table->id();
            $table->foreignId('transcript_id')->constrained()->cascadeOnDelete();
            $table->foreignId('subject_id')->nullable()->constrained()->nullOnDelete();
            $table->unsignedTinyInteger('year');
            $table->unsignedTinyInteger('semester');
            $table->unsignedTinyInteger('position')->default(0);
            $table->string('code', 50)->nullable();
            $table->string('title', 150);
            $table->unsignedTinyInteger('credit')->default(0);
            $table->decimal('score', 5, 2)->nullable();
            $table->decimal('grade_point', 3, 2)->nullable();
            $table->string('grade', 3)->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('transcript_subjects');
    }
};
