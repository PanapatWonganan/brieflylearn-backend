@extends('emails.layout')

@section('title', 'ชำระเงินสำเร็จ')

@section('content')
    <h1>ชำระเงินสำเร็จ ยินดีต้อนรับสู่คอร์ส!</h1>

    <p>สวัสดี {{ $user->full_name }},</p>

    <p>เราได้รับการชำระเงินของคุณเรียบร้อยแล้ว ตอนนี้คุณสามารถเข้าเรียนได้ทันที</p>

    <div class="info-box">
        <p style="margin: 0;"><strong>หมายเลขคำสั่งซื้อ:</strong> #{{ $enrollment->order_no }}</p>
        <p style="margin: 10px 0 0 0;"><strong>คอร์ส:</strong> {{ $course->title }}</p>
        <p style="margin: 10px 0 0 0;"><strong>ยอดที่ชำระ:</strong> {{ number_format((float) $enrollment->amount_paid, 2) }} บาท</p>
        @if($enrollment->payment_date)
            <p style="margin: 10px 0 0 0;"><strong>วันที่ชำระ:</strong> {{ $enrollment->payment_date->format('d/m/Y H:i') }}</p>
        @endif
    </div>

    <div class="button-container">
        <a href="{{ config('app.frontend_url', 'https://antiparallel.co') }}/courses/{{ $course->id }}" class="button">
            เริ่มเรียนเลย
        </a>
    </div>

    <p style="margin-top: 30px;">
        ขอให้เรียนรู้อย่างมีความสุข<br>
        <strong>ทีมงาน Antiparallel</strong>
    </p>
@endsection
