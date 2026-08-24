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
            $table->foreignId('batch_id')->constrained()->cascadeOnDelete();
            $table->foreignId('major_id')->constrained()->cascadeOnDelete();
            $table->foreignId('group_id')->constrained()->cascadeOnDelete();
            $table->foreignId('shift_id')->constrained()->cascadeOnDelete();
            $table->foreignId('status_id')->constrained()->cascadeOnDelete();

            $table->string('code')->nullable()->unique();
            $table->string('from_school')->nullable();
            $table->date('admission_date')->nullable();
            $table->unsignedTinyInteger('year_level')->default(1);
            $table->string('degree_type')->default('associate');
            $table->string('bacc_2_code')->nullable()->unique();
            $table->string('intake')->default('primary');
            $table->string('payment_as')->default('none');
            $table->string('scholarship')->default('none');
            $table->string('entrance_exam')->default('none');
            $table->string('exit_exam')->default('none');
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
