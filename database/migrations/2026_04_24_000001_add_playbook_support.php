<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Adds playbook support to the LMS.
 *
 * Courses can now have content_type = 'video' (default, existing behaviour)
 * or 'playbook' (HTML long-form reading, single lesson).
 * Lessons get an optional html_file_path (uploaded .html in storage) and a
 * cached html_content column so the API can serve content without re-reading
 * the file on every request.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('courses', function (Blueprint $table) {
            $table->enum('content_type', ['video', 'playbook'])
                ->default('video')
                ->after('category_id');
        });

        Schema::table('lessons', function (Blueprint $table) {
            $table->string('html_file_path', 500)->nullable()->after('video_url');
            $table->longText('html_content')->nullable()->after('html_file_path');
        });
    }

    public function down(): void
    {
        Schema::table('lessons', function (Blueprint $table) {
            $table->dropColumn(['html_file_path', 'html_content']);
        });

        Schema::table('courses', function (Blueprint $table) {
            $table->dropColumn('content_type');
        });
    }
};
