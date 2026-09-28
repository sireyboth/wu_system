<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * When the student (or REG, via the manual override) last actually chose
 * this row's is_selected. Null means nobody has chosen yet, so the public
 * page shows the subject unticked whatever is_selected says — rows imported
 * before the import switched to opt-in (is_selected = true) don't need a
 * data fix to start unticked.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('retake_registrations', function (Blueprint $table) {
            $table->dateTime('selection_saved_at')->nullable()->after('is_selected');
        });
    }

    public function down(): void
    {
        Schema::table('retake_registrations', function (Blueprint $table) {
            $table->dropColumn('selection_saved_at');
        });
    }
};
