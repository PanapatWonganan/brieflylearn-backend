<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Exam;
use App\Models\ExamQuestion;

class ExamSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $exams = [
            // 1. AI Fundamentals Quiz - Beginner
            [
                'title' => 'แบบทดสอบ AI พื้นฐาน',
                'description' => 'ทดสอบความเข้าใจพื้นฐานเกี่ยวกับ AI และการใช้งานในชีวิตประจำวัน',
                'duration_minutes' => 30,
                'passing_score' => 70,
                'difficulty' => 'beginner',
                'category' => 'AI Fundamentals',
                'price' => 0,
                'is_published' => true,
                'questions' => [
                    [
                        'question_text' => 'AI ย่อมาจากอะไร?',
                        'options' => [
                            ['label' => 'A', 'text' => 'Artificial Intelligence'],
                            ['label' => 'B', 'text' => 'Automatic Information'],
                            ['label' => 'C', 'text' => 'Advanced Internet'],
                            ['label' => 'D', 'text' => 'Application Interface'],
                        ],
                        'correct_answer' => 'A',
                        'points' => 1,
                    ],
                    [
                        'question_text' => 'Machine Learning คืออะไร?',
                        'options' => [
                            ['label' => 'A', 'text' => 'การเรียนรู้ภาษาต่างประเทศ'],
                            ['label' => 'B', 'text' => 'การที่คอมพิวเตอร์เรียนรู้จากข้อมูลโดยไม่ต้องเขียนโปรแกรมโดยตรง'],
                            ['label' => 'C', 'text' => 'การเรียนรู้การใช้เครื่องจักร'],
                            ['label' => 'D', 'text' => 'การสอนหุ่นยนต์'],
                        ],
                        'correct_answer' => 'B',
                        'points' => 1,
                    ],
                    [
                        'question_text' => 'ข้อใดไม่ใช่ตัวอย่างการใช้งาน AI ในชีวิตประจำวัน?',
                        'options' => [
                            ['label' => 'A', 'text' => 'การแนะนำหนังใน Netflix'],
                            ['label' => 'B', 'text' => 'ผู้ช่วยเสียงอย่าง Siri'],
                            ['label' => 'C', 'text' => 'การส่งจดหมายทางไปรษณีย์'],
                            ['label' => 'D', 'text' => 'การแปลภาษาอัตโนมัติ'],
                        ],
                        'correct_answer' => 'C',
                        'points' => 1,
                    ],
                    [
                        'question_text' => 'ChatGPT เป็น AI ประเภทใด?',
                        'options' => [
                            ['label' => 'A', 'text' => 'Image AI'],
                            ['label' => 'B', 'text' => 'Large Language Model (LLM)'],
                            ['label' => 'C', 'text' => 'Video AI'],
                            ['label' => 'D', 'text' => 'Music AI'],
                        ],
                        'correct_answer' => 'B',
                        'points' => 1,
                    ],
                    [
                        'question_text' => 'ข้อใดเป็นข้อจำกัดของ AI ปัจจุบัน?',
                        'options' => [
                            ['label' => 'A', 'text' => 'ไม่สามารถประมวลผลข้อมูลได้เร็ว'],
                            ['label' => 'B', 'text' => 'ไม่มีความเข้าใจบริบทและอารมณ์ลึกซึ้งเหมือนมนุษย์'],
                            ['label' => 'C', 'text' => 'ใช้งานได้เฉพาะภาษาอังกฤษ'],
                            ['label' => 'D', 'text' => 'ไม่สามารถเรียนรู้ได้'],
                        ],
                        'correct_answer' => 'B',
                        'points' => 1,
                    ],
                    [
                        'question_text' => 'Deep Learning เกี่ยวข้องกับโครงสร้างใด?',
                        'options' => [
                            ['label' => 'A', 'text' => 'Neural Network'],
                            ['label' => 'B', 'text' => 'Cloud Storage'],
                            ['label' => 'C', 'text' => 'Database'],
                            ['label' => 'D', 'text' => 'Website'],
                        ],
                        'correct_answer' => 'A',
                        'points' => 1,
                    ],
                    [
                        'question_text' => 'NLP ย่อมาจากอะไร?',
                        'options' => [
                            ['label' => 'A', 'text' => 'Network Link Protocol'],
                            ['label' => 'B', 'text' => 'Natural Language Processing'],
                            ['label' => 'C', 'text' => 'New Learning Platform'],
                            ['label' => 'D', 'text' => 'Neural Logic Programming'],
                        ],
                        'correct_answer' => 'B',
                        'points' => 1,
                    ],
                    [
                        'question_text' => 'ข้อใดเป็นประโยชน์ของ AI ในธุรกิจ?',
                        'options' => [
                            ['label' => 'A', 'text' => 'ลดต้นทุนและเพิ่มประสิทธิภาพ'],
                            ['label' => 'B', 'text' => 'ทำงานแทนคนได้ทั้งหมด'],
                            ['label' => 'C', 'text' => 'ไม่ต้องใช้พนักงาน'],
                            ['label' => 'D', 'text' => 'ไม่มีข้อผิดพลาดเลย'],
                        ],
                        'correct_answer' => 'A',
                        'points' => 1,
                    ],
                    [
                        'question_text' => 'Generative AI คืออะไร?',
                        'options' => [
                            ['label' => 'A', 'text' => 'AI ที่สร้างสรรค์เนื้อหาใหม่'],
                            ['label' => 'B', 'text' => 'AI ที่วิเคราะห์ข้อมูล'],
                            ['label' => 'C', 'text' => 'AI ที่ควบคุมหุ่นยนต์'],
                            ['label' => 'D', 'text' => 'AI ที่ป้องกันไวรัส'],
                        ],
                        'correct_answer' => 'A',
                        'points' => 1,
                    ],
                    [
                        'question_text' => 'ข้อใดเป็นหลักจริยธรรมที่สำคัญในการใช้ AI?',
                        'options' => [
                            ['label' => 'A', 'text' => 'ใช้ AI แทนที่มนุษย์ทั้งหมด'],
                            ['label' => 'B', 'text' => 'ความโปร่งใสและความเป็นธรรม'],
                            ['label' => 'C', 'text' => 'เก็บข้อมูลทุกอย่างไม่ต้องขออนุญาต'],
                            ['label' => 'D', 'text' => 'ไม่ต้องตรวจสอบผลลัพธ์'],
                        ],
                        'correct_answer' => 'B',
                        'points' => 1,
                    ],
                ],
            ],

            // 2. Prompt Engineering - Intermediate
            [
                'title' => 'แบบทดสอบ Prompt Engineering',
                'description' => 'ทดสอบความรู้เกี่ยวกับเทคนิคการเขียน Prompt ที่มีประสิทธิภาพ',
                'duration_minutes' => 45,
                'passing_score' => 70,
                'difficulty' => 'intermediate',
                'category' => 'Prompt Engineering',
                'price' => 0,
                'is_published' => true,
                'questions' => [
                    [
                        'question_text' => 'Prompt Engineering คืออะไร?',
                        'options' => [
                            ['label' => 'A', 'text' => 'การเขียนโค้ดโปรแกรม'],
                            ['label' => 'B', 'text' => 'การออกแบบคำสั่งให้ AI เพื่อผลลัพธ์ที่ต้องการ'],
                            ['label' => 'C', 'text' => 'การสร้าง AI'],
                            ['label' => 'D', 'text' => 'การซ่อมแซมระบบ'],
                        ],
                        'correct_answer' => 'B',
                        'points' => 1,
                    ],
                    [
                        'question_text' => 'ข้อใดเป็นหลักการสำคัญในการเขียน Prompt ที่ดี?',
                        'options' => [
                            ['label' => 'A', 'text' => 'เขียนสั้นที่สุด'],
                            ['label' => 'B', 'text' => 'ชัดเจน เฉพาะเจาะจง และให้บริบท'],
                            ['label' => 'C', 'text' => 'ใช้คำยากๆ'],
                            ['label' => 'D', 'text' => 'เขียนยาวที่สุด'],
                        ],
                        'correct_answer' => 'B',
                        'points' => 1,
                    ],
                    [
                        'question_text' => 'Chain-of-Thought Prompting คืออะไร?',
                        'options' => [
                            ['label' => 'A', 'text' => 'การสั่งให้ AI ทำหลายงานพร้อมกัน'],
                            ['label' => 'B', 'text' => 'การขอให้ AI อธิบายขั้นตอนการคิดทีละขั้น'],
                            ['label' => 'C', 'text' => 'การเชื่อมโยง AI หลายตัว'],
                            ['label' => 'D', 'text' => 'การใช้ AI แบบต่อเนื่อง'],
                        ],
                        'correct_answer' => 'B',
                        'points' => 1,
                    ],
                    [
                        'question_text' => 'Few-Shot Prompting หมายถึง?',
                        'options' => [
                            ['label' => 'A', 'text' => 'ให้ตัวอย่างน้อยๆ ก่อนถามคำถาม'],
                            ['label' => 'B', 'text' => 'ถามคำถามสั้นๆ'],
                            ['label' => 'C', 'text' => 'ใช้เวลาน้อย'],
                            ['label' => 'D', 'text' => 'ลองหลายครั้ง'],
                        ],
                        'correct_answer' => 'A',
                        'points' => 1,
                    ],
                    [
                        'question_text' => 'Role Prompting คือการ?',
                        'options' => [
                            ['label' => 'A', 'text' => 'กำหนดบทบาทให้ AI เช่น "คุณเป็นนักการตลาด"'],
                            ['label' => 'B', 'text' => 'เปลี่ยน AI'],
                            ['label' => 'C', 'text' => 'ตั้งค่า AI'],
                            ['label' => 'D', 'text' => 'ลบข้อมูล'],
                        ],
                        'correct_answer' => 'A',
                        'points' => 1,
                    ],
                    [
                        'question_text' => 'Temperature parameter ควบคุมอะไร?',
                        'options' => [
                            ['label' => 'A', 'text' => 'ความเร็วในการตอบ'],
                            ['label' => 'B', 'text' => 'ความสร้างสรรค์/สุ่มของคำตอบ'],
                            ['label' => 'C', 'text' => 'ขนาดของคำตอบ'],
                            ['label' => 'D', 'text' => 'ภาษาของคำตอบ'],
                        ],
                        'correct_answer' => 'B',
                        'points' => 1,
                    ],
                    [
                        'question_text' => 'ข้อใดเป็นเทคนิคการเขียน Prompt ที่มีประสิทธิภาพ?',
                        'options' => [
                            ['label' => 'A', 'text' => 'ใส่คำว่า "โปรด" เยอะๆ'],
                            ['label' => 'B', 'text' => 'ระบุ format ผลลัพธ์ที่ต้องการชัดเจน'],
                            ['label' => 'C', 'text' => 'เขียนเป็นภาษาอังกฤษเท่านั้น'],
                            ['label' => 'D', 'text' => 'ใช้อิโมจิเยอะๆ'],
                        ],
                        'correct_answer' => 'B',
                        'points' => 1,
                    ],
                    [
                        'question_text' => 'Zero-Shot Prompting คืออะไร?',
                        'options' => [
                            ['label' => 'A', 'text' => 'ถามโดยไม่ให้ตัวอย่าง'],
                            ['label' => 'B', 'text' => 'ไม่ถามคำถาม'],
                            ['label' => 'C', 'text' => 'ถามทีเดียวหลายคำถาม'],
                            ['label' => 'D', 'text' => 'ไม่ใช้ AI'],
                        ],
                        'correct_answer' => 'A',
                        'points' => 1,
                    ],
                    [
                        'question_text' => 'การใช้ System Message มีประโยชน์อย่างไร?',
                        'options' => [
                            ['label' => 'A', 'text' => 'ตั้งพฤติกรรมและบริบทพื้นฐานของ AI'],
                            ['label' => 'B', 'text' => 'ลบข้อมูล'],
                            ['label' => 'C', 'text' => 'เปลี่ยนภาษา'],
                            ['label' => 'D', 'text' => 'เพิ่มความเร็ว'],
                        ],
                        'correct_answer' => 'A',
                        'points' => 1,
                    ],
                    [
                        'question_text' => 'Prompt Template คืออะไร?',
                        'options' => [
                            ['label' => 'A', 'text' => 'แม่แบบ Prompt ที่ใช้ซ้ำได้'],
                            ['label' => 'B', 'text' => 'รูปแบบการตอบ'],
                            ['label' => 'C', 'text' => 'โปรแกรม AI'],
                            ['label' => 'D', 'text' => 'เว็บไซต์'],
                        ],
                        'correct_answer' => 'A',
                        'points' => 1,
                    ],
                    [
                        'question_text' => 'การใช้ Delimiter (เช่น """, ###) ช่วยอะไร?',
                        'options' => [
                            ['label' => 'A', 'text' => 'แยกส่วนต่างๆ ของ Prompt ให้ชัดเจน'],
                            ['label' => 'B', 'text' => 'ทำให้สวยงาม'],
                            ['label' => 'C', 'text' => 'เพิ่มความเร็ว'],
                            ['label' => 'D', 'text' => 'ลดค่าใช้จ่าย'],
                        ],
                        'correct_answer' => 'A',
                        'points' => 1,
                    ],
                    [
                        'question_text' => 'ข้อใดเป็นข้อควรระวังในการเขียน Prompt?',
                        'options' => [
                            ['label' => 'A', 'text' => 'คำสั่งที่คลุมเครือ'],
                            ['label' => 'B', 'text' => 'ใช้ภาษาไทย'],
                            ['label' => 'C', 'text' => 'เขียนยาว'],
                            ['label' => 'D', 'text' => 'ให้บริบท'],
                        ],
                        'correct_answer' => 'A',
                        'points' => 1,
                    ],
                    [
                        'question_text' => 'Iterative Prompting คือ?',
                        'options' => [
                            ['label' => 'A', 'text' => 'การปรับปรุง Prompt ทีละน้อยจากผลลัพธ์'],
                            ['label' => 'B', 'text' => 'การเขียน Prompt ครั้งเดียว'],
                            ['label' => 'C', 'text' => 'การใช้ AI หลายตัว'],
                            ['label' => 'D', 'text' => 'การลบและเขียนใหม่'],
                        ],
                        'correct_answer' => 'A',
                        'points' => 1,
                    ],
                    [
                        'question_text' => 'Max Tokens ควบคุมอะไร?',
                        'options' => [
                            ['label' => 'A', 'text' => 'จำนวนคำสูงสุดในคำตอบ'],
                            ['label' => 'B', 'text' => 'จำนวนครั้งที่ใช้งาน'],
                            ['label' => 'C', 'text' => 'จำนวนผู้ใช้'],
                            ['label' => 'D', 'text' => 'ราคา'],
                        ],
                        'correct_answer' => 'A',
                        'points' => 1,
                    ],
                    [
                        'question_text' => 'การใช้ Prompt ที่ดีส่งผลต่ออะไร?',
                        'options' => [
                            ['label' => 'A', 'text' => 'คุณภาพและความแม่นยำของผลลัพธ์'],
                            ['label' => 'B', 'text' => 'ราคาเท่านั้น'],
                            ['label' => 'C', 'text' => 'ไม่มีผล'],
                            ['label' => 'D', 'text' => 'ความเร็วอินเทอร์เน็ต'],
                        ],
                        'correct_answer' => 'A',
                        'points' => 1,
                    ],
                ],
            ],

            // 3. AI for Business - Intermediate
            [
                'title' => 'แบบทดสอบ AI สำหรับธุรกิจ',
                'description' => 'ทดสอบความรู้การนำ AI ไปใช้ในธุรกิจและการตลาด',
                'duration_minutes' => 40,
                'passing_score' => 70,
                'difficulty' => 'intermediate',
                'category' => 'AI Business',
                'price' => 0,
                'is_published' => true,
                'questions' => [
                    [
                        'question_text' => 'AI สามารถช่วยในด้านการตลาดได้อย่างไร?',
                        'options' => [
                            ['label' => 'A', 'text' => 'วิเคราะห์พฤติกรรมลูกค้าและ personalization'],
                            ['label' => 'B', 'text' => 'ทดแทนนักการตลาดทั้งหมด'],
                            ['label' => 'C', 'text' => 'สร้างสินค้า'],
                            ['label' => 'D', 'text' => 'จัดส่งสินค้า'],
                        ],
                        'correct_answer' => 'A',
                        'points' => 1,
                    ],
                    [
                        'question_text' => 'CRM ที่มี AI ช่วยอะไรได้บ้าง?',
                        'options' => [
                            ['label' => 'A', 'text' => 'ทำนายพฤติกรรมลูกค้าและแนะนำสินค้า'],
                            ['label' => 'B', 'text' => 'สร้างเว็บไซต์'],
                            ['label' => 'C', 'text' => 'ทำบัญชี'],
                            ['label' => 'D', 'text' => 'ออกแบบโลโก้'],
                        ],
                        'correct_answer' => 'A',
                        'points' => 1,
                    ],
                    [
                        'question_text' => 'Chatbot AI มีประโยชน์อย่างไรกับธุรกิจ?',
                        'options' => [
                            ['label' => 'A', 'text' => 'ตอบคำถามลูกค้าได้ 24/7'],
                            ['label' => 'B', 'text' => 'ขายสินค้าเองทั้งหมด'],
                            ['label' => 'C', 'text' => 'ผลิตสินค้า'],
                            ['label' => 'D', 'text' => 'จัดการคลังสินค้า'],
                        ],
                        'correct_answer' => 'A',
                        'points' => 1,
                    ],
                    [
                        'question_text' => 'Predictive Analytics คืออะไร?',
                        'options' => [
                            ['label' => 'A', 'text' => 'การวิเคราะห์เพื่อทำนายแนวโน้มในอนาคต'],
                            ['label' => 'B', 'text' => 'การวิเคราะห์อดีต'],
                            ['label' => 'C', 'text' => 'การวิเคราะห์ปัจจุบัน'],
                            ['label' => 'D', 'text' => 'การคำนวณราคา'],
                        ],
                        'correct_answer' => 'A',
                        'points' => 1,
                    ],
                    [
                        'question_text' => 'AI สามารถช่วย Content Marketing อย่างไร?',
                        'options' => [
                            ['label' => 'A', 'text' => 'สร้างไอเดีย เขียนร่าง และแนะนำหัวข้อ'],
                            ['label' => 'B', 'text' => 'แทนที่ Content Creator'],
                            ['label' => 'C', 'text' => 'โพสต์เองอัตโนมัติ'],
                            ['label' => 'D', 'text' => 'ไม่สามารถช่วยได้'],
                        ],
                        'correct_answer' => 'A',
                        'points' => 1,
                    ],
                    [
                        'question_text' => 'Dynamic Pricing ที่ใช้ AI คืออะไร?',
                        'options' => [
                            ['label' => 'A', 'text' => 'ปรับราคาตามอุปสงค์-อุปทานแบบเรียลไทม์'],
                            ['label' => 'B', 'text' => 'ลดราคาเสมอ'],
                            ['label' => 'C', 'text' => 'ตั้งราคาสูงเสมอ'],
                            ['label' => 'D', 'text' => 'ไม่เปลี่ยนราคา'],
                        ],
                        'correct_answer' => 'A',
                        'points' => 1,
                    ],
                    [
                        'question_text' => 'AI ช่วยในการ Segmentation ได้อย่างไร?',
                        'options' => [
                            ['label' => 'A', 'text' => 'แบ่งกลุ่มลูกค้าตามพฤติกรรมอัตโนมัติ'],
                            ['label' => 'B', 'text' => 'แบ่งสินค้า'],
                            ['label' => 'C', 'text' => 'แบ่งพนักงาน'],
                            ['label' => 'D', 'text' => 'แบ่งเงิน'],
                        ],
                        'correct_answer' => 'A',
                        'points' => 1,
                    ],
                    [
                        'question_text' => 'Sentiment Analysis ใช้ในการอะไร?',
                        'options' => [
                            ['label' => 'A', 'text' => 'วิเคราะห์ความรู้สึกของลูกค้าจากรีวิวและโซเชียล'],
                            ['label' => 'B', 'text' => 'วิเคราะห์ยอดขาย'],
                            ['label' => 'C', 'text' => 'วิเคราะห์สินค้าคงคลัง'],
                            ['label' => 'D', 'text' => 'วิเคราะห์คู่แข่ง'],
                        ],
                        'correct_answer' => 'A',
                        'points' => 1,
                    ],
                    [
                        'question_text' => 'Recommendation Engine ทำงานอย่างไร?',
                        'options' => [
                            ['label' => 'A', 'text' => 'แนะนำสินค้าตามประวัติและความชอบของผู้ใช้'],
                            ['label' => 'B', 'text' => 'แนะนำสินค้าราคาแพงเสมอ'],
                            ['label' => 'C', 'text' => 'แนะนำสินค้าสุ่ม'],
                            ['label' => 'D', 'text' => 'แนะนำสินค้าเดิมซ้ำ'],
                        ],
                        'correct_answer' => 'A',
                        'points' => 1,
                    ],
                    [
                        'question_text' => 'AI ช่วยในการ Lead Scoring อย่างไร?',
                        'options' => [
                            ['label' => 'A', 'text' => 'ให้คะแนนโอกาสปิดการขายของแต่ละลูกค้า'],
                            ['label' => 'B', 'text' => 'ให้คะแนนพนักงาน'],
                            ['label' => 'C', 'text' => 'ให้คะแนนสินค้า'],
                            ['label' => 'D', 'text' => 'ให้คะแนนบริษัท'],
                        ],
                        'correct_answer' => 'A',
                        'points' => 1,
                    ],
                    [
                        'question_text' => 'Churn Prediction คืออะไร?',
                        'options' => [
                            ['label' => 'A', 'text' => 'ทำนายลูกค้าที่จะหยุดใช้บริการ'],
                            ['label' => 'B', 'text' => 'ทำนายยอดขาย'],
                            ['label' => 'C', 'text' => 'ทำนายราคา'],
                            ['label' => 'D', 'text' => 'ทำนายสภาพอากาศ'],
                        ],
                        'correct_answer' => 'A',
                        'points' => 1,
                    ],
                    [
                        'question_text' => 'AI สามารถช่วย Email Marketing อย่างไร?',
                        'options' => [
                            ['label' => 'A', 'text' => 'Personalize เนื้อหาและหาเวลาส่งที่เหมาะสม'],
                            ['label' => 'B', 'text' => 'ส่งอีเมลสแปมเยอะๆ'],
                            ['label' => 'C', 'text' => 'ไม่สามารถช่วยได้'],
                            ['label' => 'D', 'text' => 'ส่งอีเมลเดิมซ้ำ'],
                        ],
                        'correct_answer' => 'A',
                        'points' => 1,
                    ],
                ],
            ],

            // 4. ChatGPT & Claude - Beginner
            [
                'title' => 'แบบทดสอบ ChatGPT & Claude',
                'description' => 'ทดสอบความรู้เกี่ยวกับการใช้งาน ChatGPT และ Claude',
                'duration_minutes' => 30,
                'passing_score' => 70,
                'difficulty' => 'beginner',
                'category' => 'AI Tools',
                'price' => 0,
                'is_published' => true,
                'questions' => [
                    [
                        'question_text' => 'ChatGPT พัฒนาโดยบริษัทใด?',
                        'options' => [
                            ['label' => 'A', 'text' => 'OpenAI'],
                            ['label' => 'B', 'text' => 'Google'],
                            ['label' => 'C', 'text' => 'Microsoft'],
                            ['label' => 'D', 'text' => 'Apple'],
                        ],
                        'correct_answer' => 'A',
                        'points' => 1,
                    ],
                    [
                        'question_text' => 'Claude พัฒนาโดยบริษัทใด?',
                        'options' => [
                            ['label' => 'A', 'text' => 'OpenAI'],
                            ['label' => 'B', 'text' => 'Anthropic'],
                            ['label' => 'C', 'text' => 'Google'],
                            ['label' => 'D', 'text' => 'Meta'],
                        ],
                        'correct_answer' => 'B',
                        'points' => 1,
                    ],
                    [
                        'question_text' => 'ChatGPT ย่อมาจากอะไร?',
                        'options' => [
                            ['label' => 'A', 'text' => 'Chat Generative Pre-trained Transformer'],
                            ['label' => 'B', 'text' => 'Chat General Purpose Tool'],
                            ['label' => 'C', 'text' => 'Chat Graphics Processing Technology'],
                            ['label' => 'D', 'text' => 'Chat Global Platform Technology'],
                        ],
                        'correct_answer' => 'A',
                        'points' => 1,
                    ],
                    [
                        'question_text' => 'ข้อใดเป็นจุดเด่นของ Claude?',
                        'options' => [
                            ['label' => 'A', 'text' => 'ความปลอดภัยและจริยธรรม'],
                            ['label' => 'B', 'text' => 'ราคาถูกที่สุด'],
                            ['label' => 'C', 'text' => 'เร็วที่สุด'],
                            ['label' => 'D', 'text' => 'ใช้ได้เฉพาะภาษาอังกฤษ'],
                        ],
                        'correct_answer' => 'A',
                        'points' => 1,
                    ],
                    [
                        'question_text' => 'ChatGPT Plus มีข้อได้เปรียบอะไร?',
                        'options' => [
                            ['label' => 'A', 'text' => 'เข้าถึงโมเดลใหม่และเร็วกว่า'],
                            ['label' => 'B', 'text' => 'ฟรี'],
                            ['label' => 'C', 'text' => 'ไม่มีข้อจำกัด'],
                            ['label' => 'D', 'text' => 'ใช้ได้บนมือถือเท่านั้น'],
                        ],
                        'correct_answer' => 'A',
                        'points' => 1,
                    ],
                    [
                        'question_text' => 'ข้อใดไม่ใช่การใช้งาน ChatGPT?',
                        'options' => [
                            ['label' => 'A', 'text' => 'เขียนโค้ด'],
                            ['label' => 'B', 'text' => 'แปลภาษา'],
                            ['label' => 'C', 'text' => 'สร้างวิดีโอ'],
                            ['label' => 'D', 'text' => 'สรุปข้อความ'],
                        ],
                        'correct_answer' => 'C',
                        'points' => 1,
                    ],
                    [
                        'question_text' => 'Claude มี Context Window ที่ยาวเท่าไร?',
                        'options' => [
                            ['label' => 'A', 'text' => 'มากกว่า 100K tokens'],
                            ['label' => 'B', 'text' => '1K tokens'],
                            ['label' => 'C', 'text' => '100 tokens'],
                            ['label' => 'D', 'text' => '10 tokens'],
                        ],
                        'correct_answer' => 'A',
                        'points' => 1,
                    ],
                    [
                        'question_text' => 'GPT ย่อมาจากอะไร?',
                        'options' => [
                            ['label' => 'A', 'text' => 'Generative Pre-trained Transformer'],
                            ['label' => 'B', 'text' => 'General Purpose Technology'],
                            ['label' => 'C', 'text' => 'Global Processing Tool'],
                            ['label' => 'D', 'text' => 'Graphics Processing Transformer'],
                        ],
                        'correct_answer' => 'A',
                        'points' => 1,
                    ],
                    [
                        'question_text' => 'ข้อใดควรระวังเมื่อใช้ ChatGPT?',
                        'options' => [
                            ['label' => 'A', 'text' => 'ข้อมูลอาจไม่ถูกต้อง ต้องตรวจสอบ'],
                            ['label' => 'B', 'text' => 'ถูกต้อง 100%'],
                            ['label' => 'C', 'text' => 'ไม่ต้องตรวจสอบ'],
                            ['label' => 'D', 'text' => 'ใช้แทนการคิดทั้งหมด'],
                        ],
                        'correct_answer' => 'A',
                        'points' => 1,
                    ],
                    [
                        'question_text' => 'Claude เน้นเรื่องอะไรเป็นพิเศษ?',
                        'options' => [
                            ['label' => 'A', 'text' => 'Constitutional AI และความปลอดภัย'],
                            ['label' => 'B', 'text' => 'ความเร็ว'],
                            ['label' => 'C', 'text' => 'ราคา'],
                            ['label' => 'D', 'text' => 'กราฟิก'],
                        ],
                        'correct_answer' => 'A',
                        'points' => 1,
                    ],
                ],
            ],

            // 5. AI Automation - Advanced
            [
                'title' => 'แบบทดสอบ AI Automation',
                'description' => 'ทดสอบความรู้เกี่ยวกับการสร้างระบบอัตโนมัติด้วย AI',
                'duration_minutes' => 50,
                'passing_score' => 70,
                'difficulty' => 'advanced',
                'category' => 'AI Automation',
                'price' => 0,
                'is_published' => true,
                'questions' => [
                    [
                        'question_text' => 'Workflow Automation คืออะไร?',
                        'options' => [
                            ['label' => 'A', 'text' => 'การทำงานซ้ำๆ ให้เป็นอัตโนมัติ'],
                            ['label' => 'B', 'text' => 'การทำงานด้วยมือ'],
                            ['label' => 'C', 'text' => 'การเขียนโปรแกรม'],
                            ['label' => 'D', 'text' => 'การออกแบบ'],
                        ],
                        'correct_answer' => 'A',
                        'points' => 1,
                    ],
                    [
                        'question_text' => 'Zapier ใช้ทำอะไร?',
                        'options' => [
                            ['label' => 'A', 'text' => 'เชื่อมต่อแอปต่างๆ และทำงานอัตโนมัติ'],
                            ['label' => 'B', 'text' => 'สร้างเว็บไซต์'],
                            ['label' => 'C', 'text' => 'แก้ไขรูปภาพ'],
                            ['label' => 'D', 'text' => 'ทำวิดีโอ'],
                        ],
                        'correct_answer' => 'A',
                        'points' => 1,
                    ],
                    [
                        'question_text' => 'Make (Integromat) มีจุดเด่นอะไร?',
                        'options' => [
                            ['label' => 'A', 'text' => 'Visual automation builder ที่ซับซ้อนได้'],
                            ['label' => 'B', 'text' => 'ถูกที่สุด'],
                            ['label' => 'C', 'text' => 'เร็วที่สุด'],
                            ['label' => 'D', 'text' => 'ง่ายที่สุด'],
                        ],
                        'correct_answer' => 'A',
                        'points' => 1,
                    ],
                    [
                        'question_text' => 'Trigger ใน Automation คืออะไร?',
                        'options' => [
                            ['label' => 'A', 'text' => 'เหตุการณ์ที่เริ่มต้น Workflow'],
                            ['label' => 'B', 'text' => 'ผลลัพธ์'],
                            ['label' => 'C', 'text' => 'ข้อผิดพลาด'],
                            ['label' => 'D', 'text' => 'ค่าใช้จ่าย'],
                        ],
                        'correct_answer' => 'A',
                        'points' => 1,
                    ],
                    [
                        'question_text' => 'Action ใน Automation คืออะไร?',
                        'options' => [
                            ['label' => 'A', 'text' => 'งานที่ทำหลังจาก Trigger'],
                            ['label' => 'B', 'text' => 'การเริ่มต้น'],
                            ['label' => 'C', 'text' => 'การหยุด'],
                            ['label' => 'D', 'text' => 'การลบ'],
                        ],
                        'correct_answer' => 'A',
                        'points' => 1,
                    ],
                    [
                        'question_text' => 'RPA ย่อมาจากอะไร?',
                        'options' => [
                            ['label' => 'A', 'text' => 'Robotic Process Automation'],
                            ['label' => 'B', 'text' => 'Remote Process Application'],
                            ['label' => 'C', 'text' => 'Rapid Programming Algorithm'],
                            ['label' => 'D', 'text' => 'Real-time Processing Automation'],
                        ],
                        'correct_answer' => 'A',
                        'points' => 1,
                    ],
                    [
                        'question_text' => 'API Integration สำคัญอย่างไร?',
                        'options' => [
                            ['label' => 'A', 'text' => 'เชื่อมต่อระบบต่างๆ ให้ทำงานร่วมกัน'],
                            ['label' => 'B', 'text' => 'สร้างเว็บไซต์'],
                            ['label' => 'C', 'text' => 'แก้ไขภาพ'],
                            ['label' => 'D', 'text' => 'ไม่สำคัญ'],
                        ],
                        'correct_answer' => 'A',
                        'points' => 1,
                    ],
                    [
                        'question_text' => 'Webhook คืออะไร?',
                        'options' => [
                            ['label' => 'A', 'text' => 'การส่งข้อมูลอัตโนมัติเมื่อเกิดเหตุการณ์'],
                            ['label' => 'B', 'text' => 'เว็บไซต์'],
                            ['label' => 'C', 'text' => 'ฐานข้อมูล'],
                            ['label' => 'D', 'text' => 'โปรแกรม'],
                        ],
                        'correct_answer' => 'A',
                        'points' => 1,
                    ],
                    [
                        'question_text' => 'No-Code Automation มีประโยชน์อย่างไร?',
                        'options' => [
                            ['label' => 'A', 'text' => 'ทำ Automation โดยไม่ต้องเขียนโค้ด'],
                            ['label' => 'B', 'text' => 'ต้องเขียนโค้ด'],
                            ['label' => 'C', 'text' => 'ช้ากว่า'],
                            ['label' => 'D', 'text' => 'แพงกว่า'],
                        ],
                        'correct_answer' => 'A',
                        'points' => 1,
                    ],
                    [
                        'question_text' => 'Error Handling ใน Automation คืออะไร?',
                        'options' => [
                            ['label' => 'A', 'text' => 'การจัดการกับข้อผิดพลาด'],
                            ['label' => 'B', 'text' => 'การสร้างข้อผิดพลาด'],
                            ['label' => 'C', 'text' => 'การละเว้นข้อผิดพลาด'],
                            ['label' => 'D', 'text' => 'การซ่อนข้อผิดพลาด'],
                        ],
                        'correct_answer' => 'A',
                        'points' => 1,
                    ],
                    [
                        'question_text' => 'Data Mapping ใน Integration คืออะไร?',
                        'options' => [
                            ['label' => 'A', 'text' => 'การจับคู่ข้อมูลระหว่างระบบ'],
                            ['label' => 'B', 'text' => 'การลบข้อมูล'],
                            ['label' => 'C', 'text' => 'การสำรองข้อมูล'],
                            ['label' => 'D', 'text' => 'การเข้ารหัสข้อมูล'],
                        ],
                        'correct_answer' => 'A',
                        'points' => 1,
                    ],
                    [
                        'question_text' => 'Conditional Logic ใน Workflow ใช้ทำอะไร?',
                        'options' => [
                            ['label' => 'A', 'text' => 'กำหนดเงื่อนไขการทำงาน if/else'],
                            ['label' => 'B', 'text' => 'ลบข้อมูล'],
                            ['label' => 'C', 'text' => 'เพิ่มความเร็ว'],
                            ['label' => 'D', 'text' => 'ลดราคา'],
                        ],
                        'correct_answer' => 'A',
                        'points' => 1,
                    ],
                    [
                        'question_text' => 'Loop/Iteration ใน Automation ใช้เมื่อไร?',
                        'options' => [
                            ['label' => 'A', 'text' => 'ทำงานซ้ำกับข้อมูลหลายรายการ'],
                            ['label' => 'B', 'text' => 'ทำงานครั้งเดียว'],
                            ['label' => 'C', 'text' => 'หยุดการทำงาน'],
                            ['label' => 'D', 'text' => 'ลบข้อมูล'],
                        ],
                        'correct_answer' => 'A',
                        'points' => 1,
                    ],
                    [
                        'question_text' => 'Scheduled Trigger คืออะไร?',
                        'options' => [
                            ['label' => 'A', 'text' => 'เริ่มงานตามเวลาที่กำหนด'],
                            ['label' => 'B', 'text' => 'เริ่มงานสุ่ม'],
                            ['label' => 'C', 'text' => 'ไม่เริ่มงาน'],
                            ['label' => 'D', 'text' => 'หยุดงาน'],
                        ],
                        'correct_answer' => 'A',
                        'points' => 1,
                    ],
                    [
                        'question_text' => 'Testing Automation Workflow ทำไมสำคัญ?',
                        'options' => [
                            ['label' => 'A', 'text' => 'ตรวจสอบว่าทำงานถูกต้องก่อนใช้จริง'],
                            ['label' => 'B', 'text' => 'ไม่สำคัญ'],
                            ['label' => 'C', 'text' => 'เสียเวลา'],
                            ['label' => 'D', 'text' => 'แพง'],
                        ],
                        'correct_answer' => 'A',
                        'points' => 1,
                    ],
                ],
            ],

            // 6. AI Strategy - Advanced
            [
                'title' => 'แบบทดสอบ AI Strategy',
                'description' => 'ทดสอบความรู้การวางกลยุทธ์และนำ AI เข้าองค์กร',
                'duration_minutes' => 40,
                'passing_score' => 70,
                'difficulty' => 'advanced',
                'category' => 'AI Strategy',
                'price' => 0,
                'is_published' => true,
                'questions' => [
                    [
                        'question_text' => 'AI Strategy คืออะไร?',
                        'options' => [
                            ['label' => 'A', 'text' => 'แผนการนำ AI มาใช้ในองค์กรอย่างเป็นระบบ'],
                            ['label' => 'B', 'text' => 'การซื้อ AI'],
                            ['label' => 'C', 'text' => 'การเรียน AI'],
                            ['label' => 'D', 'text' => 'การขาย AI'],
                        ],
                        'correct_answer' => 'A',
                        'points' => 1,
                    ],
                    [
                        'question_text' => 'ขั้นตอนแรกในการนำ AI เข้าองค์กรคืออะไร?',
                        'options' => [
                            ['label' => 'A', 'text' => 'ประเมินความพร้อมและกำหนดเป้าหมาย'],
                            ['label' => 'B', 'text' => 'ซื้อ AI ทันที'],
                            ['label' => 'C', 'text' => 'ไล่พนักงาน'],
                            ['label' => 'D', 'text' => 'เปลี่ยนระบบทั้งหมด'],
                        ],
                        'correct_answer' => 'A',
                        'points' => 1,
                    ],
                    [
                        'question_text' => 'AI Readiness Assessment คืออะไร?',
                        'options' => [
                            ['label' => 'A', 'text' => 'การประเมินความพร้อมขององค์กรในการใช้ AI'],
                            ['label' => 'B', 'text' => 'การทดสอบ AI'],
                            ['label' => 'C', 'text' => 'การวัดความเร็ว'],
                            ['label' => 'D', 'text' => 'การวัดราคา'],
                        ],
                        'correct_answer' => 'A',
                        'points' => 1,
                    ],
                    [
                        'question_text' => 'Change Management สำคัญอย่างไรกับ AI Transformation?',
                        'options' => [
                            ['label' => 'A', 'text' => 'จัดการการเปลี่ยนแปลงและยอมรับจากพนักงาน'],
                            ['label' => 'B', 'text' => 'ไม่สำคัญ'],
                            ['label' => 'C', 'text' => 'บังคับพนักงาน'],
                            ['label' => 'D', 'text' => 'ลดพนักงาน'],
                        ],
                        'correct_answer' => 'A',
                        'points' => 1,
                    ],
                    [
                        'question_text' => 'ROI ของ AI วัดอย่างไร?',
                        'options' => [
                            ['label' => 'A', 'text' => 'เปรียบเทียบผลตอบแทนกับการลงทุน'],
                            ['label' => 'B', 'text' => 'ดูราคาเท่านั้น'],
                            ['label' => 'C', 'text' => 'ไม่ต้องวัด'],
                            ['label' => 'D', 'text' => 'ดูจำนวนพนักงาน'],
                        ],
                        'correct_answer' => 'A',
                        'points' => 1,
                    ],
                    [
                        'question_text' => 'Pilot Project ใน AI Implementation คืออะไร?',
                        'options' => [
                            ['label' => 'A', 'text' => 'โปรเจกต์ทดลองขนาดเล็กก่อนขยายผล'],
                            ['label' => 'B', 'text' => 'โปรเจกต์ใหญ่'],
                            ['label' => 'C', 'text' => 'โปรเจกต์สุดท้าย'],
                            ['label' => 'D', 'text' => 'โปรเจกต์ที่ล้มเหลว'],
                        ],
                        'correct_answer' => 'A',
                        'points' => 1,
                    ],
                    [
                        'question_text' => 'Data Governance สำคัญอย่างไรกับ AI?',
                        'options' => [
                            ['label' => 'A', 'text' => 'ควบคุมคุณภาพและความปลอดภัยของข้อมูล'],
                            ['label' => 'B', 'text' => 'ไม่สำคัญ'],
                            ['label' => 'C', 'text' => 'ลบข้อมูล'],
                            ['label' => 'D', 'text' => 'แชร์ข้อมูลทั้งหมด'],
                        ],
                        'correct_answer' => 'A',
                        'points' => 1,
                    ],
                    [
                        'question_text' => 'AI Ethics Policy คืออะไร?',
                        'options' => [
                            ['label' => 'A', 'text' => 'นโยบายการใช้ AI อย่างมีจริยธรรม'],
                            ['label' => 'B', 'text' => 'ข้อห้ามใช้ AI'],
                            ['label' => 'C', 'text' => 'ราคา AI'],
                            ['label' => 'D', 'text' => 'ความเร็ว AI'],
                        ],
                        'correct_answer' => 'A',
                        'points' => 1,
                    ],
                    [
                        'question_text' => 'Upskilling/Reskilling ทำไมสำคัญ?',
                        'options' => [
                            ['label' => 'A', 'text' => 'พัฒนาทักษะพนักงานให้ทำงานกับ AI'],
                            ['label' => 'B', 'text' => 'ไล่พนักงาน'],
                            ['label' => 'C', 'text' => 'ลดเงินเดือน'],
                            ['label' => 'D', 'text' => 'ไม่สำคัญ'],
                        ],
                        'correct_answer' => 'A',
                        'points' => 1,
                    ],
                    [
                        'question_text' => 'KPI สำหรับ AI Project ควรวัดอะไร?',
                        'options' => [
                            ['label' => 'A', 'text' => 'ประสิทธิภาพ ความแม่นยำ และผลกระทบทางธุรกิจ'],
                            ['label' => 'B', 'text' => 'ราคาเท่านั้น'],
                            ['label' => 'C', 'text' => 'จำนวนพนักงาน'],
                            ['label' => 'D', 'text' => 'ไม่ต้องวัด'],
                        ],
                        'correct_answer' => 'A',
                        'points' => 1,
                    ],
                ],
            ],
        ];

        foreach ($exams as $examData) {
            $questions = $examData['questions'];
            unset($examData['questions']);

            $examData['total_questions'] = count($questions);

            $exam = Exam::create($examData);

            foreach ($questions as $index => $questionData) {
                $questionData['exam_id'] = $exam->id;
                $questionData['order_index'] = $index + 1;

                ExamQuestion::create($questionData);
            }
        }

        $this->command->info('✅ Exams seeded successfully! (' . count($exams) . ' exams)');
    }
}

