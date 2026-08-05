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
        make_fields('terms', function (Blueprint $table) {
            $table->integer('year')->comment('Starting year of academic year e.g. 2025');
            $table->enum('semester', [1, 2])->comment('1 or 2');
            $table->string('code')->unique()->comment('S1-2025, S2-2025');
            $table->string('name')->comment('Display name: 2025-2026');
            $table->date('start_date');
            $table->date('end_date');
            $table->boolean('is_active')->default(false);

            // Indexes
            $table->index(['year', 'semester']);
            $table->index('is_active');
        }, false);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('terms');
    }
};
