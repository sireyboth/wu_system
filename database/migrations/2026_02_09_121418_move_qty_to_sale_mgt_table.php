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
        Schema::table('sale_m_g_t_s', function (Blueprint $table) {
            $table->decimal('qty')->nullable(); 
        });
        // 2. Remove qty from the items table
    Schema::table('sale_items', function (Blueprint $table) {
        if (Schema::hasColumn('sale_items', 'qty')) {
            $table->dropColumn('qty');
        }
    });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('sale_items', function (Blueprint $table) {
             $table->decimal('qty')->nullable(); 
        });
        Schema::table('sale_m_g_t_s', function (Blueprint $table) {
        $table->dropColumn('qty');
    });
    }
};
