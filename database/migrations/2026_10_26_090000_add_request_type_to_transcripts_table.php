<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * What the student asked for decides the print rules: a Transcript /
 * Student Status only checks the semesters entered, a Provisional
 * Certificate needs the whole degree plus the comprehensive exam passed.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('transcripts', function (Blueprint $table) {
            if (! Schema::hasColumn('transcripts', 'request_type')) {
                $table->string('request_type', 20)->default('transcript')->after('status');
            }
            if (! Schema::hasColumn('transcripts', 'comprehensive_exam')) {
                $table->boolean('comprehensive_exam')->default(false)->after('exam_date');
            }
        });
    }

    public function down(): void
    {
        Schema::table('transcripts', function (Blueprint $table) {
            $table->dropColumn(['request_type', 'comprehensive_exam']);
        });
    }
};
