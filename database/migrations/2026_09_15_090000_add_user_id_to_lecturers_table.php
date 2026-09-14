<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Links a lecturer record to an actual login. Nullable — most lecturers
 * won't have a portal account yet; one is created explicitly via
 * LecturerController::createAccount(), not assumed to exist.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('lecturers', function (Blueprint $table) {
            $table->foreignId('user_id')->nullable()->unique()->after('code')
                ->constrained()->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('lecturers', function (Blueprint $table) {
            $table->dropConstrainedForeignId('user_id');
        });
    }
};
