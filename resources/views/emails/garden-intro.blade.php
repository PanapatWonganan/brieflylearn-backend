@extends('emails.layout')

@section('title', 'รู้จักกับ AI Lab')

@section('content')
    <h1>ยินดีต้อนรับสู่ AI Lab 🌱</h1>

    <p>สวัสดี {{ $user->full_name }}!</p>

    <p>คุณพร้อมที่จะสำรวจ <strong>AI Lab</strong> - ห้องทดลองส่วนตัวของคุณแล้วหรือยัง? นี่คือที่ที่คุณจะได้ลองใช้เครื่องมือ AI และดูความก้าวหน้าของตัวเองผ่านสวนดิจิทัล!</p>

    <div style="background: linear-gradient(135deg, #ecfdf5 0%, #d1fae5 100%); border-radius: 12px; padding: 30px; text-align: center; margin: 30px 0;">
        <div style="font-size: 64px; margin-bottom: 15px;">🌳</div>
        <h2 style="margin: 0 0 10px 0; color: #065f46; font-size: 24px;">สวน AI ของคุณ</h2>
        <p style="margin: 0; color: #047857; font-size: 16px;">ปลูกต้นไม้ เก็บ Impact Points และดูสวนของคุณเติบโต</p>
    </div>

    <h2>AI Lab คืออะไร?</h2>

    <p>AI Lab เป็นพื้นที่พิเศษที่คุณสามารถ:</p>

    <ul>
        <li><strong>🌱 ปลูกต้นไม้</strong> - ทุกครั้งที่คุณเรียนจบบทเรียน คุณจะได้เมล็ดพันธุ์เพื่อปลูกในสวน</li>
        <li><strong>💎 เก็บ Impact Points</strong> - ทำภารกิจและรับ IP เพื่อปลดล็อกต้นไม้พิเศษ</li>
        <li><strong>🔬 ทดลอง AI Tools</strong> - เล่นกับเครื่องมือ AI ต่างๆ แบบฟรี</li>
        <li><strong>🏆 แข่งขันกับเพื่อน</strong> - เปรียบเทียบสวนและความก้าวหน้ากับผู้เรียนคนอื่น</li>
    </ul>

    <h2>ประเภทของต้นไม้</h2>

    <div class="info-box">
        <p>🌿 <strong>ต้นไม้ธรรมดา</strong> - ได้จากการเรียนบทเรียนทั่วไป<br></p>
        <p style="margin-top: 8px;">🌺 <strong>ต้นไม้พิเศษ</strong> - ได้จากการทำแบบทดสอบผ่าน<br></p>
        <p style="margin-top: 8px;">🌟 <strong>ต้นไม้หายาก</strong> - ได้จากการทำ Milestone Achievement<br></p>
        <p style="margin-top: 8px;">✨ <strong>ต้นไม้เลเจนด์</strong> - ได้จากการสะสม IP และปลดล็อก</p>
    </div>

    <div class="button-container">
        <a href="{{ config('app.frontend_url', 'https://antiparallel.app') }}/garden" class="button">
            เข้าสู่ AI Lab
        </a>
    </div>

    <div style="background-color: #fef3c7; border-left: 4px solid #f59e0b; padding: 16px 20px; margin: 20px 0; border-radius: 4px;">
        <p style="margin: 0; color: #92400e;"><strong>💡 เคล็ดลับ:</strong></p>
        <p style="margin: 8px 0 0 0; color: #78350f;">
            ปลูกต้นไม้ทุกวันเพื่อรักษา streak และรับโบนัส IP พิเศษ!
        </p>
    </div>

    <p style="margin-top: 30px;">
        มาเริ่มต้นสร้างสวนของคุณกันเลย!<br>
        <strong>ทีมงาน Antiparallel</strong>
    </p>
@endsection
