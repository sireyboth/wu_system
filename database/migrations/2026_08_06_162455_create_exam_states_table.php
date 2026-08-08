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
            $table->unsignedInteger('no')->nullable();          // row number on the sheet; same 'no' repeats across "Mix" rows
            $table->unsignedInteger('floor_order')->nullable(); // 1,2,3... for sorting; null on data rows after the first per floor
            $table->string('floor', 50)->nullable();            // "1 Floor", "2 Floor"... only set on the row that starts a new floor
            $table->string('room', 50);
            $table->string('shift', 50)->nullable();
            $table->string('major', 50);                       // Morning major, e.g. "CS+CNT+DCA"
            $table->unsignedInteger('students');               // Morning students
            $table->string('degree');                          // BA / AA
            $table->json('majors')->nullable();                // [{ "major": "CS", "total": 15 }, ...] up to 4 entries
            $table->unsignedInteger('sort_order')->nullable(); // preserves exact row order from the sheet
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
