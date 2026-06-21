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
    Schema::create('rooms', function (Blueprint $table) {
        $table->id();

        // e.g. "Single", "Double", "Suite"
        $table->string('room_type');

        // Keep as string because some hotels use "A101", "B-12", etc.
        $table->string('room_number')->unique();

        // Money: use decimal for accuracy (never float)
        $table->decimal('default_unit_price', 10, 2)->default(0);

        // Controlled values for UI and filtering
        $table->enum('status', ['available', 'occupied', 'maintenance', 'inactive'])
              ->default('available');

        // Optional notes
        $table->text('note')->nullable();

        $table->timestamps();      // created_at, updated_at
        $table->softDeletes();     // deleted_at
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('rooms');
    }
};
