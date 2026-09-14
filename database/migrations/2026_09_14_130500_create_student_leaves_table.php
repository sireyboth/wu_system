<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/** Approved leave, feeds automatic "Excused" attendance status. See attendance schema doc §4. */
return new class extends Migration
{
    public function up(): void
    {
        make_fields('student_leaves', function (Blueprint $table) {
            $table->foreignId('student_id')->constrained()->cascadeOnDelete();
            $table->date('starts_on');
            $table->date('ends_on');
            $table->string('status', 20)->default('pending')->comment('pending / approved / rejected');
            $table->foreignId('approved_by')->nullable()->constrained('users')->nullOnDelete();
        }, false);
    }

    public function down(): void
    {
        Schema::dropIfExists('student_leaves');
    }
};
