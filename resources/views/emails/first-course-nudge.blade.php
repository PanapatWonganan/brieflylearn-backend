@extends('emails.layout')

@section('title', 'คอร์ส AI ที่เหมาะกับคุณรออยู่')

@section('content')
    <h1>พร้อมเริ่มต้นแล้วหรือยัง {{ $user->full_name }}? 🚀</h1>

    <p>เรารวบรวมคอร์ส AI ยอดนิยมที่เหมาะกับคุณมาให้แล้ว เลือกคอร์สที่ชอบและเริ่มเรียนได้เลย!</p>

    <h2>3 คอร์สแนะนำสำหรับคุณ</h2>

    <div style="border: 2px solid #e5e7eb; border-radius: 12px; padding: 20px; margin: 20px 0; background-color: #ffffff;">
        <h3 style="margin: 0 0 10px 0; color: #4a7a5a; font-size: 18px;">🤖 AI เบื้องต้น: จากศูนย์สู่ฮีโร่</h3>
        <p style="margin: 0 0 10px 0; color: #6b7280; font-size: 14px;">
            <span style="background-color: #dbeafe; color: #1e40af; padding: 3px 8px; border-radius: 4px; font-size: 12px; font-weight: 600; margin-right: 8px;">เริ่มต้น</span>
            12 บทเรียน · 3 ชั่วโมง
        </p>
        <p style="margin: 0 0 15px 0; color: #4b5563; font-size: 15px;">
            เรียนรู้พื้นฐาน AI และ Machine Learning ตั้งแต่เริ่มต้น เหมาะสำหรับผู้ที่ไม่มีพื้นฐาน
        </p>
        <a href="{{ config('app.frontend_url', 'https://antiparallel.co') }}/courses/ai-basics" style="color: #00FFBA; text-decoration: none; font-weight: 600; font-size: 15px;">เริ่มเรียนเลย →</a>
    </div>

    <div style="border: 2px solid #e5e7eb; border-radius: 12px; padding: 20px; margin: 20px 0; background-color: #ffffff;">
        <h3 style="margin: 0 0 10px 0; color: #4a7a5a; font-size: 18px;">💬 ChatGPT & Prompt Engineering</h3>
        <p style="margin: 0 0 10px 0; color: #6b7280; font-size: 14px;">
            <span style="background-color: #fef3c7; color: #92400e; padding: 3px 8px; border-radius: 4px; font-size: 12px; font-weight: 600; margin-right: 8px;">กลาง</span>
            15 บทเรียน · 4 ชั่วโมง
        </p>
        <p style="margin: 0 0 15px 0; color: #4b5563; font-size: 15px;">
            เรียนรู้เทคนิคการใช้ ChatGPT อย่างมืออาชีพ เขียน Prompt ที่ได้ผลลัพธ์ที่ต้องการ
        </p>
        <a href="{{ config('app.frontend_url', 'https://antiparallel.co') }}/courses/chatgpt-prompts" style="color: #00FFBA; text-decoration: none; font-weight: 600; font-size: 15px;">เริ่มเรียนเลย →</a>
    </div>

    <div style="border: 2px solid #e5e7eb; border-radius: 12px; padding: 20px; margin: 20px 0; background-color: #ffffff;">
        <h3 style="margin: 0 0 10px 0; color: #4a7a5a; font-size: 18px;">🎨 AI Art Generation</h3>
        <p style="margin: 0 0 10px 0; color: #6b7280; font-size: 14px;">
            <span style="background-color: #dbeafe; color: #1e40af; padding: 3px 8px; border-radius: 4px; font-size: 12px; font-weight: 600; margin-right: 8px;">เริ่มต้น</span>
            10 บทเรียน · 2.5 ชั่วโมง
        </p>
        <p style="margin: 0 0 15px 0; color: #4b5563; font-size: 15px;">
            สร้างงานศิลปะด้วย AI ใช้เครื่องมืออย่าง Midjourney, DALL-E และ Stable Diffusion
        </p>
        <a href="{{ config('app.frontend_url', 'https://antiparallel.co') }}/courses/ai-art" style="color: #00FFBA; text-decoration: none; font-weight: 600; font-size: 15px;">เริ่มเรียนเลย →</a>
    </div>

    <div class="button-container">
        <a href="{{ config('app.frontend_url', 'https://antiparallel.co') }}/courses" class="button">
            ดูคอร์สทั้งหมด
        </a>
    </div>

    <div class="info-box">
        <p><strong>🎁 โปรโมชั่นพิเศษ:</strong></p>
        <p style="margin-top: 10px;">
            เริ่มเรียนคอร์สแรกภายใน 48 ชั่วโมงนี้ รับฟรี 200 XP โบนัส!
        </p>
    </div>

    <p style="margin-top: 30px;">
        เริ่มต้นการเรียนรู้ของคุณวันนี้!<br>
        <strong>ทีมงาน Antiparallel</strong>
    </p>
@endsection
