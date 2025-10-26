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
                'name' => 'ฟิตเนสและการออกกำลังกาย',
                'slug' => 'fitness',
                'description' => '💪 คอร์สเรียนเกี่ยวกับการออกกำลังกาย โยคะ และการเคลื่อนไหวร่างกาย',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'โภชนาการและสุขภาพ',
                'slug' => 'nutrition',
                'description' => '🥗 คอร์สเรียนเกี่ยวกับการกินอาหารเพื่อสุขภาพ โภชนาการ และการปรุงอาหาร',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'สุขภาพจิตและจิตใจ',
                'slug' => 'mental-health',
                'description' => '🧘‍♀️ คอร์สเรียนเกี่ยวกับการดูแลสุขภาพจิต สติ และการผ่อนคลาย',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'การพัฒนาตนเอง',
                'slug' => 'personal-development',
                'description' => '🌟 คอร์สเรียนเกี่ยวกับการพัฒนาทักษะชีวิต ภาวะผู้นำ และการจัดการเวลา',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'ตั้งครรภ์และหลังคลอด',
                'slug' => 'pregnancy',
                'description' => '🤱 คอร์สเรียนสำหรับคุณแม่ตั้งครรภ์และหลังคลอด',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'ฮอร์โมนและสุขภาพผู้หญิง',
                'slug' => 'hormonal-health',
                'description' => '🌸 คอร์สเรียนเกี่ยวกับการดูแลสุขภาพฮอร์โมนและสุขภาพผู้หญิง',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ];

        foreach ($categories as $category) {
            \App\Models\Category::create($category);
        }
    }
}
