<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * Leng's call, 2026-09-14: the attendance QR gate needs to know which
 * statuses may scan in. That list was going to be hardcoded in the
 * attendance controller (active, re_enrolled, campus_move_in,
 * transferred, add_subject, retake_subject, unpaid) — but `unpaid` only
 * surfaced because it showed up in real imported data, not the demo
 * seed, which is exactly the failure mode a hardcoded list can't catch.
 * A flag on the row itself means a new status just needs the checkbox
 * ticked, no code change or deploy.
 */
return new class extends Migration
{
    private const ELIGIBLE_SHORTCUTS = [
        'active',
        're_enrolled',
        'campus_move_in',
        'transferred',
        'add_subject',
        'retake_subject',
        'unpaid',
    ];

    public function up(): void
    {
        Schema::table('statuses', function (Blueprint $table) {
            $table->boolean('can_attend')->default(false)->after('shortcut');
        });

        DB::table('statuses')->whereIn('shortcut', self::ELIGIBLE_SHORTCUTS)->update(['can_attend' => true]);
    }

    public function down(): void
    {
        Schema::table('statuses', function (Blueprint $table) {
            $table->dropColumn('can_attend');
        });
    }
};
