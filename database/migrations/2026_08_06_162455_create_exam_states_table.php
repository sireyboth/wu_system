<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        make_fields('exam_states', function (Blueprint $table) {
            $table->unsignedInteger('no')->nullable();      // row number on the sheet; same 'no' repeats across "Mix" rows
            $table->string('room', 50);                           // Room name, e.g. "101"
            $table->string('shift', 50)->nullable();              // Morning / Afternoon / Evening
            $table->string('major', 100);                         // Morning major, e.g. "CS+CNT+DCA"
            $table->unsignedInteger('student_total')->default(0); // Total of all students in the morning shift
            $table->string('degree', 50);                         // BA / AA
            $table->json('majors')->nullable();                   // [{ "major": "CS", "total": 15 }, ...] up to 4 entries
            $table->json('absences')->nullable();                 // [{ "time": "08:00 AM", "total": 2 }, ...] up to 4 entries
            $table->date('exam_date')->default(now()->toDateString());
        }, false);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('exam_states');
    }
};
