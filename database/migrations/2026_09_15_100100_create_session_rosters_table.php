<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Expected attendees, frozen at the moment a session starts — a student
 * added to the class after the session already opened doesn't retroactively
 * show up on a session in progress. See attendance schema doc §5.
 */
return new class extends Migration
{
    public function up(): void
    {
        make_fields('session_rosters', function (Blueprint $table) {
            $table->foreignId('class_session_id')->constrained()->cascadeOnDelete();
            $table->foreignId('course_enrollment_id')->constrained()->cascadeOnDelete();

            $table->unique(['class_session_id', 'course_enrollment_id']);
        }, false);
    }

    public function down(): void
    {
        Schema::dropIfExists('session_rosters');
    }
};
