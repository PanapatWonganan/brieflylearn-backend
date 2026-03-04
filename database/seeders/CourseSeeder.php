<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Category;
use App\Models\User;
use App\Models\Course;

class CourseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get admin user as default instructor
        $instructor = User::where('role', 'admin')->first()
            ?? User::first();

        if (!$instructor) {
            $this->command->error('No user found to assign as instructor. Run DatabaseSeeder first.');
            return;
        }

        // Get categories by slug
        $categories = Category::pluck('id', 'slug');

        if ($categories->isEmpty()) {
            $this->command->error('No categories found. Run CategorySeeder first.');
            return;
        }

        $courses = [
            // AI พื้นฐาน
            [
                'title' => 'AI 101: เริ่มต้นเข้าใจ AI ฉบับไม่ต้องเขียนโค้ด',
                'description' => 'คอร์สพื้นฐานสำหรับทุกคนที่อยากเข้าใจ AI ตั้งแต่แนวคิด หลักการทำงาน ไปจนถึงการใช้งานจริงในชีวิตประจำวันและการทำงาน ไม่ต้องมีพื้นฐานโปรแกรมมิ่ง',
                'price' => 1490,
                'category_slug' => 'ai-fundamentals',
                'thumbnail_url' => 'https://images.unsplash.com/photo-1677442136019-21780ecad995?w=800',
                'level' => 'beginner',
                'duration' => 180,
                'is_published' => true,
            ],
            [
                'title' => 'ChatGPT & Claude Mastery',
                'description' => 'เรียนรู้การใช้งาน ChatGPT และ Claude อย่างเต็มประสิทธิภาพ ตั้งแต่พื้นฐานจนถึงเทคนิคขั้นสูง เพื่อเพิ่มผลผลิตในการทำงานได้ทันที',
                'price' => 1990,
                'category_slug' => 'ai-fundamentals',
                'thumbnail_url' => 'https://images.unsplash.com/photo-1655720828018-edd2daec9349?w=800',
                'level' => 'beginner',
                'duration' => 200,
                'is_published' => true,
            ],
            [
                'title' => 'AI Tools รวมเครื่องมือ AI ที่ต้องรู้ในปี 2025',
                'description' => 'สำรวจเครื่องมือ AI กว่า 30+ ตัวที่น่าสนใจ ทั้ง Text, Image, Video, Audio AI พร้อมวิธีเลือกใช้ให้เหมาะกับงาน',
                'price' => 1290,
                'category_slug' => 'ai-fundamentals',
                'thumbnail_url' => 'https://images.unsplash.com/photo-1485827404703-89b55fcc595e?w=800',
                'level' => 'beginner',
                'duration' => 150,
                'is_published' => true,
            ],

            // AI สร้างธุรกิจ (Entrepreneur Track)
            [
                'title' => 'สร้างธุรกิจออนไลน์ด้วย AI ใน 30 วัน',
                'description' => 'คอร์สเข้มข้นสำหรับผู้ประกอบการ เรียนรู้การใช้ AI สร้างคอนเทนต์ ทำการตลาด สร้างเว็บไซต์ และขายสินค้าออนไลน์ ตั้งแต่เริ่มต้นจนมีรายได้',
                'price' => 3990,
                'category_slug' => 'ai-business',
                'thumbnail_url' => 'https://images.unsplash.com/photo-1553877522-43269d4ea984?w=800',
                'level' => 'beginner',
                'duration' => 360,
                'is_published' => true,
            ],
            [
                'title' => 'AI Content Creator: สร้างคอนเทนต์ระดับโปร',
                'description' => 'เทคนิคการใช้ AI สร้างคอนเทนต์คุณภาพ ทั้งบทความ วิดีโอสคริปต์ โพสต์โซเชียล กราฟิก และ Ads Copy ที่ขายได้จริง',
                'price' => 2490,
                'category_slug' => 'ai-business',
                'thumbnail_url' => 'https://images.unsplash.com/photo-1611532736597-de2d4265fba3?w=800',
                'level' => 'intermediate',
                'duration' => 240,
                'is_published' => true,
            ],
            [
                'title' => 'AI Marketing: การตลาดยุค AI',
                'description' => 'วางแผนกลยุทธ์การตลาดด้วย AI ตั้งแต่วิเคราะห์ลูกค้า สร้าง Persona ทำ SEO ยิง Ads และวัดผลแคมเปญอย่างมีประสิทธิภาพ',
                'price' => 2990,
                'category_slug' => 'ai-business',
                'thumbnail_url' => 'https://images.unsplash.com/photo-1460925895917-afdab827c52f?w=800',
                'level' => 'intermediate',
                'duration' => 280,
                'is_published' => true,
            ],

            // AI บริหารองค์กร (Leader Track)
            [
                'title' => 'AI Transformation: นำ AI เข้าองค์กรอย่างเป็นระบบ',
                'description' => 'คอร์สสำหรับผู้บริหารและ HR เรียนรู้กระบวนการนำ AI เข้าสู่องค์กร ตั้งแต่การประเมินความพร้อม วางแผน จนถึงการ Implement และวัดผล ROI',
                'price' => 4990,
                'category_slug' => 'ai-organization',
                'thumbnail_url' => 'https://images.unsplash.com/photo-1552664730-d307ca884978?w=800',
                'level' => 'advanced',
                'duration' => 400,
                'is_published' => true,
            ],
            [
                'title' => 'AI Leadership: ผู้นำยุค AI',
                'description' => 'พัฒนาทักษะการเป็นผู้นำในยุค AI เรียนรู้วิธีบริหารทีมที่ทำงานร่วมกับ AI การตัดสินใจด้วยข้อมูล และการสร้างวัฒนธรรมองค์กรที่พร้อมรับ AI',
                'price' => 3490,
                'category_slug' => 'ai-organization',
                'thumbnail_url' => 'https://images.unsplash.com/photo-1542744173-8e7e53415bb0?w=800',
                'level' => 'intermediate',
                'duration' => 300,
                'is_published' => true,
            ],

            // Prompt Engineering
            [
                'title' => 'Prompt Engineering Masterclass',
                'description' => 'เจาะลึกเทคนิคการเขียน Prompt ขั้นสูง ทั้ง Chain-of-Thought, Few-Shot, Role Prompting และ Framework ต่างๆ เพื่อผลลัพธ์ที่แม่นยำจาก AI',
                'price' => 2490,
                'category_slug' => 'prompt-engineering',
                'thumbnail_url' => 'https://images.unsplash.com/photo-1516321318423-f06f85e504b3?w=800',
                'level' => 'intermediate',
                'duration' => 220,
                'is_published' => true,
            ],
            [
                'title' => 'Prompt Templates สำหรับทุกสายงาน',
                'description' => 'รวม Prompt Templates กว่า 100+ ชุด สำหรับงานการตลาด งานเขียน วิเคราะห์ข้อมูล HR การเงิน และอีกมากมาย พร้อมใช้ได้ทันที',
                'price' => 1790,
                'category_slug' => 'prompt-engineering',
                'thumbnail_url' => 'https://images.unsplash.com/photo-1526374965328-7f61d4dc18c5?w=800',
                'level' => 'beginner',
                'duration' => 160,
                'is_published' => true,
            ],

            // AI Automation
            [
                'title' => 'AI Automation with Make & Zapier',
                'description' => 'สร้างระบบอัตโนมัติด้วย AI ผ่าน Make (Integromat) และ Zapier เชื่อมต่อเครื่องมือกว่า 50+ แอป ลดงานซ้ำซ้อนได้กว่า 80%',
                'price' => 2990,
                'category_slug' => 'ai-automation',
                'thumbnail_url' => 'https://images.unsplash.com/photo-1518432031352-d6fc5c10da5a?w=800',
                'level' => 'intermediate',
                'duration' => 280,
                'is_published' => true,
            ],
            [
                'title' => 'สร้าง AI Chatbot สำหรับธุรกิจ',
                'description' => 'เรียนรู้การสร้าง AI Chatbot ตอบลูกค้าอัตโนมัติ ทั้งบน LINE, Facebook Messenger และเว็บไซต์ ไม่ต้องเขียนโค้ด',
                'price' => 2490,
                'category_slug' => 'ai-automation',
                'thumbnail_url' => 'https://images.unsplash.com/photo-1531746790095-6c5c78131748?w=800',
                'level' => 'beginner',
                'duration' => 200,
                'is_published' => true,
            ],

            // AI Strategy
            [
                'title' => 'AI Strategy Blueprint สำหรับ CEO',
                'description' => 'วางแผนกลยุทธ์ AI ระดับองค์กร เรียนรู้จาก Case Study จริงของบริษัทชั้นนำ พร้อม Framework ที่นำไปใช้ได้ทันที',
                'price' => 5990,
                'category_slug' => 'ai-strategy',
                'thumbnail_url' => 'https://images.unsplash.com/photo-1454165804606-c3d57bc86b40?w=800',
                'level' => 'advanced',
                'duration' => 360,
                'is_published' => true,
            ],
            [
                'title' => 'AI ROI: วัดผลตอบแทนจากการลงทุน AI',
                'description' => 'เรียนรู้วิธีคำนวณ ROI จากโปรเจกต์ AI ตั้งแต่การตั้ง KPI วัดผล และนำเสนอผลลัพธ์ต่อผู้บริหาร พร้อม Template Excel',
                'price' => 2990,
                'category_slug' => 'ai-strategy',
                'thumbnail_url' => 'https://images.unsplash.com/photo-1551288049-bebda4e38f71?w=800',
                'level' => 'intermediate',
                'duration' => 240,
                'is_published' => true,
            ],
        ];

        foreach ($courses as $courseData) {
            $slug = $courseData['category_slug'];
            unset($courseData['category_slug']);

            $courseData['instructor_id'] = $instructor->id;
            $courseData['category_id'] = $categories[$slug] ?? null;

            Course::create($courseData);
        }

        $this->command->info('✅ Courses seeded successfully! (' . count($courses) . ' courses)');
    }
}
