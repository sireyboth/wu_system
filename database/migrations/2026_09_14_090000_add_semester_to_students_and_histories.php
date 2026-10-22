<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Semester (1 or 2) is a per-student fact, independent of whichever Term
 * happens to be globally active — two students can legitimately be on
 * different semesters of their own program at the same calendar moment
 * (a retake, a deferral, ...). Nullable everywhere: none of the already
 * -imported students have a real semester value, and there's no safe
 * default to guess at — see StudentImport, which fills this in on a
 * re-import without ever overwriting a value that's already set.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('students', function (Blueprint $table) {
            $table->unsignedTinyInteger('semester')->nullable()->after('year_level');
        });

        Schema::table('student_academic_histories', function (Blueprint $table) {
            $table->unsignedTinyInteger('semester')->nullable()->after('year_level');
        });
    }

    public function down(): void
    {
        Schema::table('students', function (Blueprint $table) {
            $table->dropColumn('semester');
        });

        Schema::table('student_academic_histories', function (Blueprint $table) {
            $table->dropColumn('semester');
        });
    }
};
