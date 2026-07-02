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
        make_fields('students', function (Blueprint $table) {
            $table->foreignId('person_id')
                ->constrained('people')
                ->cascadeOnDelete();

            $table->foreignId('bactch_id')
                ->constrained()
                ->cascadeOnDelete();

            $table->string('code', 50)->nullable()->unique();
            $table->date('admission_at')->nullable();
            $table->string('bacc_2_code', 50)->nullable()->unique();
            $table->string('status', 50)->nullable();
            $table->string('entrance_exam', 50)->nullable();
            $table->string('exit_exam', 50)->nullable();
        }, named: false);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('students');
    }
};
