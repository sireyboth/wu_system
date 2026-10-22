<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

/**
 * A manageable list of exam programs — State Exam, Scholarship, Entrance
 * Exam, whatever comes next — so the exam-room/invigilator/report system
 * (originally hardcoded to just "state exam") can serve more than one
 * program without a code change every time a new one shows up.
 */
return new class extends Migration
{
    public function up(): void
    {
        make_fields('exam_categories', function (Blueprint $table) {
            //
        });
    }

    public function down(): void
    {
        \Illuminate\Support\Facades\Schema::dropIfExists('exam_categories');
    }
};
