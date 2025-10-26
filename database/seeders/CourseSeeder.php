<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class CourseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $courses = [
            // Fitness Category
            [
                'title' => 'โยคะสำหรับคุณแม่ตั้งครรภ์',
                'description' => 'คอร์สโยคะเบื้องต้นที่ออกแบบมาเป็นพิเศษสำหรับคุณแม่ตั้งครรภ์ เพื่อเสริมสร้างความแข็งแรงและความยืดหยุ่นของร่างกาย',
                'price' => 1990,
                'category_id' => 1, // Fitness
                'instructor_name' => 'อ.สุภาพร วรรณชัย',
                'thumbnail_url' => 'https://images.unsplash.com/photo-1544367567-0f2fcb009e0b?w=800',
                'level' => 'beginner',
                'duration_minutes' => 180,
                'is_published' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'title' => 'Pilates สำหรับหลังคลอด',
                'description' => 'คอร์ส Pilates ที่ช่วยฟื้นฟูร่างกายหลังคลอด เน้นการกระชับกล้ามเนื้อและฟื้นฟูแกนกลาง',
                'price' => 2490,
                'category_id' => 1, // Fitness
                'instructor_name' => 'อ.ปาณิสรา จันทร์เพ็ญ',
                'thumbnail_url' => 'https://images.unsplash.com/photo-1518611012118-696072aa579a?w=800',
                'level' => 'intermediate',
                'duration_minutes' => 240,
                'is_published' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'title' => 'Cardio สนุกสำหรับผู้หญิง',
                'description' => 'ออกกำลังกาย Cardio สไตล์สนุกๆ เต้นตามจังหวะ เพื่อสุขภาพหัวใจและการเผาผลาญที่ดี',
                'price' => 1590,
                'category_id' => 1, // Fitness
                'instructor_name' => 'อ.น้ำทิพย์ สุขสันต์',
                'thumbnail_url' => 'https://images.unsplash.com/photo-1571019614242-c5c5dee9f50b?w=800',
                'level' => 'beginner',
                'duration_minutes' => 150,
                'is_published' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],

            // Nutrition Category
            [
                'title' => 'โภชนาการสำหรับคุณแม่ตั้งครรภ์',
                'description' => 'เรียนรู้การวางแผนอาหารที่เหมาะสมสำหรับคุณแม่ตั้งครรภ์ เพื่อสุขภาพของแม่และลูก',
                'price' => 1790,
                'category_id' => 2, // Nutrition
                'instructor_name' => 'นักโภชนาการ ธันยวีร์ ปัญญาดี',
                'thumbnail_url' => 'https://images.unsplash.com/photo-1490645935967-10de6ba17061?w=800',
                'level' => 'beginner',
                'duration_minutes' => 200,
                'is_published' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'title' => 'อาหารเสริมนมแม่หลังคลอด',
                'description' => 'สูตรอาหารและเมนูเด็ดสำหรับคุณแม่หลังคลอด ที่ช่วยเพิ่มคุณภาพและปริมาณน้ำนม',
                'price' => 1590,
                'category_id' => 2, // Nutrition
                'instructor_name' => 'นักโภชนาการ วรรณา ศรีสุข',
                'thumbnail_url' => 'https://images.unsplash.com/photo-1504674900247-0877df9cc836?w=800',
                'level' => 'beginner',
                'duration_minutes' => 180,
                'is_published' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'title' => 'การกินเพื่อสมดุลฮอร์โมน',
                'description' => 'เรียนรู้เกี่ยวกับอาหารที่ช่วยสมดุลฮอร์โมนในผู้หญิง ลดอาการ PMS และเมโนพอส',
                'price' => 2190,
                'category_id' => 2, // Nutrition
                'instructor_name' => 'นักโภชนาการ สุดารัตน์ พัฒนสิน',
                'thumbnail_url' => 'https://images.unsplash.com/photo-1512621776951-a57141f2eefd?w=800',
                'level' => 'intermediate',
                'duration_minutes' => 250,
                'is_published' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],

            // Mental Health Category
            [
                'title' => 'สมาธิและการผ่อนคลายสำหรับคุณแม่ใหม่',
                'description' => 'เทคนิคการทำสมาธิและผ่อนคลายเพื่อลดความเครียดหลังคลอด',
                'price' => 1290,
                'category_id' => 3, // Mental Health
                'instructor_name' => 'อ.ภัทรา มณีรัตน์',
                'thumbnail_url' => 'https://images.unsplash.com/photo-1506126613408-eca07ce68773?w=800',
                'level' => 'beginner',
                'duration_minutes' => 120,
                'is_published' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'title' => 'จัดการอารมณ์และความเครียด',
                'description' => 'เรียนรู้เทคนิคการจัดการอารมณ์และความเครียดสำหรับผู้หญิงยุคใหม่',
                'price' => 1690,
                'category_id' => 3, // Mental Health
                'instructor_name' => 'นักจิตวิทยา ดร.ปรียาภรณ์ สุวรรณ',
                'thumbnail_url' => 'https://images.unsplash.com/photo-1499209974431-9dddcece7f88?w=800',
                'level' => 'intermediate',
                'duration_minutes' => 200,
                'is_published' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],

            // Personal Development Category
            [
                'title' => 'การจัดการเวลาสำหรับคุณแม่วัยทำงาน',
                'description' => 'เทคนิคการจัดการเวลาอย่างมีประสิทธิภาพเพื่อสมดุลระหว่างงานและครอบครัว',
                'price' => 1990,
                'category_id' => 4, // Personal Development
                'instructor_name' => 'โค้ช วิภาวี ธีระกุล',
                'thumbnail_url' => 'https://images.unsplash.com/photo-1484480974693-6ca0a78fb36b?w=800',
                'level' => 'beginner',
                'duration_minutes' => 180,
                'is_published' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'title' => 'สร้างความมั่นใจและภาวะผู้นำ',
                'description' => 'พัฒนาความมั่นใจในตัวเองและทักษะภาวะผู้นำสำหรับผู้หญิง',
                'price' => 2490,
                'category_id' => 4, // Personal Development
                'instructor_name' => 'โค้ช ชญานิศ กิจเจริญ',
                'thumbnail_url' => 'https://images.unsplash.com/photo-1573497019940-1c28c88b4f3e?w=800',
                'level' => 'intermediate',
                'duration_minutes' => 240,
                'is_published' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],

            // Pregnancy Category
            [
                'title' => 'เตรียมตัวตั้งครรภ์อย่างมีความสุข',
                'description' => 'คอร์สเตรียมความพร้อมร่างกายและจิตใจสำหรับการตั้งครรภ์',
                'price' => 2990,
                'category_id' => 5, // Pregnancy
                'instructor_name' => 'พญ.สุภาพร ประดิษฐ์',
                'thumbnail_url' => 'https://images.unsplash.com/photo-1555252333-9f8e92e65df9?w=800',
                'level' => 'beginner',
                'duration_minutes' => 300,
                'is_published' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'title' => 'การดูแลตัวเองหลังคลอดแบบองค์รวม',
                'description' => 'คู่มือการดูแลตัวเองหลังคลอดทั้งร่างกายและจิตใจ ตั้งแต่สัปดาห์แรกถึง 6 เดือน',
                'price' => 2790,
                'category_id' => 5, // Pregnancy
                'instructor_name' => 'อ.ธิดารัตน์ วงศ์ชัย',
                'thumbnail_url' => 'https://images.unsplash.com/photo-1555252333-9f8e92e65df9?w=800',
                'level' => 'beginner',
                'duration_minutes' => 280,
                'is_published' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],

            // Hormonal Health Category
            [
                'title' => 'ทำความรู้จักกับฮอร์โมนผู้หญิง',
                'description' => 'เรียนรู้เกี่ยวกับฮอร์โมนผู้หญิง การทำงาน และวิธีดูแลให้สมดุล',
                'price' => 1890,
                'category_id' => 6, // Hormonal Health
                'instructor_name' => 'พญ.ณัฐชา อิ่มสุข',
                'thumbnail_url' => 'https://images.unsplash.com/photo-1559757148-5c350d0d3c56?w=800',
                'level' => 'beginner',
                'duration_minutes' => 220,
                'is_published' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'title' => 'จัดการ PCOS และความไม่สมดุลของฮอร์โมน',
                'description' => 'แนวทางการดูแลและจัดการ PCOS ด้วยวิธีธรรมชาติและการปรับไลฟ์สไตล์',
                'price' => 2290,
                'category_id' => 6, // Hormonal Health
                'instructor_name' => 'พญ.รัตนา บุญมี',
                'thumbnail_url' => 'https://images.unsplash.com/photo-1576091160550-2173dba999ef?w=800',
                'level' => 'intermediate',
                'duration_minutes' => 260,
                'is_published' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ];

        foreach ($courses as $course) {
            \App\Models\Course::create($course);
        }
    }
}
