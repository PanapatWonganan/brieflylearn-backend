@extends('emails.layout')

@section('title', 'คุณได้รับรางวัลใหม่!')

@section('content')
    <h1>ยินดีด้วย! คุณได้รับรางวัลใหม่</h1>

    <p>สวัสดี {{ $user->full_name }},</p>

    <p>ความพยายามของคุณได้รับผลแล้ว! คุณเพิ่งปลดล็อครางวัลใหม่</p>

    <div class="achievement-badge">
        <div class="achievement-icon">{{ $achievement->badge_icon ?? '🏆' }}</div>
        <h2 class="achievement-name">{{ $achievement->name }}</h2>
        <p style="color: #78350f; margin: 10px 0; font-size: 15px;">{{ $achievement->description }}</p>

        <div style="margin-top: 20px; padding-top: 15px; border-top: 2px dashed rgba(146, 64, 14, 0.3);">
            @if(isset($achievement->xp_reward) && $achievement->xp_reward > 0)
                <p class="achievement-xp" style="margin: 5px 0;">
                    + {{ number_format($achievement->xp_reward) }} XP
                </p>
            @endif
            @if(isset($achievement->star_seeds_reward) && $achievement->star_seeds_reward > 0)
                <p class="achievement-xp" style="margin: 5px 0;">
                    + {{ number_format($achievement->star_seeds_reward) }} Star Seeds
                </p>
            @endif
        </div>
    </div>

    @if(isset($achievement->rarity))
    <div class="info-box">
        <p><strong>ความหายาก:</strong>
            @if($achievement->rarity === 'common')
                ธรรมดา
            @elseif($achievement->rarity === 'rare')
                หายาก
            @elseif($achievement->rarity === 'epic')
                หายากมาก
            @elseif($achievement->rarity === 'legendary')
                ตำนาน
            @else
                {{ $achievement->rarity }}
            @endif
        </p>
    </div>
    @endif

    <h2>ทำไมรางวัลนี้จึงมีความหมาย?</h2>

    <p>รางวัลแต่ละอันแสดงถึงความก้าวหน้าและความมุ่งมั่นของคุณในการเรียนรู้ มันไม่ใช่แค่ตัวเลข แต่เป็นหลักฐานของการพัฒนาตนเองอย่างต่อเนื่อง</p>

    <div class="button-container">
        <a href="{{ config('app.frontend_url', 'https://antiparallel.app') }}/achievements" class="button">
            ดูรางวัลทั้งหมด
        </a>
    </div>

    <div class="info-box">
        <p><strong>เคล็ดลับ:</strong> ยังมีรางวัลอื่นๆ อีกมากมายรอคุณอยู่! ลองสำรวจดูว่ามีรางวัลอะไรบ้างที่คุณสามารถปลดล็อคได้</p>
    </div>

    <p style="margin-top: 30px;">
        ทำต่อไปเลย! คุณกำลังทำได้ดีมาก<br>
        <strong>ทีมงาน Antiparallel</strong>
    </p>
@endsection
