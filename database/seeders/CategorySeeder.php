<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class CategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $categories = [
            [
                'name' => 'AI พื้นฐาน',
                'slug' => 'ai-fundamentals',
                'description' => 'คอร์สพื้นฐาน AI สำหรับผู้เริ่มต้น เรียนรู้แนวคิด หลักการ และการใช้งาน AI ในชีวิตจริง',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'AI สร้างธุรกิจ',
                'slug' => 'ai-business',
                'description' => 'คอร์ส AI สำหรับผู้ประกอบการ เรียนรู้การใช้ AI สร้างรายได้ ลดต้นทุน และขยายธุรกิจ',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'AI บริหารองค์กร',
                'slug' => 'ai-organization',
                'description' => 'คอร์ส AI สำหรับผู้บริหาร เรียนรู้การนำ AI เข้าสู่องค์กรอย่างเป็นระบบ',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Prompt Engineering',
                'slug' => 'prompt-engineering',
                'description' => 'คอร์สเทคนิคการเขียน Prompt ให้ได้ผลลัพธ์ตรงใจ ทั้ง ChatGPT, Claude และ AI อื่นๆ',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'AI Automation',
                'slug' => 'ai-automation',
                'description' => 'คอร์สสร้างระบบอัตโนมัติด้วย AI ลดงานซ้ำซ้อน เพิ่มประสิทธิภาพการทำงาน',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'AI Strategy',
                'slug' => 'ai-strategy',
                'description' => 'คอร์สวางแผนกลยุทธ์ AI สำหรับธุรกิจและองค์กร ตั้งแต่วิสัยทัศน์จนถึงการลงมือทำ',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ];

        foreach ($categories as $category) {
            \App\Models\Category::create($category);
        }
    }
}
