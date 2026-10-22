<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Extra fields printed on the card front: department (shown after the
 * English name), room, and valid_until — which also drives the card's
 * ON DUTY / EXPIRED pill.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('invigilators', function (Blueprint $table) {
            $table->string('department', 100)->nullable()->after('batch');
            $table->string('room', 50)->nullable()->after('department');
            $table->date('valid_until')->nullable()->after('room');
        });
    }

    public function down(): void
    {
        Schema::table('invigilators', function (Blueprint $table) {
            $table->dropColumn(['department', 'room', 'valid_until']);
        });
    }
};
