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
        Schema::table('users', function (Blueprint $table) {
            $table->timestamp('last_active_at')->nullable()->after('token_expires_at');
            $table->tinyInteger('onboarding_step')->default(0)->after('last_active_at');
            // 0 = no onboarding emails sent
            // 1 = welcome sent (already happens on register)
            // 2 = day 1 onboarding sent
            // 3 = first course nudge sent
            // 4 = garden intro sent (onboarding complete)
            $table->integer('current_streak')->default(0)->after('onboarding_step');
            $table->date('last_streak_date')->nullable()->after('current_streak');
            $table->timestamp('weekly_email_sent_at')->nullable()->after('last_streak_date');
            $table->timestamp('inactive_email_sent_at')->nullable()->after('weekly_email_sent_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn([
                'last_active_at',
                'onboarding_step',
                'current_streak',
                'last_streak_date',
                'weekly_email_sent_at',
                'inactive_email_sent_at'
            ]);
        });
    }
};
