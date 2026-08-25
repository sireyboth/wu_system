<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('alert_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('alert_id')->constrained()->cascadeOnDelete();

            // before_start|before_end|reminder|created|completed|snoozed
            $table->string('type', 20);
            $table->text('message')->nullable();
            $table->dateTime('sent_at');
            $table->boolean('success')->default(true);

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('alert_logs');
    }
};
