<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Print log for Request Docs. A record no longer has a fixed document
 * type — the layout (transcript | provisional | …) is picked each time it
 * is printed, and every print is logged here so a reprint can warn
 * "already printed N times". transcripts.doc_type is left in place but
 * no longer written.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('transcript_prints', function (Blueprint $table) {
            $table->id();
            $table->foreignId('transcript_id')->constrained()->cascadeOnDelete();
            $table->string('doc_type', 20)->index();
            $table->foreignId('printed_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('transcript_prints');
    }
};
