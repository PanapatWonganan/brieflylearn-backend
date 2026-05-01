@extends('emails.layout')

@section('title', 'สรุปประจำสัปดาห์')

@section('content')
    <h1>สรุปความก้าวหน้าประจำสัปดาห์ 📊</h1>

    <p>สวัสดี {{ $user->full_name }}!</p>

    <p>นี่คือสรุปความก้าวหน้าของคุณในสัปดาห์ที่ผ่านมา มาดูกันว่าคุณทำได้ดีแค่ไหน!</p>

    <div style="background: linear-gradient(135deg, #eff6ff 0%, #dbeafe 100%); border-radius: 12px; padding: 30px; margin: 30px 0;">
        <h2 style="margin: 0 0 20px 0; color: #1e3a8a; text-align: center; font-size: 22px;">สถิติของคุณ</h2>

        <table style="width: 100%; border-collapse: collapse;">
            <tr>
                <td style="padding: 15px; text-align: center; border-right: 1px solid #bfdbfe;">
                    <div style="font-size: 36px; font-weight: 700; color: #1e40af; margin-bottom: 5px;">{{ $stats['lessons_completed'] ?? 0 }}</div>
                    <div style="font-size: 14px; color: #3b82f6;">บทเรียนที่เรียนจบ</div>
                </td>
                <td style="padding: 15px; text-align: center;">
                    <div style="font-size: 36px; font-weight: 700; color: #1e40af; margin-bottom: 5px;">{{ $stats['xp_earned'] ?? 0 }}</div>
                    <div style="font-size: 14px; color: #3b82f6;">XP ที่ได้รับ</div>
                </td>
            </tr>
            <tr>
                <td style="padding: 15px; text-align: center; border-right: 1px solid #bfdbfe; border-top: 1px solid #bfdbfe;">
                    <div style="font-size: 36px; font-weight: 700; color: #1e40af; margin-bottom: 5px;">{{ $stats['streak_days'] ?? 0 }}</div>
                    <div style="font-size: 14px; color: #3b82f6;">🔥 Streak (วัน)</div>
                </td>
                <td style="padding: 15px; text-align: center; border-top: 1px solid #bfdbfe;">
                    <div style="font-size: 36px; font-weight: 700; color: #1e40af; margin-bottom: 5px;">{{ $stats['garden_level'] ?? 1 }}</div>
                    <div style="font-size: 14px; color: #3b82f6;">ระดับสวน</div>
                </td>
            </tr>
        </table>
    </div>

    <h2>ความก้าวหน้าของคุณ</h2>

    @php
        $progress = min(100, (($stats['lessons_completed'] ?? 0) / max(1, ($stats['total_lessons'] ?? 10))) * 100);
    @endphp

    <div style="background-color: #f9fafb; padding: 20px; border-radius: 8px; margin: 20px 0;">
        <p style="margin: 0 0 10px 0; color: #4b5563; font-size: 14px; font-weight: 600;">ความคืบหน้าในคอร์สปัจจุบัน</p>
        <div style="width: 100%; height: 24px; background-color: #e5e7eb; border-radius: 12px; overflow: hidden;">
            <div style="width: {{ $progress }}%; height: 100%; background: linear-gradient(90deg, #4a7a5a 0%, #5a8a6a 100%);"></div>
        </div>
        <p style="margin: 10px 0 0 0; color: #6b7280; font-size: 13px; text-align: right;">{{ number_format($progress, 0) }}% สำเร็จ</p>
    </div>

    @if(($stats['lessons_completed'] ?? 0) > 0)
        <div class="achievement-badge">
            <div class="achievement-icon">⭐</div>
            <div class="achievement-name">สุดยอด!</div>
            <div class="achievement-xp">คุณเรียนได้ {{ $stats['lessons_completed'] }} บทเรียนในสัปดาห์นี้</div>
        </div>
    @endif

    <div class="info-box">
        <p><strong>🎯 เป้าหมายสัปดาห์หน้า:</strong></p>
        <p style="margin-top: 10px;">
            • เรียนจบอีก {{ max(5, ($stats['lessons_completed'] ?? 0) + 2) }} บทเรียน<br>
            • เก็บ XP อีก {{ ($stats['xp_earned'] ?? 100) + 200 }} แต้ม<br>
            • รักษา streak ไปต่อเนื่อง {{ ($stats['streak_days'] ?? 0) + 7 }} วัน
        </p>
    </div>

    <div class="button-container">
        <a href="{{ config('app.frontend_url', 'https://antiparallel.app') }}/dashboard" class="button">
            ดูรายละเอียดเพิ่มเติม
        </a>
    </div>

    <p style="margin-top: 30px;">
        เก่งมาก! รักษาฟอร์มนี้ไปต่อนะ<br>
        <strong>ทีมงาน Antiparallel</strong>
    </p>
@endsection
