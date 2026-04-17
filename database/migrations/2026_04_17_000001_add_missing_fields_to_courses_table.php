<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Aligns `courses` table with Filament CourseResource form + Course model $fillable.
 *
 * The original migration (2025_08_15_000002_create_all_tables.php) only created
 * minimal columns. The Filament admin form and frontend need these additional
 * fields to work properly (currently causes 500 errors on /admin/courses/create).
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('courses', function (Blueprint $table) {
            if (!Schema::hasColumn('courses', 'duration_weeks')) {
                $table->integer('duration_weeks')->default(0)->after('level');
            }
            if (!Schema::hasColumn('courses', 'original_price')) {
                $table->decimal('original_price', 10, 2)->nullable()->after('price');
            }
            if (!Schema::hasColumn('courses', 'trailer_video_url')) {
                $table->string('trailer_video_url', 500)->nullable()->after('thumbnail_url');
            }
            if (!Schema::hasColumn('courses', 'rating')) {
                $table->decimal('rating', 3, 1)->default(0)->after('is_published');
            }
            if (!Schema::hasColumn('courses', 'total_students')) {
                $table->integer('total_students')->default(0)->after('rating');
            }
            if (!Schema::hasColumn('courses', 'total_lessons')) {
                $table->integer('total_lessons')->default(0)->after('total_students');
            }
        });
    }

    public function down(): void
    {
        Schema::table('courses', function (Blueprint $table) {
            $columns = [
                'duration_weeks',
                'original_price',
                'trailer_video_url',
                'rating',
                'total_students',
                'total_lessons',
            ];

            foreach ($columns as $column) {
                if (Schema::hasColumn('courses', $column)) {
                    $table->dropColumn($column);
                }
            }
        });
    }
};
