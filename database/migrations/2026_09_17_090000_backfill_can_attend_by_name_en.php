<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

/**
 * The original can_attend backfill (2026_09_14_120000) matched on
 * `shortcut`, which turned out to be blank on real imported data (a
 * known, separate pre-existing bug — see StructureSeeder/shortcut
 * issues flagged elsewhere). That silently left every status'
 * can_attend at its false default in any environment where shortcuts
 * were never populated, production included. This backfills the same
 * intent by name_en instead, which real data does have. Safe to run
 * anywhere, any number of times — it only ever sets can_attend to true
 * for an exact name match, never turns it off, and never touches a
 * status a registrar has already explicitly configured either way
 * beyond this list.
 */
return new class extends Migration
{
    private const ELIGIBLE_NAMES = [
        'Active',
        'Re-enrolled',
        'Campus Move In',
        'Transferred',
        'Add Subject',
        'Retake Subject',
        'Unpaid',
    ];

    public function up(): void
    {
        DB::table('statuses')
            ->whereIn('name_en', self::ELIGIBLE_NAMES)
            ->where('can_attend', false)
            ->update(['can_attend' => true]);
    }

    public function down(): void
    {
        // Deliberately no-op — this is a data correction, not a schema
        // change, and reversing it would silently re-break attendance
        // for whoever it just fixed.
    }
};
