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
        Schema::create('taxmgts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('sale_mgt_id')->constrained('sale_m_g_t_s')->onDelete('cascade');
            $table->string('tax_invoice_number')->unique()->nullable();
            $table->string('tax_cus_vattin')->nullable();
            $table->string('tax_cus_address')->nullable();
            $table->decimal('tax_vat_price', 12, 2)->default(0);
            $table->decimal('tax_at_price', 12, 2)->default(0);
            $table->decimal('tax_hidden_price', 12, 2)->nullable();
            $table->decimal('tax_sub_total', 12, 2)->default(0);
            $table->decimal('tax_balance_remaining', 12, 2)->default(0);
            $table->decimal('tax_balance_complete', 12, 2)->default(0);
            $table->decimal('tax_balance_final', 12, 2)->default(0);
            $table->string('status')->default('pending')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('taxmgts');
    }
};
