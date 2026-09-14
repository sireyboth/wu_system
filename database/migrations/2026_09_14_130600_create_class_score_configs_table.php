<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * One row per class (i.e. per lecturer's own section) — the point
 * allocation that lecturer chose. class_id is UNIQUE, which is the whole
 * isolation mechanism: Lecturer A's config on class_id=1 can never
 * collide with Lecturer B's on class_id=2, even for the same subject.
 * set_by is an audit trail only, not part of the scoping. The
 * <=100-total-points cap is enforced in ClassScoreConfigRequest, not a DB
 * CHECK constraint (can't express a 6-column sum cleanly across drivers).
 * See attendance schema doc §4 "Scoring".
 */
return new class extends Migration
{
    public function up(): void
    {
        make_fields('class_score_configs', function (Blueprint $table) {
            $table->foreignId('class_id')->unique()->constrained()->cascadeOnDelete();
            $table->unsignedSmallInteger('homework_max')->default(0);
            $table->unsignedSmallInteger('quiz_max')->default(0);
            $table->unsignedSmallInteger('assignment_max')->default(0);
            $table->unsignedSmallInteger('midterm_max')->default(0);
            $table->unsignedSmallInteger('final_max')->default(0);
            $table->unsignedSmallInteger('attendance_max')->default(0);
            $table->foreignId('set_by')->nullable()->constrained('users')->nullOnDelete();
        }, false);
    }

    public function down(): void
    {
        Schema::dropIfExists('class_score_configs');
    }
};
