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
        make_fields('certificates', function (Blueprint $table) {
            $table->foreignId('student_id')->constrained()->cascadeOnDelete();
            $table->date('issue_date');
            $table->string('full_date_kh')->nullable();
            $table->string('short_date_kh')->nullable();
            $table->string('certificate_no', 20)->nullable()->unique();
            $table->string('status', 50)->nullable()->default('pending');
            $table->string('type', 50)->nullable()->default('status');

            $table->index(['issue_date', 'certificate_no']);
        }, false);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('certificates');
    }
};
