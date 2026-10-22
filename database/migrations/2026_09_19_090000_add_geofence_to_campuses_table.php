<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Optional GPS geofence per campus — a student scanning the attendance QR
 * from outside this radius gets rejected. Nullable on purpose: a campus
 * with no coordinates set has geofencing simply off, so this can be rolled
 * out campus-by-campus instead of needing every campus configured at once.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('campuses', function (Blueprint $table) {
            $table->decimal('latitude', 10, 7)->nullable()->after('shortcut');
            $table->decimal('longitude', 10, 7)->nullable()->after('latitude');
            $table->unsignedInteger('attendance_radius_meters')->nullable()->after('longitude');
        });
    }

    public function down(): void
    {
        Schema::table('campuses', function (Blueprint $table) {
            $table->dropColumn(['latitude', 'longitude', 'attendance_radius_meters']);
        });
    }
};
