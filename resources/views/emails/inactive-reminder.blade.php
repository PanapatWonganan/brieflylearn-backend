@extends('emails.layout')

@section('title', 'คิดถึงคุณนะ')

@section('content')
    @if($daysInactive == 3)
        <h1>คิดถึงนะ {{ $user->full_name }}! 💙</h1>

        <p>เราสังเกตเห็นว่าคุณไม่ได้เข้ามาเรียนมา 3 วันแล้ว อยากให้คุณรู้ว่าเรายังรอคุณอยู่!</p>

        <p>บางครั้งการพักก็เป็นเรื่องดี แต่การกลับมาเรียนต่อเนื่องจะช่วยให้คุณบรรลุเป้าหมายได้เร็วขึ้นนะ</p>

        <div class="info-box">
            <p><strong>สิ่งที่คุณพลาดไปในช่วงนี้:</strong></p>
            <p style="margin-top: 10px;">
                • คอร์สใหม่ที่น่าสนใจ 3 คอร์ส<br>
                • Challenge ประจำวันที่มีรางวัล XP<br>
                • โอกาสในการรักษา streak ของคุณ
            </p>
        </div>

        <div class="button-container">
            <a href="{{ config('app.frontend_url', 'https://antiparallel.co') }}/dashboard" class="button">
                กลับมาเรียนต่อ
            </a>
        </div>

    @elseif($daysInactive == 7)
        <h1>มีอะไรใหม่รอคุณอยู่! 🎉</h1>

        <p>สวัสดี {{ $user->full_name }},</p>

        <p>คุณไม่ได้เข้ามาใน Antiparallel มา 7 วันแล้ว ในช่วงนี้เรามีอะไรใหม่ๆ เยอะเลย!</p>

        <h2>อัพเดทใหม่สำหรับคุณ:</h2>

        <ul>
            <li><strong>🆕 คอร์สใหม่</strong> - "Advanced AI Techniques" เพิ่งเปิดตัว</li>
            <li><strong>🎨 AI Lab อัพเกรด</strong> - เพิ่มเครื่องมือ AI ใหม่ 5 ตัว</li>
            <li><strong>🏆 Event พิเศษ</strong> - Double XP Weekend กำลังจะมาถึง</li>
            <li><strong>👥 ชุมชน</strong> - มีเพื่อนเรียนใหม่ 150+ คนรอคุณอยู่</li>
        </ul>

        <div class="achievement-badge">
            <div class="achievement-icon">🎁</div>
            <div class="achievement-name">Welcome Back Bonus!</div>
            <div class="achievement-xp">กลับมาเรียนวันนี้รับฟรี 100 XP</div>
        </div>

        <div class="button-container">
            <a href="{{ config('app.frontend_url', 'https://antiparallel.co') }}/whats-new" class="button">
                ดูอะไรใหม่
            </a>
        </div>

    @else
        <h1>เราคิดถึงคุณมากๆ {{ $user->full_name }} 💚</h1>

        <p>คุณไม่ได้เข้ามาเรียนมา 1 เดือนเต็มแล้ว เราอยากให้คุณรู้ว่าการเรียนรู้ของคุณยังคงรออยู่!</p>

        <h2>สิ่งที่คุณพลาดไปในเดือนนี้:</h2>

        <div style="background-color: #fef3c7; border-radius: 12px; padding: 25px; margin: 25px 0;">
            <p style="margin: 0 0 15px 0; color: #92400e; font-weight: 600;">📊 สรุป 30 วันที่ผ่านมา:</p>
            <p style="margin: 0 0 8px 0; color: #78350f;">• 15 คอร์สใหม่เพิ่มเข้ามา</p>
            <p style="margin: 0 0 8px 0; color: #78350f;">• ฟีเจอร์ใหม่ 8 ฟีเจอร์</p>
            <p style="margin: 0 0 8px 0; color: #78350f;">• 3 Events พิเศษที่จบไปแล้ว</p>
            <p style="margin: 0; color: #78350f;">• ชุมชนเติบโตขึ้น 40%</p>
        </div>

        <p><strong>อย่ากังวลไป!</strong> ไม่สายเกินไปที่จะกลับมา เราพร้อมต้อนรับคุณเสมอ และความก้าวหน้าเดิมของคุณยังอยู่ครบถ้วน</p>

        <div class="info-box">
            <p><strong>🎁 โปรโมชั่นพิเศษสำหรับคุณ:</strong></p>
            <p style="margin-top: 10px;">
                กลับมาเรียนภายใน 7 วันนี้ รับ:<br>
                • 500 XP โบนัส<br>
                • ส่วนลด 30% สำหรับคอร์ส Premium<br>
                • ไอเทมพิเศษในสวน AI Lab
            </p>
        </div>

        <div class="button-container">
            <a href="{{ config('app.frontend_url', 'https://antiparallel.co') }}/comeback" class="button">
                กลับมาเริ่มใหม่
            </a>
        </div>
    @endif

    <p style="margin-top: 30px;">
        เรารอคุณอยู่นะ!<br>
        <strong>ทีมงาน Antiparallel</strong>
    </p>
@endsection
