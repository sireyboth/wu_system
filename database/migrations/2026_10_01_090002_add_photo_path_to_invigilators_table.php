<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Invigilator photo — stored on the public disk but served through
 * InvigilatorController::photo (same reason as payment proofs: works
 * without the storage:link symlink).
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('invigilators', function (Blueprint $table) {
            $table->string('photo_path')->nullable()->after('batch');
        });
    }

    public function down(): void
    {
        Schema::table('invigilators', function (Blueprint $table) {
            $table->dropColumn('photo_path');
        });
    }
};
