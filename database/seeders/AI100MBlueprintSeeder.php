<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Course;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

/**
 * Seeds the "AI ฿100M Blueprint" course used by the /ai-100m sales page.
 *
 * Idempotent: uses updateOrCreate on the unique title.
 * The course's UUID is echoed to stdout so you can copy it into the
 * frontend AI_100M_COURSE_ID env var if needed.
 */
class AI100MBlueprintSeeder extends Seeder
{
    public function run(): void
    {
        $title = 'AI ฿100M Blueprint';

        // Use first admin as instructor — fall back to any user.
        $instructor = User::where('role', 'admin')->first() ?? User::first();
        if (!$instructor) {
            $this->command?->warn('No user found. Run DatabaseSeeder first to create admin@example.com.');
            return;
        }

        // Try to use an existing category; create "AI Business" if missing.
        $category = Category::where('slug', 'ai-business')->first();
        if (!$category) {
            $category = Category::firstOrCreate(
                ['slug' => 'ai-business'],
                [
                    'id' => (string) Str::uuid(),
                    'name' => 'AI Business',
                    'description' => 'คอร์สเรียน AI สำหรับผู้ประกอบการและนักธุรกิจ',
                ]
            );
        }

        $course = Course::updateOrCreate(
            ['title' => $title],
            [
                'description' => 'Playbook เดียวที่ผมจะใช้สร้างบริษัท 100 ล้านจาก 0 — เรียนรู้วิธีผสม AI เข้ากับ framework ด้าน product-market ที่เฉพาะเจาะจง พร้อม Wedge Method, SuperB Prompts, Valuation Sprint 90 วัน, AI Agent Starter Kit และอื่นๆ',
                'instructor_id' => $instructor->id,
                'category_id' => $category->id,
                'level' => 'advanced',
                'duration_weeks' => 12,
                'price' => 19900.00,
                'original_price' => 49900.00,
                'thumbnail_url' => null,
                'trailer_video_url' => null,
                'is_published' => true,
                'rating' => 0,
                'total_students' => 0,
                'total_lessons' => 0,
            ]
        );

        $this->command?->info('AI ฿100M Blueprint course seeded.');
        $this->command?->line("Course ID: {$course->id}");
        $this->command?->line("Price: ฿" . number_format((float) $course->price, 2));
    }
}
