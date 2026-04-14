@extends('emails.layout')

@section('title', 'ความท้าทายวันนี้รอคุณอยู่!')

@section('content')
    <h1>ความท้าทายวันนี้พร้อมแล้ว!</h1>

    <p>สวัสดี {{ $user->full_name }},</p>

    <p>ยินดีต้อนรับสู่วันใหม่แห่งการเรียนรู้! เรามีภารกิจพิเศษที่จะช่วยให้คุณพัฒนาทักษะและได้รับรางวัลมากมาย</p>

    <h2>ภารกิจวันนี้</h2>

    @if(isset($challenges) && count($challenges) > 0)
        @foreach($challenges as $challenge)
            <div class="info-box" style="margin: 15px 0;">
                <p style="margin: 0; font-weight: 600; color: #1f2937;">
                    {{ $challenge['icon'] ?? '✓' }} {{ $challenge['title'] }}
                </p>
                <p style="margin: 8px 0 0 0; font-size: 14px; color: #6b7280;">
                    {{ $challenge['description'] }}
                </p>
                @if(isset($challenge['xp_reward']))
                    <p style="margin: 8px 0 0 0; font-size: 14px; font-weight: 600; color: #4a7a5a;">
                        รางวัล: +{{ number_format($challenge['xp_reward']) }} XP
                    </p>
                @endif
            </div>
        @endforeach
    @else
        <div class="info-box">
            <p style="margin: 0;"><strong>เรียนรู้ 1 บทเรียนใหม่</strong></p>
            <p style="margin: 8px 0 0 0; font-size: 14px;">ทำบทเรียนให้เสร็จอย่างน้อย 1 บทวันนี้</p>
            <p style="margin: 8px 0 0 0; font-size: 14px; font-weight: 600; color: #4a7a5a;">รางวัล: +50 XP</p>
        </div>

        <div class="info-box">
            <p style="margin: 0;"><strong>ฝึกฝนให้มากขึ้น</strong></p>
            <p style="margin: 8px 0 0 0; font-size: 14px;">ทำแบบฝึกหัดอย่างน้อย 3 ข้อ</p>
            <p style="margin: 8px 0 0 0; font-size: 14px; font-weight: 600; color: #4a7a5a;">รางวัล: +30 XP</p>
        </div>

        <div class="info-box">
            <p style="margin: 0;"><strong>เรียนรู้อย่างต่อเนื่อง</strong></p>
            <p style="margin: 8px 0 0 0; font-size: 14px;">เข้ามาเรียนติดต่อกัน 7 วัน</p>
            <p style="margin: 8px 0 0 0; font-size: 14px; font-weight: 600; color: #4a7a5a;">รางวัล: +100 XP + รางวัลพิเศษ</p>
        </div>
    @endif

    <div class="button-container">
        <a href="{{ config('app.frontend_url', 'https://brieflylearn.com') }}/challenges" class="button">
            ทำภารกิจวันนี้
        </a>
    </div>

    @if(isset($user->current_streak) && $user->current_streak > 0)
        <div style="background: linear-gradient(135deg, #fef3c7 0%, #fde68a 100%); padding: 20px; border-radius: 12px; text-align: center; margin: 20px 0;">
            <p style="margin: 0; font-size: 18px; font-weight: 600; color: #92400e;">
                🔥 สตรีคปัจจุบันของคุณ: {{ $user->current_streak }} วัน
            </p>
            <p style="margin: 10px 0 0 0; font-size: 14px; color: #78350f;">
                ทำภารกิววันนี้เพื่อรักษาสตรีคของคุณ!
            </p>
        </div>
    @endif

    <h2>ทำไมต้องทำภารกิจประจำวัน?</h2>

    <ul>
        <li><strong>สร้างนิสัยการเรียนรู้</strong> - การเรียนรู้อย่างสม่ำเสมอช่วยให้ความรู้ติดตัวยาวนาน</li>
        <li><strong>ได้รับรางวัลมากมาย</strong> - XP, Star Seeds และรางวัลพิเศษอื่นๆ</li>
        <li><strong>ปลดล็อครางวัล</strong> - ทำภารกิจเพื่อปลดล็อครางวัลที่หายากได้</li>
        <li><strong>แข่งขันกับเพื่อน</strong> - เปรียบเทียบความก้าวหน้ากับผู้เรียนคนอื่นๆ</li>
    </ul>

    <div class="info-box">
        <p><strong>เคล็ดลับ:</strong> ตั้งเวลาเตือนประจำวันเพื่อไม่พลาดโอกาสในการทำภารกิจ แค่ 15-30 นาทีต่อวันก็สามารถสร้างความแตกต่างได้!</p>
    </div>

    <p style="margin-top: 30px;">
        มาเริ่มต้นวันนี้ด้วยกันเลย!<br>
        <strong>ทีมงาน BrieflyLearn</strong>
    </p>
@endsection
