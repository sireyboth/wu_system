<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        make_fields('student_snapshots', function (Blueprint $table) {
            $table->foreignId('student_id')->constrained()->cascadeOnDelete();
            $table->foreignId('batch_id')->constrained();
            $table->foreignId('campus_id')->constrained();
            $table->foreignId('major_id')->constrained();
            $table->foreignId('group_id')->constrained();
            $table->foreignId('shift_id')->constrained();
            $table->foreignId('status_id')->constrained();
            $table->date('effective_date')->nullable();
            $table->boolean('is_current')->default(true);
        }, false);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('student_snapshots');
    }
};
