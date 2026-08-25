<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Heads-up pings (1 day before start / end) are no longer a per-alert
     * opt-in — they always fire automatically now, so the toggle columns
     * are dead weight.
     */
    public function up(): void
    {
        Schema::table('alerts', function (Blueprint $table) {
            $table->dropColumn(['remind_before_start', 'remind_before_end']);
        });
    }

    public function down(): void
    {
        Schema::table('alerts', function (Blueprint $table) {
            $table->boolean('remind_before_start')->default(true)->after('status');
            $table->boolean('remind_before_end')->default(true)->after('remind_before_start');
        });
    }
};
