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
        make_fields('addresses', function (Blueprint $table) {
            // Owner
            $table->foreignId('person_id')
                ->constrained('people')
                ->cascadeOnDelete();

            // Administrative divisions
            $table->foreignId('province_id')
                ->constrained()
                ->restrictOnDelete();

            $table->foreignId('district_id')
                ->constrained()
                ->restrictOnDelete();

            $table->foreignId('commune_id')
                ->constrained()
                ->restrictOnDelete();

            $table->foreignId('village_id')
                ->constrained()
                ->restrictOnDelete();

            $table->string('street')->nullable();
            $table->string('house_no')->nullable();

            // Address type
            $table->string('type')->default('current');

            // Prevent duplicate address types for the same person
            $table->unique(['person_id', 'type']);
        }, false);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('addresses');
    }
};
