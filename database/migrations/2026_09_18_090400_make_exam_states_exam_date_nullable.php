<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * exam_states.exam_date was NOT NULL with only a DB-level default — but
 * ExamStateRequest already validates it as nullable, and the create-room
 * form has no `required` on this field. Whenever it's actually left
 * blank, Eloquent inserts an explicit NULL (bypassing the DB default
 * entirely, since the key is present in the INSERT), which the NOT NULL
 * constraint then rejects with a 500. Pre-existing bug, unrelated to
 * Exam Terms — just surfaced by testing the new create-room flow.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('exam_states', function (Blueprint $table) {
            $table->date('exam_date')->nullable()->default(null)->change();
        });
    }

    public function down(): void
    {
        Schema::table('exam_states', function (Blueprint $table) {
            $table->date('exam_date')->default(now()->toDateString())->change();
        });
    }
};
