<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/** The mark itself — one row per student per session. See attendance schema doc §5. */
return new class extends Migration
{
    public function up(): void
    {
        make_fields('attendance_records', function (Blueprint $table) {
            $table->foreignId('class_session_id')->constrained()->cascadeOnDelete();
            $table->foreignId('student_id')->constrained()->cascadeOnDelete();
            $table->foreignId('session_roster_id')->constrained()->cascadeOnDelete();
            $table->string('status', 20)->comment('present / late / absent / excused');
            $table->string('method', 20)->comment('qr / manual / auto_leave / auto_absent');
            $table->dateTime('marked_at')->nullable();

            $table->unique(['class_session_id', 'student_id']);
        }, false);
    }

    public function down(): void
    {
        Schema::dropIfExists('attendance_records');
    }
};
