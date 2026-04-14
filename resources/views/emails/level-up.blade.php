@extends('emails.layout')

@section('title', 'Level Up!')

@section('content')
    <div class="achievement-badge">
        <div class="achievement-icon">🌟</div>
        <div class="achievement-name">Level Up!</div>
        <div class="achievement-xp">Level {{ $newLevel }}</div>
    </div>

    <h1>ยินดีด้วย {{ $user->full_name }}!</h1>

    <p>คุณได้เลื่อนระดับเป็น <strong style="font-size: 20px; color: #4a7a5a;">Level {{ $newLevel }}</strong> แล้ว! 🎉</p>

    <p>การขึ้นเลเวลแต่ละครั้งแสดงถึงความมุ่งมั่นและความก้าวหน้าในการเรียนรู้ของคุณ ทุกความพยายามของคุณคุ้มค่า!</p>

    <div class="info-box">
        <p><strong>สิ่งที่ปลดล็อกในเลเวลนี้:</strong></p>
        <p style="margin-top: 10px;">
            @if($newLevel >= 5)
            🌱 ปลดล็อก AI Lab - ปลูกต้นไม้และทดลอง AI<br>
            @endif
            @if($newLevel >= 10)
            🎯 คอร์สระดับ Advanced<br>
            @endif
            @if($newLevel >= 15)
            🏆 ห้อง Leaderboard - แข่งขันกับผู้เรียนคนอื่น<br>
            @endif
            @if($newLevel >= 20)
            👥 Community Features - เข้าร่วมกลุ่มเรียนรู้<br>
            @endif
            ✨ รางวัลพิเศษในสวน AI Lab<br>
            🎁 ไอเทมและธีมใหม่สำหรับโปรไฟล์
        </p>
    </div>

    <h2>ความก้าวหน้าของคุณ</h2>

    <p>คุณกำลังเดินหน้าไปสู่เป้าหมายการเรียนรู้ ระดับถัดไปคือ Level {{ $newLevel + 1 }} เก็บ XP อีก {{ ($newLevel + 1) * 1000 - ($user->total_xp ?? 0) }} แต้มเพื่อไปให้ถึง!</p>

    <div class="button-container">
        <a href="{{ config('app.frontend_url', 'https://brieflylearn.com') }}/garden" class="button">
            ดูสวนและรางวัลของคุณ
        </a>
    </div>

    <p style="margin-top: 30px;">
        เรียนต่อไป คุณทำได้ดีมาก!<br>
        <strong>ทีมงาน BrieflyLearn</strong>
    </p>
@endsection
