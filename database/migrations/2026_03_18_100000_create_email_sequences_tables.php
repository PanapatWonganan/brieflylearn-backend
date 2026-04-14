<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('email_sequences', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('name');
            $table->string('slug')->unique();
            $table->text('description')->nullable();
            $table->boolean('is_active')->default(true);
            $table->boolean('is_default')->default(false); // auto-subscribe new users
            $table->timestamps();
        });

        Schema::create('email_sequence_steps', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('sequence_id');
            $table->integer('step_number');
            $table->string('subject');
            $table->longText('body_html');
            $table->integer('delay_days')->default(3); // days after previous step
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->foreign('sequence_id')->references('id')->on('email_sequences')->onDelete('cascade');
            $table->unique(['sequence_id', 'step_number']);
            $table->index(['sequence_id', 'step_number', 'is_active']);
        });

        Schema::create('email_sequence_subscriptions', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('user_id');
            $table->uuid('sequence_id');
            $table->integer('current_step')->default(0); // 0 = not started yet, 1 = step 1 sent
            $table->timestamp('next_send_at')->nullable();
            $table->string('status')->default('active'); // active, paused, completed, unsubscribed
            $table->timestamp('started_at')->nullable();
            $table->timestamp('completed_at')->nullable();
            $table->timestamps();

            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('sequence_id')->references('id')->on('email_sequences')->onDelete('cascade');
            $table->unique(['user_id', 'sequence_id']);
            $table->index(['status', 'next_send_at']);
            $table->index(['user_id', 'status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('email_sequence_subscriptions');
        Schema::dropIfExists('email_sequence_steps');
        Schema::dropIfExists('email_sequences');
    }
};
