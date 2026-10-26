<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * The Student Status Certification reads either "ជានិស្សិតកំពុងសិក្សាថ្នាក់"
 * (still studying) or "ជានិស្សិតបានបញ្ចប់ការសិក្សាថ្នាក់" (graduated); this
 * picks which, per record.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('transcripts', function (Blueprint $table) {
            if (! Schema::hasColumn('transcripts', 'study_status')) {
                $table->string('study_status', 20)->default('studying')->after('current_semester');
            }
        });
    }

    public function down(): void
    {
        Schema::table('transcripts', function (Blueprint $table) {
            $table->dropColumn('study_status');
        });
    }
};
