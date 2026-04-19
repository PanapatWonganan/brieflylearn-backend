@extends('emails.layout')

@section('title', 'ยินดีด้วย! คุณเรียนจบคอร์ส')

@section('content')
    <div class="achievement-badge">
        <div class="achievement-icon">🎓</div>
        <div class="achievement-name">เรียนจบคอร์ส!</div>
        <div class="achievement-xp">+ {{ $course->completion_xp ?? 500 }} XP</div>
    </div>

    <h1>ยินดีด้วย {{ $user->full_name }}!</h1>

    <p>คุณได้เรียนจบคอร์ส <strong>{{ $course->title }}</strong> เรียบร้อยแล้ว เราภูมิใจในความมุ่งมั่นและความพยายามของคุณ!</p>

    <div class="info-box">
        <p><strong>สิ่งที่คุณได้รับ:</strong></p>
        <p style="margin-top: 10px;">
            ✅ ใบประกาศนียบัตรดิจิทัล<br>
            ✅ {{ $course->completion_xp ?? 500 }} XP เพิ่มเข้าบัญชีของคุณ<br>
            ✅ ปลดล็อกคอร์สระดับสูงขึ้น<br>
            ✅ Achievement Badge ใหม่
        </p>
    </div>

    <h2>ก้าวต่อไปของคุณ</h2>

    <p>อย่าหยุดแค่นี้! เรามีคอร์สแนะนำที่จะช่วยให้คุณพัฒนาต่อยอดทักษะที่เพิ่งได้เรียนรู้:</p>

    <ul>
        <li>คอร์ส AI ระดับกลาง - เพื่อเจาะลึกเทคนิคขั้นสูง</li>
        <li>Machine Learning Fundamentals - ก้าวสู่โลกของ ML</li>
        <li>AI in Practice - นำความรู้ไปใช้จริง</li>
    </ul>

    <div class="button-container">
        <a href="{{ config('app.frontend_url', 'https://antiparallel.co') }}/courses/recommended" class="button">
            ดูคอร์สแนะนำ
        </a>
    </div>

    <p style="margin-top: 30px;">
        ขอแสดงความยินดี!<br>
        <strong>ทีมงาน Antiparallel</strong>
    </p>
@endsection
