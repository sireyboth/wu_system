<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * General-purpose invigilator register — standalone on purpose, no link to
 * lecturers/students/batches (batch is free text). public_token is what
 * the printed card's QR points at, so the public page can't be browsed by
 * guessing sequential ids.
 */
return new class extends Migration
{
    public function up(): void
    {
        make_fields('invigilators', function (Blueprint $table) {
            // Not a DB unique — uniqueness is enforced in InvigilatorRequest
            // ignoring trashed rows, so a deleted invigilator's ID can be reused.
            $table->string('code', 50)->index();
            $table->string('batch', 100)->nullable();
            $table->string('public_token', 64)->unique();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('invigilators');
    }
};
