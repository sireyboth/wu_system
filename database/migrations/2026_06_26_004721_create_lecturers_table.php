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
        make_fields('lecturers', function (Blueprint $table) {
            $table->foreignId('person_id')
                ->constrained('people')
                ->cascadeOnDelete()
                ->unique();
            $table->foreignId('major_id')->constrained()->cascadeOnDelete();

            $table->date('hired_at')->nullable();
            $table->string('code', 50)->unique()->nullable();
        }, named: false);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('lecturers');
    }
};
