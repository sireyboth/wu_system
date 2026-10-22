<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * The only path to change a locked attendance record. A lecturer
 * requests it (e.g. "this student's phone died, I marked them absent by
 * mistake"); a registrar approves or rejects. Approving is what actually
 * updates the underlying attendance_records row — the request itself
 * never does. See attendance schema doc §5/§6.
 */
return new class extends Migration
{
    public function up(): void
    {
        make_fields('attendance_corrections', function (Blueprint $table) {
            $table->foreignId('attendance_record_id')->constrained()->cascadeOnDelete();
            $table->foreignId('requested_by')->constrained('users')->cascadeOnDelete();
            $table->foreignId('approved_by')->nullable()->constrained('users')->nullOnDelete();
            $table->string('old_status', 20);
            $table->string('new_status', 20);
            $table->text('reason');
            $table->string('status', 20)->default('pending')->comment('pending / approved / rejected');
        }, false);
    }

    public function down(): void
    {
        Schema::dropIfExists('attendance_corrections');
    }
};
