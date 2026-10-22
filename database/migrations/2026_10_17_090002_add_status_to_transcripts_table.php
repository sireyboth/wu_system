<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Draft mode — a transcript can be saved half-filled (missing N°, name,
 * subject picks…) and finished later. Drafts can't be printed; a normal
 * Save runs the full validation and flips status to 'final'. The columns
 * a draft may leave empty become nullable.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('transcripts', function (Blueprint $table) {
            if (! Schema::hasColumn('transcripts', 'status')) {
                $table->string('status', 10)->default('final')->index()->after('id');
            }
            $table->string('issue_no', 100)->nullable()->change();
            $table->string('name', 100)->nullable()->change();
            $table->string('surname', 100)->nullable()->change();
        });

        Schema::table('transcript_subjects', function (Blueprint $table) {
            $table->string('title', 150)->nullable()->change();
        });
    }

    public function down(): void
    {
        Schema::table('transcripts', function (Blueprint $table) {
            if (Schema::hasColumn('transcripts', 'status')) {
                $table->dropColumn('status');
            }
        });
    }
};
