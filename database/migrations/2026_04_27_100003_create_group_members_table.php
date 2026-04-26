<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Group members — pivot between users and groups.
 *
 * Created automatically by OrderItemFulfillmentService when a 'group_membership'
 * bump is paid for. `enrollment_id` ties the membership back to the order that
 * granted it (so refunds can revoke membership cleanly).
 */
return new class extends Migration {
    public function up(): void
    {
        Schema::create('group_members', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('group_id')->constrained('groups')->cascadeOnDelete();
            $table->foreignUuid('user_id')->constrained('users')->cascadeOnDelete();

            $table->enum('role', ['member', 'admin'])->default('member');
            $table->enum('status', ['active', 'pending', 'removed'])->default('active');

            $table->timestamp('joined_at')->useCurrent();

            // Optional link back to the enrollment that granted this membership
            // (so refund can find this row by enrollment_id).
            $table->foreignUuid('enrollment_id')->nullable()->constrained('enrollments')->nullOnDelete();

            $table->timestamps();

            $table->unique(['group_id', 'user_id']);
            $table->index(['user_id', 'status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('group_members');
    }
};
