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
        make_fields('people', function (Blueprint $table) {
            $table->string('first_name');
            $table->string('last_name');
            $table->string('first_name_kh');
            $table->string('last_name_kh');
            $table->foreignId('nationality_id')->constrained()->restrictOnDelete();

            $table->date('dob')->nullable();
            $table->string('sex')->default('other');
            $table->string('email')->nullable();
            $table->json('phones')->nullable();
        }, false);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('people');
    }
};
