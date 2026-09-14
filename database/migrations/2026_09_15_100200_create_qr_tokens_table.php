<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Short-lived rotating tokens per session. Only the hash is stored — the
 * raw token is handed to the lecturer's browser to render as a QR code
 * and is never persisted in plaintext. See attendance schema doc §5/§6.
 */
return new class extends Migration
{
    public function up(): void
    {
        make_fields('qr_tokens', function (Blueprint $table) {
            $table->foreignId('class_session_id')->constrained()->cascadeOnDelete();
            $table->string('token_hash', 64)->unique();
            $table->unsignedInteger('sequence');
            $table->dateTime('issued_at');
            $table->dateTime('expires_at');

            $table->index(['class_session_id', 'sequence']);
        }, false);
    }

    public function down(): void
    {
        Schema::dropIfExists('qr_tokens');
    }
};
