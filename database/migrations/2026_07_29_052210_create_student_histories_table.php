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
        make_fields('student_histories', function (Blueprint $table) {
            $table->foreignId('student_id')->constrained()->cascadeOnDelete();
            $table->foreignId('term_id')->nullable()->constrained();
            $table->unsignedTinyInteger('from_level')->nullable();
            $table->unsignedTinyInteger('to_level');
            $table->foreignId('status_id')->nullable()->constrained('statuses'); // promoted / retained / dropped
            $table->foreignId('created_by')->nullable()->constrained('users');
        }, false);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('student_histories');
    }
};
