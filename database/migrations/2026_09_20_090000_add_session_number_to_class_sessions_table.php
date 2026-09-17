<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * A class can meet more than once on the same calendar date — e.g. one
 * lecturer's 3-hour block split by a break into two attendance checks, or
 * an actual morning + afternoon sitting. session_number distinguishes
 * those; the old (class_id, session_date) uniqueness meant a second
 * session on the same day was impossible — the lecturer could only ever
 * reopen the first one, even after it was locked.
 */
return new class extends Migration
{
    public function up(): void
    {
        // Guarded — an earlier, partially-applied run of this same migration
        // (or a manual fix) may have already added the column/index.
        if (! Schema::hasColumn('class_sessions', 'session_number')) {
            Schema::table('class_sessions', function (Blueprint $table) {
                $table->unsignedTinyInteger('session_number')->default(1)->after('session_date');
            });
        }

        // class_id has a FK (class_sessions.class_id -> classes.id), and
        // MySQL/InnoDB requires *some* index covering a FK column to exist
        // at all times — it refuses to drop the old (class_id, session_date)
        // unique if that's the only index backing it. Both indexes below
        // start with class_id, so adding the new one FIRST keeps the FK
        // continuously backed and lets the old one drop cleanly right after,
        // with no need to touch the foreign key itself.
        if (! Schema::hasIndex('class_sessions', ['class_id', 'session_date', 'session_number'], 'unique')) {
            Schema::table('class_sessions', function (Blueprint $table) {
                $table->unique(['class_id', 'session_date', 'session_number']);
            });
        }

        if (Schema::hasIndex('class_sessions', ['class_id', 'session_date'], 'unique')) {
            Schema::table('class_sessions', function (Blueprint $table) {
                $table->dropUnique(['class_id', 'session_date']);
            });
        }
    }

    public function down(): void
    {
        // Same reasoning as up() — add the old index back before dropping
        // the new one, so class_id's FK is never left unbacked.
        if (! Schema::hasIndex('class_sessions', ['class_id', 'session_date'], 'unique')) {
            Schema::table('class_sessions', function (Blueprint $table) {
                $table->unique(['class_id', 'session_date']);
            });
        }

        if (Schema::hasIndex('class_sessions', ['class_id', 'session_date', 'session_number'], 'unique')) {
            Schema::table('class_sessions', function (Blueprint $table) {
                $table->dropUnique(['class_id', 'session_date', 'session_number']);
            });
        }

        Schema::table('class_sessions', function (Blueprint $table) {
            $table->dropColumn('session_number');
        });
    }
};
