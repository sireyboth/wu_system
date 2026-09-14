<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Both are advisory signals for risk scoring only — neither blocks a
 * scan, they just feed AttendanceVerification. Nullable because manual
 * marks (method=manual/auto_absent) never come from a scan at all.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('attendance_records', function (Blueprint $table) {
            $table->foreignId('device_id')->nullable()->after('session_roster_id')
                ->constrained('student_devices')->nullOnDelete();
            $table->string('ip_address', 45)->nullable()->after('device_id');
        });
    }

    public function down(): void
    {
        Schema::table('attendance_records', function (Blueprint $table) {
            $table->dropConstrainedForeignId('device_id');
            $table->dropColumn('ip_address');
        });
    }
};
