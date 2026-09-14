<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Master data for the attendance system (see StudentAcademicHistory.md's
 * sibling attendance schema doc, §3/§4) — a room is where a class_schedule
 * meets. Bilingual name like the other master tables (campuses, majors).
 */
return new class extends Migration
{
    public function up(): void
    {
        make_fields('rooms', function (Blueprint $table) {
            $table->foreignId('campus_id')->constrained()->cascadeOnDelete();
            $table->string('code', 50)->nullable();
            $table->unsignedSmallInteger('capacity')->nullable();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('rooms');
    }
};
