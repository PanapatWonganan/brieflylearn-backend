<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('enrollments', function (Blueprint $table) {
            // Unique order reference passed to Pay Solutions as `refno` (12 digits max).
            $table->string('order_no', 32)->nullable()->unique()->after('transaction_id');

            // 'paysolutions' | 'manual' | 'promptpay' | ...
            $table->string('payment_gateway', 32)->nullable()->after('order_no');

            // Raw payload from postback / inquiry API for audit.
            $table->json('gateway_response')->nullable()->after('payment_gateway');
        });
    }

    public function down(): void
    {
        Schema::table('enrollments', function (Blueprint $table) {
            $table->dropColumn(['order_no', 'payment_gateway', 'gateway_response']);
        });
    }
};
