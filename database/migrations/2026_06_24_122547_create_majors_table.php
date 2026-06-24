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
        make_fields('majors', function (Blueprint $table) {
            $table->foreignId('faculty_id')->constrained()->cascadeOnDelete();
            $table->string('shortcut')->nullable();
        }, ['name_kh', 'name_en']);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('majors');
    }
};
