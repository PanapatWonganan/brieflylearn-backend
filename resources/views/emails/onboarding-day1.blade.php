@extends('emails.layout')

@section('title', 'เริ่มต้นกับ BrieflyLearn')

@section('content')
    <h1>สวัสดี {{ $user->full_name }}! 👋</h1>

    <p>ยินดีต้อนรับสู่ BrieflyLearn อีกครั้ง! เราดีใจที่คุณพร้อมจะเริ่มต้นการเรียนรู้แล้ว</p>

    <p>เพื่อให้คุณเริ่มต้นได้อย่างราบรื่น เรามี <strong>3 สิ่งที่คุณควรทำวันนี้</strong>:</p>

    <div style="background-color: #f0fdf4; border: 2px solid #4a7a5a; border-radius: 12px; padding: 25px; margin: 25px 0;">
        <div style="margin-bottom: 25px;">
            <div style="display: inline-block; width: 40px; height: 40px; background-color: #4a7a5a; color: white; border-radius: 50%; text-align: center; line-height: 40px; font-weight: 700; font-size: 20px; margin-right: 15px;">1</div>
            <div style="display: inline-block; vertical-align: top; width: calc(100% - 60px);">
                <h3 style="margin: 0 0 8px 0; color: #1a1a1a; font-size: 18px;">📚 ดูคอร์สแนะนำ</h3>
                <p style="margin: 0; color: #4b5563; font-size: 15px;">เลือกคอร์ส AI ที่เหมาะกับระดับของคุณและเริ่มเรียนบทแรก</p>
            </div>
        </div>

        <div style="margin-bottom: 25px;">
            <div style="display: inline-block; width: 40px; height: 40px; background-color: #4a7a5a; color: white; border-radius: 50%; text-align: center; line-height: 40px; font-weight: 700; font-size: 20px; margin-right: 15px;">2</div>
            <div style="display: inline-block; vertical-align: top; width: calc(100% - 60px);">
                <h3 style="margin: 0 0 8px 0; color: #1a1a1a; font-size: 18px;">🎯 ลองทำแบบทดสอบ</h3>
                <p style="margin: 0; color: #4b5563; font-size: 15px;">ทำแบบทดสอบความรู้เบื้องต้นเพื่อวัดระดับและรับ XP แรก</p>
            </div>
        </div>

        <div>
            <div style="display: inline-block; width: 40px; height: 40px; background-color: #4a7a5a; color: white; border-radius: 50%; text-align: center; line-height: 40px; font-weight: 700; font-size: 20px; margin-right: 15px;">3</div>
            <div style="display: inline-block; vertical-align: top; width: calc(100% - 60px);">
                <h3 style="margin: 0 0 8px 0; color: #1a1a1a; font-size: 18px;">🌱 สำรวจ AI Lab</h3>
                <p style="margin: 0; color: #4b5563; font-size: 15px;">ลองเล่นกับเครื่องมือ AI และปลูกต้นไม้ต้นแรกในสวนของคุณ</p>
            </div>
        </div>
    </div>

    <div class="button-container">
        <a href="{{ config('app.frontend_url', 'https://brieflylearn.com') }}/dashboard" class="button">
            เริ่มต้นเลย
        </a>
    </div>

    <div class="info-box">
        <p><strong>💡 เคล็ดลับ:</strong></p>
        <p style="margin-top: 10px;">
            ตั้งเป้าหมายให้เรียนอย่างน้อย 15 นาทีต่อวัน เพื่อสร้าง streak และรับโบนัส XP!
        </p>
    </div>

    <p style="margin-top: 30px;">
        หากมีคำถามหรือต้องการความช่วยเหลือ ติดต่อเราได้ทุกเมื่อ<br>
        <strong>ทีมงาน BrieflyLearn</strong>
    </p>
@endsection
