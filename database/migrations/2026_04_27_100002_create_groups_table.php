<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Groups (coaching groups / community / cohorts).
 *
 * Phase 2 of the order-bump system: a "group_membership" bump (e.g. DWY upgrade)
 * grants the buyer a row in `group_members` referencing one of these groups.
 *
 * Admin updates `zoom_link` / `meeting_schedule` from Filament; members see
 * the group via /groups page on the frontend.
 */
return new class extends Migration {
    public function up(): void
    {
        Schema::create('groups', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('slug', 80)->unique();
            $table->string('name');
            $table->text('description')->nullable();

            // 'coaching' = paid group with cohort schedule (DWY)
            // 'community' = free open community
            // 'cohort'    = time-boxed batch
            $table->enum('type', ['coaching', 'community', 'cohort'])->default('coaching');

            $table->string('zoom_link')->nullable();
            $table->text('meeting_schedule')->nullable(); // human-readable, e.g. "ทุกวันอังคาร 20:00-21:30"
            $table->json('resources')->nullable();         // array of {label, url} for replays/docs

            $table->unsignedInteger('max_members')->nullable(); // null = unlimited; 12 for DWY
            $table->boolean('is_active')->default(true);

            $table->timestamps();

            $table->index(['type', 'is_active']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('groups');
    }
};
