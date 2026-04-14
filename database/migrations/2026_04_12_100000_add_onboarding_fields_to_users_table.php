<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->json('goals')->nullable()->after('onboarding_step');
            $table->json('interests')->nullable()->after('goals');
            $table->string('experience_level')->nullable()->after('interests');
            $table->boolean('onboarding_completed')->default(false)->after('experience_level');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['goals', 'interests', 'experience_level', 'onboarding_completed']);
        });
    }
};
