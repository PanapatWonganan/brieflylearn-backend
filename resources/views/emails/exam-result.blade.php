@extends('emails.layout')

@section('title', 'ผลสอบของคุณ')

@section('content')
    <h1>ผลสอบ: {{ $exam->title }}</h1>

    <p>สวัสดี {{ $user->full_name }},</p>

    <p>ผลการทำแบบทดสอบของคุณออกมาแล้ว นี่คือรายละเอียดผลคะแนน:</p>

    <div style="background: linear-gradient(135deg, {{ $result->is_passed ? '#d1fae5' : '#fee2e2' }} 0%, {{ $result->is_passed ? '#a7f3d0' : '#fecaca' }} 100%); border-radius: 12px; padding: 30px; text-align: center; margin: 30px 0;">
        <div style="font-size: 48px; margin-bottom: 10px;">
            @if($result->is_passed)
                ✅
            @else
                📝
            @endif
        </div>
        <div style="font-size: 32px; font-weight: 700; color: {{ $result->is_passed ? '#065f46' : '#991b1b' }}; margin-bottom: 10px;">
            {{ $result->score }}%
        </div>
        <div style="font-size: 18px; color: {{ $result->is_passed ? '#047857' : '#dc2626' }}; font-weight: 600;">
            @if($result->is_passed)
                🎉 ผ่านการทดสอบ!
            @else
                ไม่ผ่าน - ลองใหม่อีกครั้ง
            @endif
        </div>
    </div>

    <div class="info-box">
        <p><strong>รายละเอียดผลสอบ:</strong></p>
        <p style="margin-top: 10px;">
            📊 คะแนน: <strong>{{ $result->correct_answers }}/{{ $result->total_questions }}</strong> ข้อ<br>
            ⏱️ เวลาที่ใช้: <strong>{{ gmdate('i:s', $result->time_taken ?? 0) }}</strong> นาที<br>
            🎯 คะแนนที่ได้: <strong>{{ $result->score }}%</strong><br>
            ✨ XP ที่ได้รับ: <strong>+{{ $result->xp_earned ?? 0 }}</strong>
        </p>
    </div>

    @if($result->is_passed)
        <h2>ยินดีด้วย! 🎊</h2>
        <p>คุณผ่านการทดสอบแล้ว! พร้อมที่จะก้าวไปสู่บทเรียนถัดไปหรือยัง?</p>

        <div class="button-container">
            <a href="{{ config('app.frontend_url', 'https://antiparallel.co') }}/courses/{{ $exam->course_id ?? '' }}/continue" class="button">
                เรียนต่อ
            </a>
        </div>
    @else
        <h2>อย่าท้อใจ! 💪</h2>
        <p>การทดสอบเป็นส่วนหนึ่งของการเรียนรู้ ลองทบทวนเนื้อหาอีกครั้งและกลับมาทำแบบทดสอบใหม่ได้เสมอ</p>

        <div class="button-container">
            <a href="{{ config('app.frontend_url', 'https://antiparallel.co') }}/courses/{{ $exam->course_id ?? '' }}/review" class="button">
                ทบทวนเนื้อหา
            </a>
        </div>
    @endif

    <p style="margin-top: 30px;">
        ขอให้โชคดี!<br>
        <strong>ทีมงาน Antiparallel</strong>
    </p>
@endsection
