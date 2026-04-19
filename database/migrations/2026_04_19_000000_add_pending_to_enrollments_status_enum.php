<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

/**
 * Expand enrollments.status enum to include 'pending' so the Pay Solutions
 * checkout flow can create a pre-payment enrollment row. Existing values
 * ('active','completed','cancelled') remain valid.
 */
return new class extends Migration
{
    public function up(): void
    {
        DB::statement("ALTER TABLE enrollments MODIFY COLUMN status ENUM('pending','active','completed','cancelled') NOT NULL DEFAULT 'active'");
    }

    public function down(): void
    {
        // Collapse any 'pending' rows back to 'cancelled' before shrinking the enum
        DB::statement("UPDATE enrollments SET status = 'cancelled' WHERE status = 'pending'");
        DB::statement("ALTER TABLE enrollments MODIFY COLUMN status ENUM('active','completed','cancelled') NOT NULL DEFAULT 'active'");
    }
};
