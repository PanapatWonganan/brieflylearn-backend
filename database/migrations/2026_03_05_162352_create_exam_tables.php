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
        // Exams table
        Schema::create('exams', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('course_id')->nullable();
            $table->string('title');
            $table->text('description')->nullable();
            $table->integer('duration_minutes')->default(60);
            $table->integer('passing_score')->default(70);
            $table->integer('total_questions')->default(0);
            $table->string('difficulty')->default('intermediate'); // beginner, intermediate, advanced
            $table->string('category')->nullable();
            $table->decimal('price', 10, 2)->nullable();
            $table->boolean('is_published')->default(true);
            $table->timestamps();
            $table->foreign('course_id')->references('id')->on('courses')->nullOnDelete();
        });

        // Exam questions table
        Schema::create('exam_questions', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('exam_id');
            $table->text('question_text');
            $table->string('question_type')->default('multiple_choice');
            $table->json('options'); // [{label: "A", text: "..."}, ...]
            $table->string('correct_answer');
            $table->integer('points')->default(1);
            $table->integer('order_index')->default(0);
            $table->timestamps();
            $table->foreign('exam_id')->references('id')->on('exams')->cascadeOnDelete();
        });

        // Exam results table
        Schema::create('exam_results', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('user_id');
            $table->uuid('exam_id');
            $table->integer('score');
            $table->integer('total_points');
            $table->boolean('passed')->default(false);
            $table->json('answers')->nullable();
            $table->timestamp('started_at')->nullable();
            $table->timestamp('completed_at')->nullable();
            $table->timestamps();
            $table->foreign('user_id')->references('id')->on('users')->cascadeOnDelete();
            $table->foreign('exam_id')->references('id')->on('exams')->cascadeOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('exam_results');
        Schema::dropIfExists('exam_questions');
        Schema::dropIfExists('exams');
    }
};
