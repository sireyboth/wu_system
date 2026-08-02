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
            $table->foreignId('person_id')->constrained('people')->cascadeOnDelete();
            $table->unsignedTinyInteger('year_level')->nullable();
            $table->string('code', 50)->nullable()->unique();
            $table->string('from_school', 100)->nullable();
            $table->date('admission_date')->nullable();
            $table->string('degree_type', 50)->nullable();
            $table->string('bacc_2_code', 50)->nullable()->unique();
            $table->string('intake', 50)->nullable();
            $table->string('scholarship', 50)->nullable();
            $table->string('entrance_exam', 50)->nullable();
            $table->string('exit_exam', 50)->nullable();
        }, false);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('students');
    }
};
