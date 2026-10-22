<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Registered/seen devices — advisory, not restrictive. Deliberately has
 * no student_id: the same device scanning in on behalf of *different*
 * students is exactly the fraud signal this exists to catch, so a device
 * is never "owned" by one student. See attendance schema doc §5/§8.
 */
return new class extends Migration
{
    public function up(): void
    {
        make_fields('student_devices', function (Blueprint $table) {
            $table->string('fingerprint', 64)->unique()->comment('hashed client-generated id, never a raw identifier');
            $table->boolean('trusted')->default(false);
            $table->dateTime('first_seen_at');
            $table->dateTime('last_seen_at');
        }, false);
    }

    public function down(): void
    {
        Schema::dropIfExists('student_devices');
    }
};
