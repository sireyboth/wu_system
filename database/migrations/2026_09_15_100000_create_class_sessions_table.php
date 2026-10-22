<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/** One real occurrence of a class — created when a lecturer starts attendance. See attendance schema doc §5. */
return new class extends Migration
{
    public function up(): void
    {
        make_fields('class_sessions', function (Blueprint $table) {
            $table->foreignId('class_id')->constrained()->cascadeOnDelete();
            $table->date('session_date');
            $table->dateTime('started_at')->nullable();
            $table->dateTime('ended_at')->nullable();
            $table->dateTime('locked_at')->nullable();
            $table->string('status', 20)->default('open')->comment('open / submitted / locked');

            $table->unique(['class_id', 'session_date']);
        }, false);
    }

    public function down(): void
    {
        Schema::dropIfExists('class_sessions');
    }
};
