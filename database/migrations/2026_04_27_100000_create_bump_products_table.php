<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('bump_products', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('slug', 80)->unique();
            $table->string('name');
            $table->text('description')->nullable();

            // Pricing — snapshot is captured per-order in order_items.
            $table->decimal('price', 10, 2);
            $table->decimal('original_price', 10, 2)->nullable();

            // Fulfillment strategy when an order succeeds.
            //   playbook_course  → grant Enrollment for deliverable_ref_id (a Course with content_type='playbook')
            //   group_membership → add user to a group with deliverable_ref_id (Phase 2)
            //   manual           → notify admin only, handled offline
            $table->enum('deliverable_type', ['playbook_course', 'group_membership', 'manual'])
                ->default('manual');

            // FK target depends on deliverable_type. Kept loose because target tables differ.
            $table->uuid('deliverable_ref_id')->nullable();

            // Optional binding: bump only offered when checking out this specific course.
            // null = available for any course this bump is attached to via UI.
            $table->uuid('course_id')->nullable();

            $table->integer('sort_order')->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->index(['course_id', 'is_active', 'sort_order']);
            $table->index(['deliverable_type', 'deliverable_ref_id']);

            $table->foreign('course_id')->references('id')->on('courses')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('bump_products');
    }
};
