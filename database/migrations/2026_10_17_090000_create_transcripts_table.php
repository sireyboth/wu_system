<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Official transcript — standalone on purpose (typed in by the registrar,
 * not pulled from student records). total_credits / total_gpa are
 * computed server-side from transcript_subjects on every save, never
 * accepted from input. remark doubles as the "Note" field.
 */
return new class extends Migration
{
    public function up(): void
    {
        make_fields('transcripts', function (Blueprint $table) {
            // Not a DB unique — uniqueness is enforced in TranscriptRequest
            // ignoring trashed rows, so a deleted transcript's N° can be reused.
            $table->string('issue_no', 100)->index();
            $table->string('name', 100);
            $table->string('surname', 100);
            $table->date('dob')->nullable();
            $table->string('pob', 150)->nullable();
            $table->string('address', 255)->nullable();
            $table->string('student_code', 50)->nullable();
            $table->string('sex', 10)->nullable();
            $table->string('nationality', 100)->nullable();
            $table->string('degree', 20)->default('bachelor');
            $table->foreignId('major_id')->nullable()->constrained()->nullOnDelete();
            $table->string('academic_year', 50)->nullable();
            $table->string('date_kh', 255)->nullable();
            $table->date('issue_date')->nullable();
            $table->string('person_in_charge', 100)->nullable();
            $table->string('person_role', 100)->nullable();
            $table->boolean('entrance_exam')->default(false);
            $table->text('entrance_exam_description')->nullable();
            $table->unsignedSmallInteger('total_credits')->default(0);
            $table->decimal('total_gpa', 4, 2)->default(0);
        }, false);
    }

    public function down(): void
    {
        Schema::dropIfExists('transcripts');
    }
};
