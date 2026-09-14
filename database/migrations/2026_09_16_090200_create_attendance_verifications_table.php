<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Risk signals, one per contested/scored record — created only when a
 * scan actually trips a signal (not one row per clean scan), so the
 * registrar review queue stays meaningful. Flagging never blocks the
 * scan itself; the student is still marked present, this just queues the
 * record for a human look. See attendance schema doc §5/§6.
 */
return new class extends Migration
{
    public function up(): void
    {
        make_fields('attendance_verifications', function (Blueprint $table) {
            $table->foreignId('attendance_record_id')->unique()->constrained()->cascadeOnDelete();
            $table->unsignedTinyInteger('risk_score');
            $table->json('signals');
            $table->boolean('flagged')->default(true);
            $table->foreignId('reviewed_by')->nullable()->constrained('users')->nullOnDelete();
            $table->dateTime('reviewed_at')->nullable();
        }, false);
    }

    public function down(): void
    {
        Schema::dropIfExists('attendance_verifications');
    }
};
