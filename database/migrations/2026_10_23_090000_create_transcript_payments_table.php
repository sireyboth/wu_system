<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Payment verification on Request Docs — one row per year/semester the
 * registrar has checked as paid (no row = unpaid). The degree decides how
 * many semesters are required (Degree::years() × 2); any missing one
 * blocks printing. verified_by/verified_at keep who ticked it and when.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('transcript_payments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('transcript_id')->constrained()->cascadeOnDelete();
            $table->unsignedTinyInteger('year');
            $table->unsignedTinyInteger('semester');
            $table->foreignId('verified_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('verified_at')->nullable();
            $table->timestamps();

            $table->unique(['transcript_id', 'year', 'semester']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('transcript_payments');
    }
};
