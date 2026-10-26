<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Student Status Certification — the third Request Docs layout. It states
 * "currently pursuing year X, semester Y", which nothing else on the record
 * holds, so both are typed once on the form and kept here as free text.
 * address_kh is the Khmer twin of address for the bilingual letter.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('transcripts', function (Blueprint $table) {
            if (! Schema::hasColumn('transcripts', 'address_kh')) {
                $table->string('address_kh', 255)->nullable()->after('address');
            }
            if (! Schema::hasColumn('transcripts', 'current_year')) {
                $table->string('current_year', 20)->nullable()->after('academic_year');
                $table->string('current_semester', 20)->nullable()->after('current_year');
            }
        });
    }

    public function down(): void
    {
        Schema::table('transcripts', function (Blueprint $table) {
            $table->dropColumn(['address_kh', 'current_year', 'current_semester']);
        });
    }
};
