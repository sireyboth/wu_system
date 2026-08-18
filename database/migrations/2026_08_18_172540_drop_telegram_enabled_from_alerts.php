<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Every alert notifies via Telegram now — no per-alert opt-out, matching
     * the same "don't ask, just do it" treatment applied to heads-up pings.
     */
    public function up(): void
    {
        Schema::table('alerts', function (Blueprint $table) {
            $table->dropColumn('telegram_enabled');
        });
    }

    public function down(): void
    {
        Schema::table('alerts', function (Blueprint $table) {
            $table->boolean('telegram_enabled')->default(true)->after('remind_interval_minutes');
        });
    }
};
