@extends('emails.layout')

@section('title', 'Streak Milestone!')

@section('content')
    @if($streakDays == 7)
        <div class="achievement-badge" style="background: linear-gradient(135deg, #fef3c7 0%, #fde68a 100%);">
            <div class="achievement-icon">🔥</div>
            <div class="achievement-name">7 Day Streak!</div>
            <div class="achievement-xp">+ 100 XP โบนัส</div>
        </div>

        <h1>สุดยอด {{ $user->full_name }}! 🎉</h1>

        <p>คุณเรียนต่อเนื่องมาแล้ว <strong style="font-size: 20px; color: #4a7a5a;">7 วัน</strong> เต็ม! นี่คือจุดเริ่มต้นของนิสัยการเรียนรู้ที่ยอดเยี่ยม</p>

        <p>การรักษาความสม่ำเสมอเป็นกุญแจสำคัญในการเรียนรู้ คุณกำลังสร้างนิสัยที่ดีให้กับตัวเอง!</p>

        <div class="info-box">
            <p><strong>รางวัลที่คุณได้รับ:</strong></p>
            <p style="margin-top: 10px;">
                ✅ 100 XP โบนัส<br>
                ✅ Badge "7 Day Warrior"<br>
                ✅ ปลดล็อกต้นไม้พิเศษในสวน<br>
                ✅ 50 Impact Points
            </p>
        </div>

        <h2>เป้าหมายถัดไป: 30 วัน! 🎯</h2>

        <p>รักษา streak ไปต่อเนื่อง 30 วัน เพื่อรับ:</p>
        <ul>
            <li>500 XP โบนัส</li>
            <li>Badge "Monthly Master"</li>
            <li>ต้นไม้หายาก 1 ต้น</li>
            <li>ไอเทมพิเศษสำหรับสวน</li>
        </ul>

    @elseif($streakDays == 30)
        <div class="achievement-badge" style="background: linear-gradient(135deg, #e0e7ff 0%, #c7d2fe 100%);">
            <div class="achievement-icon">🏆</div>
            <div class="achievement-name">30 Day Master!</div>
            <div class="achievement-xp">+ 500 XP โบนัส</div>
        </div>

        <h1>เก่งมาก {{ $user->full_name }}! 🌟</h1>

        <p>คุณเรียนต่อเนื่องมาแล้ว <strong style="font-size: 24px; color: #4a7a5a;">30 วัน</strong> เต็ม! นี่คือความทุ่มเทและความมุ่งมั่นที่แท้จริง</p>

        <p>ในช่วง 30 วันนี้ คุณได้พัฒนาตัวเองไปมากแล้ว การเรียนรู้อย่างต่อเนื่องเป็นสิ่งที่ทรงพลังที่สุด!</p>

        <div class="info-box">
            <p><strong>รางวัลสุดพิเศษ:</strong></p>
            <p style="margin-top: 10px;">
                ✅ 500 XP โบนัส<br>
                ✅ Badge "Monthly Master" (ทอง)<br>
                ✅ ต้นไม้หายาก 1 ต้น<br>
                ✅ 200 Impact Points<br>
                ✅ ส่วนลด 20% สำหรับคอร์ส Premium
            </p>
        </div>

        <h2>ท้าทายต่อไป: 100 วัน! 🚀</h2>

        <p>คุณมาไกลมากแล้ว ลองท้าทายตัวเองไปยัง 100 วัน เพื่อเข้าสู่ระดับ "Legend" กัน!</p>

    @else
        <div class="achievement-badge" style="background: linear-gradient(135deg, #fae8ff 0%, #f3e8ff 100%);">
            <div class="achievement-icon">👑</div>
            <div class="achievement-name">100 Day Legend!</div>
            <div class="achievement-xp">+ 1,000 XP โบนัส</div>
        </div>

        <h1>ตำนาน {{ $user->full_name }}! 🎊</h1>

        <p>คุณทำได้! เรียนต่อเนื่อง <strong style="font-size: 28px; color: #4a7a5a;">100 วัน</strong> เต็ม! คุณอยู่ใน 1% ของผู้เรียนที่มีความทุ่มเทมากที่สุด</p>

        <p>ความมุ่งมั่นของคุณเป็นแรงบันดาลใจให้กับผู้เรียนคนอื่นๆ นี่คือความสำเร็จที่น่าภาคภูมิใจอย่างแท้จริง!</p>

        <div style="background: linear-gradient(135deg, #fef3c7 0%, #fde68a 100%); border-radius: 12px; padding: 30px; text-align: center; margin: 30px 0;">
            <div style="font-size: 48px; margin-bottom: 10px;">🏅</div>
            <h2 style="margin: 0 0 15px 0; color: #92400e; font-size: 24px;">รางวัลระดับ Legend</h2>
            <div style="background-color: rgba(255,255,255,0.8); border-radius: 8px; padding: 20px; margin-top: 15px;">
                <p style="margin: 0 0 8px 0; color: #78350f; font-weight: 600;">✅ 1,000 XP โบนัส</p>
                <p style="margin: 0 0 8px 0; color: #78350f; font-weight: 600;">✅ Badge "100 Day Legend" (เพชร)</p>
                <p style="margin: 0 0 8px 0; color: #78350f; font-weight: 600;">✅ ต้นไม้เลเจนด์ 2 ต้น</p>
                <p style="margin: 0 0 8px 0; color: #78350f; font-weight: 600;">✅ 500 Impact Points</p>
                <p style="margin: 0 0 8px 0; color: #78350f; font-weight: 600;">✅ ส่วนลด 50% สำหรับคอร์สทั้งหมด 1 เดือน</p>
                <p style="margin: 0; color: #78350f; font-weight: 600;">✅ Premium Features ฟรี 3 เดือน</p>
            </div>
        </div>

        <h2>คุณคือแรงบันดาลใจ! 💫</h2>

        <p>เราอยากขอบคุณที่คุณเป็นส่วนหนึ่งของชุมชน BrieflyLearn ความทุ่มเทของคุณเป็นตัวอย่างที่ดีให้กับผู้เรียนทุกคน</p>

        <p>รักษา streak ต่อไปเพื่อรับโบนัส XP พิเศษทุก 10 วัน!</p>
    @endif

    <div class="button-container">
        <a href="{{ config('app.frontend_url', 'https://brieflylearn.com') }}/dashboard" class="button">
            ดูรางวัลทั้งหมด
        </a>
    </div>

    <div style="background-color: #fef3c7; border-left: 4px solid #f59e0b; padding: 16px 20px; margin: 20px 0; border-radius: 4px;">
        <p style="margin: 0; color: #92400e;"><strong>💡 เคล็ดลับ:</strong></p>
        <p style="margin: 8px 0 0 0; color: #78350f;">
            เรียนแค่ 1 บทเรียนต่อวันก็เพียงพอที่จะรักษา streak! อย่าให้วันหมดไปเปล่าๆ นะ
        </p>
    </div>

    <p style="margin-top: 30px;">
        ภูมิใจในตัวคุณมากๆ!<br>
        <strong>ทีมงาน BrieflyLearn</strong>
    </p>
@endsection
