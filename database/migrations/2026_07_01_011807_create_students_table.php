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
            $status_exam = ['none', 'passed', 'failed'];
            $degree      = ['associate', 'bachelor', 'master', 'phd'];

            $table->foreignId('person_id')->constrained('people')->cascadeOnDelete();
            $table->foreignId('batch_id')->constrained()->cascadeOnDelete();
            $table->foreignId('major_id')->constrained()->cascadeOnDelete();
            $table->foreignId('group_id')->constrained()->cascadeOnDelete();
            $table->foreignId('shift_id')->constrained()->cascadeOnDelete();
            $table->foreignId('status_id')->constrained()->cascadeOnDelete();

            $table->string('code', 50)->nullable()->unique();
            $table->string('from_school', 100)->nullable();
            $table->date('admission_date')->nullable();
            $table->enum('degree_type', $degree)->default($degree[1]);
            $table->string('bacc_2_code', 50)->nullable()->unique();
            $table->enum('intake', ['primary', 'secondary'])->default('primary');
            $table->enum('scholarship', ['none', 'ministry', 'prince', 'school'])->default('none');
            $table->enum('entrance_exam', $status_exam)->default($status_exam[0]);
            $table->enum('exit_exam', $status_exam)->default($status_exam[0]);
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
