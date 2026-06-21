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
        Schema::create('room_m_g_t_s', function (Blueprint $table) {
        $table->id();
        $table->string('room_type')->nullable();
        $table->string('room_number')->unique()->nullable();
        $table->decimal('default_unit_price', 10, 2)->nullable();
        $table->string('status')->nullable();
        $table->text('note')->nullable();
        $table->timestamps();
        $table->softDeletes(); 
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('room_m_g_t_s');
    }
};
