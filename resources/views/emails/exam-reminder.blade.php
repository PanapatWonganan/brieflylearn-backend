@extends('emails.layout')

@section('title', 'แบบทดสอบใหม่รอคุณอยู่')

@section('content')
    <h1>แบบทดสอบใหม่พร้อมแล้ว! 📝</h1>

    <p>สวัสดี {{ $user->full_name }},</p>

    <p>เรามีแบบทดสอบใหม่ที่น่าสนใจมาแนะนำให้คุณ พร้อมที่จะทดสอบความรู้และรับ XP พิเศษกันหรือยัง?</p>

    <h2>แบบทดสอบแนะนำสำหรับคุณ</h2>

    @if($exams && count($exams) > 0)
        @foreach($exams->take(3) as $exam)
            <div style="border: 2px solid #e5e7eb; border-radius: 12px; padding: 20px; margin: 20px 0; background-color: #ffffff;">
                <div style="display: flex; align-items: flex-start; margin-bottom: 15px;">
                    <div style="font-size: 36px; margin-right: 15px;">📋</div>
                    <div style="flex: 1;">
                        <h3 style="margin: 0 0 8px 0; color: #1f2937; font-size: 18px;">{{ $exam->title ?? 'AI Assessment' }}</h3>

                        <div style="margin: 8px 0;">
                            @php
                                $difficulty = $exam->difficulty ?? 'medium';
                                $difficultyColors = [
                                    'easy' => ['bg' => '#d1fae5', 'text' => '#065f46', 'label' => 'ง่าย'],
                                    'medium' => ['bg' => '#fef3c7', 'text' => '#92400e', 'label' => 'ปานกลาง'],
                                    'hard' => ['bg' => '#fee2e2', 'text' => '#991b1b', 'label' => 'ยาก'],
                                ];
                                $color = $difficultyColors[$difficulty] ?? $difficultyColors['medium'];
                            @endphp
                            <span style="background-color: {{ $color['bg'] }}; color: {{ $color['text'] }}; padding: 4px 10px; border-radius: 4px; font-size: 12px; font-weight: 600; margin-right: 8px;">{{ $color['label'] }}</span>
                            <span style="color: #6b7280; font-size: 14px;">
                                {{ $exam->question_count ?? 10 }} ข้อ ·
                                {{ $exam->time_limit ?? 15 }} นาที
                            </span>
                        </div>

                        <p style="margin: 12px 0 0 0; color: #4b5563; font-size: 14px; line-height: 1.5;">
                            {{ $exam->description ?? 'ทดสอบความรู้ด้าน AI และ Machine Learning เพื่อวัดระดับความเข้าใจของคุณ' }}
                        </p>

                        <div style="margin-top: 12px;">
                            <p style="margin: 0; color: #6b7280; font-size: 13px;">
                                🎁 ผ่านการทดสอบรับ: <strong style="color: #4a7a5a;">+{{ $exam->xp_reward ?? 150 }} XP</strong>
                            </p>
                        </div>
                    </div>
                </div>

                <a href="{{ config('app.frontend_url', 'https://brieflylearn.com') }}/exams/{{ $exam->id ?? 'start' }}" style="display: inline-block; padding: 10px 20px; background-color: #4a7a5a; color: #ffffff; text-decoration: none; border-radius: 6px; font-weight: 600; font-size: 14px;">
                    เริ่มทำข้อสอบ →
                </a>
            </div>
        @endforeach
    @else
        <div style="border: 2px solid #e5e7eb; border-radius: 12px; padding: 20px; margin: 20px 0; background-color: #ffffff;">
            <h3 style="margin: 0 0 10px 0; color: #4a7a5a; font-size: 18px;">📋 AI Fundamentals Quiz</h3>
            <p style="margin: 0 0 10px 0; color: #6b7280; font-size: 14px;">
                <span style="background-color: #fef3c7; color: #92400e; padding: 3px 8px; border-radius: 4px; font-size: 12px; font-weight: 600; margin-right: 8px;">ปานกลาง</span>
                15 ข้อ · 20 นาที
            </p>
            <p style="margin: 0 0 15px 0; color: #4b5563; font-size: 15px;">
                ทดสอบความรู้พื้นฐานด้าน AI ครอบคลุมหัวข้อ Machine Learning, Neural Networks และ AI Applications
            </p>
            <a href="{{ config('app.frontend_url', 'https://brieflylearn.com') }}/exams/ai-fundamentals" style="color: #4a7a5a; text-decoration: none; font-weight: 600; font-size: 15px;">เริ่มทำข้อสอบ →</a>
        </div>

        <div style="border: 2px solid #e5e7eb; border-radius: 12px; padding: 20px; margin: 20px 0; background-color: #ffffff;">
            <h3 style="margin: 0 0 10px 0; color: #4a7a5a; font-size: 18px;">📋 Prompt Engineering Challenge</h3>
            <p style="margin: 0 0 10px 0; color: #6b7280; font-size: 14px;">
                <span style="background-color: #d1fae5; color: #065f46; padding: 3px 8px; border-radius: 4px; font-size: 12px; font-weight: 600; margin-right: 8px;">ง่าย</span>
                10 ข้อ · 15 นาที
            </p>
            <p style="margin: 0 0 15px 0; color: #4b5563; font-size: 15px;">
                ทดสอบทักษะการเขียน Prompt ที่มีประสิทธิภาพสำหรับ ChatGPT และ AI Tools อื่นๆ
            </p>
            <a href="{{ config('app.frontend_url', 'https://brieflylearn.com') }}/exams/prompt-engineering" style="color: #4a7a5a; text-decoration: none; font-weight: 600; font-size: 15px;">เริ่มทำข้อสอบ →</a>
        </div>
    @endif

    <div class="info-box">
        <p><strong>💡 ทำไมต้องทำแบบทดสอบ?</strong></p>
        <p style="margin-top: 10px;">
            ✅ วัดความก้าวหน้าในการเรียนรู้<br>
            ✅ ได้รับ XP และ Impact Points<br>
            ✅ ปลดล็อกคอร์สและฟีเจอร์ใหม่<br>
            ✅ รับ Achievement Badge
        </p>
    </div>

    <div class="button-container">
        <a href="{{ config('app.frontend_url', 'https://brieflylearn.com') }}/exams" class="button">
            ดูแบบทดสอบทั้งหมด
        </a>
    </div>

    <div style="background-color: #fef3c7; border-left: 4px solid #f59e0b; padding: 16px 20px; margin: 20px 0; border-radius: 4px;">
        <p style="margin: 0; color: #92400e;"><strong>🎁 โปรโมชั่นพิเศษ:</strong></p>
        <p style="margin: 8px 0 0 0; color: #78350f;">
            ทำแบบทดสอบ 3 ข้อสอบในสัปดาห์นี้ รับโบนัส 300 XP พิเศษ!
        </p>
    </div>

    <p style="margin-top: 30px;">
        ขอให้โชคดีกับการทดสอบ!<br>
        <strong>ทีมงาน BrieflyLearn</strong>
    </p>
@endsection
