<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * "Request Docs" — the transcript form now issues more than one kind of
 * document. doc_type picks the print layout (transcript | provisional);
 * existing rows stay transcripts. The *_kh columns and exam_date are only
 * printed on the bilingual Provisional Certificate.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('transcripts', function (Blueprint $table) {
            if (! Schema::hasColumn('transcripts', 'doc_type')) {
                $table->string('doc_type', 20)->default('transcript')->index()->after('status');
            }
            if (! Schema::hasColumn('transcripts', 'name_kh')) {
                $table->string('name_kh', 150)->nullable()->after('surname');
                $table->string('pob_kh', 150)->nullable()->after('pob');
                $table->date('exam_date')->nullable()->after('academic_year');
                $table->string('person_in_charge_kh', 100)->nullable()->after('person_role');
                $table->string('person_role_kh', 100)->nullable()->after('person_in_charge_kh');
            }
        });
    }

    public function down(): void
    {
        Schema::table('transcripts', function (Blueprint $table) {
            $table->dropColumn(['doc_type', 'name_kh', 'pob_kh', 'exam_date', 'person_in_charge_kh', 'person_role_kh']);
        });
    }
};
