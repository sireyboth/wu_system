<?php

use function App\Helpers\make_fields;
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
        make_fields('subjects', function (Blueprint $table) {
            $table->string('code')->unique()->nullable();
            $table->enum('year', ['year 1', 'year 2', 'year 3', 'year 4'])->default('year 1');
            $table->enum('semester', ['semester 1', 'semester 2'])->default('semester 1');
            $table->string('credit')->nullable();
        }, ['name_kh', 'name_en']);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('subjects');
    }
};
