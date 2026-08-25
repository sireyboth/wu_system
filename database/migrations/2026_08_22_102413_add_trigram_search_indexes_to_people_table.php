<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

/**
 * Student search filters on people.first_name/last_name/first_name_kh/
 * last_name_kh/email via a leading-wildcard LIKE '%term%' (see whereLike in
 * AppServiceProvider), which a normal B-tree index can't accelerate — the
 * DB can't binary-search a string when it doesn't know where the match
 * starts. pg_trgm's GIN trigram index is built for exactly this: it indexes
 * every 3-character sequence in the column, so substring search — including
 * partial Khmer names — stays fast as the table grows, without changing the
 * query itself. SQLite (local dev) doesn't support pg_trgm, so this is a
 * no-op there; production runs Postgres.
 */
return new class extends Migration
{
    protected array $columns = ['first_name', 'last_name', 'first_name_kh', 'last_name_kh', 'email'];

    public function up(): void
    {
        if (DB::connection()->getDriverName() !== 'pgsql') {
            return;
        }

        DB::statement('CREATE EXTENSION IF NOT EXISTS pg_trgm');

        foreach ($this->columns as $column) {
            DB::statement("CREATE INDEX IF NOT EXISTS people_{$column}_trgm_idx ON people USING GIN ({$column} gin_trgm_ops)");
        }
    }

    public function down(): void
    {
        if (DB::connection()->getDriverName() !== 'pgsql') {
            return;
        }

        foreach ($this->columns as $column) {
            DB::statement("DROP INDEX IF EXISTS people_{$column}_trgm_idx");
        }
    }
};
