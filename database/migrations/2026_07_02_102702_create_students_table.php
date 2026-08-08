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
            $table->unsignedTinyInteger('year_level')->default(1);
            $table->string('degree_type', 50)->default('associate');
            $table->string('bacc_2_code', 50)->nullable()->unique();
            $table->string('intake', 50)->default('primary');
            $table->string('payment_as', 50)->default('none');
            $table->string('scholarship', 50)->default('none');
            $table->string('entrance_exam', 50)->default('none');
            $table->string('exit_exam', 50)->default('none');
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
