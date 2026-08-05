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
        make_fields('number_counters', function (Blueprint $table) {
            $table->unsignedSmallInteger('year');
            $table->string('type', 50)->default('status');
            $table->unsignedInteger('last_sequence')->default(0);

            $table->unique(['year', 'type']);
        }, false, false);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('number_counters');
    }
};
