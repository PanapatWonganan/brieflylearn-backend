<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('order_items', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('enrollment_id');
            $table->uuid('bump_product_id');

            // Snapshots — never read from BumpProduct after an order is placed.
            // Protects buyer if admin later edits the bump (price, name, etc.).
            $table->string('name_snapshot');
            $table->decimal('price_snapshot', 10, 2);

            // Fulfillment audit trail
            $table->timestamp('delivered_at')->nullable();
            $table->json('delivery_meta')->nullable(); // e.g. { playbook_enrollment_id, group_membership_id, error }

            $table->timestamps();

            $table->foreign('enrollment_id')->references('id')->on('enrollments')->cascadeOnDelete();
            $table->foreign('bump_product_id')->references('id')->on('bump_products')->restrictOnDelete();

            $table->unique(['enrollment_id', 'bump_product_id']);
            $table->index('enrollment_id');
            $table->index('bump_product_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('order_items');
    }
};
