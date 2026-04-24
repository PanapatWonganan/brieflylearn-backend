@extends('emails.layout')

@php
    $isPlaybook = ($course->content_type ?? 'video') === 'playbook';
    $frontendUrl = config('app.frontend_url', 'https://antiparallel.co');
    $contentUrl = $isPlaybook
        ? $frontendUrl . '/playbooks/' . $course->id
        : $frontendUrl . '/courses/' . $course->id;
    $headline = $isPlaybook ? 'ชำระเงินสำเร็จ — Playbook พร้อมให้อ่านแล้ว!' : 'ชำระเงินสำเร็จ ยินดีต้อนรับสู่คอร์ส!';
    $intro = $isPlaybook
        ? 'เราได้รับการชำระเงินของคุณเรียบร้อยแล้ว ตอนนี้คุณสามารถเปิดอ่าน Playbook ได้ทันทีแบบไม่จำกัดครั้ง'
        : 'เราได้รับการชำระเงินของคุณเรียบร้อยแล้ว ตอนนี้คุณสามารถเข้าเรียนได้ทันที';
    $itemLabel = $isPlaybook ? 'Playbook' : 'คอร์ส';
    $ctaLabel = $isPlaybook ? 'เปิดอ่าน Playbook' : 'เริ่มเรียนเลย';
    $outro = $isPlaybook ? 'ขอให้ได้ประโยชน์เต็มที่จาก Playbook เล่มนี้' : 'ขอให้เรียนรู้อย่างมีความสุข';
@endphp

@section('title', 'ชำระเงินสำเร็จ')

@section('content')
    <h1>{{ $headline }}</h1>

    <p>สวัสดี {{ $user->full_name }},</p>

    <p>{{ $intro }}</p>

    <div class="info-box">
        <p style="margin: 0;"><strong>หมายเลขคำสั่งซื้อ:</strong> #{{ $enrollment->order_no }}</p>
        <p style="margin: 10px 0 0 0;"><strong>{{ $itemLabel }}:</strong> {{ $course->title }}</p>
        <p style="margin: 10px 0 0 0;"><strong>ยอดที่ชำระ:</strong> {{ number_format((float) $enrollment->amount_paid, 2) }} บาท</p>
        @if($enrollment->payment_date)
            <p style="margin: 10px 0 0 0;"><strong>วันที่ชำระ:</strong> {{ $enrollment->payment_date->format('d/m/Y H:i') }}</p>
        @endif
    </div>

    <div class="button-container">
        <a href="{{ $contentUrl }}" class="button">
            {{ $ctaLabel }}
        </a>
    </div>

    <p style="margin-top: 30px;">
        {{ $outro }}<br>
        <strong>ทีมงาน Antiparallel</strong>
    </p>
@endsection
