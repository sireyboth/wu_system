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
        make_fields('guardians', function (Blueprint $table) {
            // $table->foreignId('person_id')
            //     ->constrained('people')
            //     ->cascadeOnDelete();
            $table->json('phones')->nullable();
            $table->json('addresses')->nullable();

            $table->string('occupation', 100)->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('guardians');
    }
};
