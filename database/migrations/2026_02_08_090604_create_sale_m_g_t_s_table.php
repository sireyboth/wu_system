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
        Schema::create('sale_m_g_t_s', function (Blueprint $table) {
            
            $table->id();

            //datetimenow + id(PK)
            $table->string('invoice_no')->unique()->nullable();
            
            // Customer info
            $table->string('cus_first_name')->nullable();
            $table->string('cus_last_name')->nullable();
            $table->string('cus_contact')->nullable();
            
            // Dates & Pricing
            $table->date('check_in_date')->nullable();
            $table->date('check_out_date')->nullable();
            
            // Calculation
            $table->decimal('balance_subtotal', 12, 2)->default(0); // sum-total from table sale_items
            // balance_subtotal - booking_price = balance_remaining - balance_completion = balance_grand_total
            $table->decimal('booking_price', 12, 2)->default(0)->nullable(); // Deposit
            $table->decimal('balance_remaining', 12, 2)->default(0)->nullable(); // user will input when customer pay final price before print
            $table->decimal('balance_completion', 12, 2)->default(0)->nullable(); // Customer can pay not fully and we will add later (user entry)
            $table->decimal('balance_grand_total', 12, 2)->default(0)->nullable(); 

            $table->string('status')->default('pending')->nullable(); // pending, completed, cancelled
            $table->text('note')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    
            // Table 2: Child (The Items/Rooms)
        Schema::create('sale_items', function (Blueprint $table) {
        $table->id();
        // THE LINK: This connects every room to one specific Sale
        $table->foreignId('sale_mgt_id')->constrained('sale_m_g_t_s')->onDelete('cascade');
        $table->foreignId('room_mgt_id')->constrained('room_m_g_t_s');

        $table->string('room_number_snapshot')->nullable(); 
        $table->string('room_type_snapshot')->nullable(); 
        $table->decimal('room_unit_price_snapshot')->nullable(); 
        $table->decimal('qty')->nullable(); 
        $table->decimal('food_price')->nullable(); 
        $table->decimal('discount_percent')->nullable(); 

        // Total_Price =( (Qty * room_unit_price_snapshot) * (1 - Discount_Percent/100)) + Food_Price
        $table->decimal('total_price', 12, 2)->nullable(); 
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
    // 1. Drop the Child FIRST to release the link
    Schema::dropIfExists('sale_items');

    // 2. Drop the Parent SECOND
    Schema::dropIfExists('sale_m_g_t_s');
}
};
