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
            $table->foreignId('person_id')
                ->constrained('people')
                ->cascadeOnDelete();

            $table->string('relationship', 100)->nullable();
        }, named: false);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('guardians');
    }
};
