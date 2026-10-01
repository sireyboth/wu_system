<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * One row per history entry on an invigilator's record — the repeatable
 * Description / Date / Rating (1–5 stars) / Remark block on the form.
 */
return new class extends Migration
{
    public function up(): void
    {
        make_fields('invigilator_histories', function (Blueprint $table) {
            $table->foreignId('invigilator_id')->constrained()->cascadeOnDelete();
            $table->text('description');
            $table->date('date')->nullable();
            $table->unsignedTinyInteger('rating')->nullable();
        }, false);
    }

    public function down(): void
    {
        Schema::dropIfExists('invigilator_histories');
    }
};
