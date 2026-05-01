@extends('emails.layout')

@section('title', 'คำสั่งซื้อรอการชำระเงิน')

@section('content')
    <h1>ได้รับคำสั่งซื้อของคุณแล้ว</h1>

    <p>สวัสดี {{ $user->full_name }},</p>

    <p>เราได้รับคำสั่งซื้อของคุณและกำลังรอการชำระเงิน กรุณาทำรายการให้เสร็จสิ้นภายใน 30 นาที</p>

    <div class="info-box">
        <p style="margin: 0;"><strong>หมายเลขคำสั่งซื้อ:</strong> #{{ $enrollment->order_no }}</p>
        <p style="margin: 10px 0 0 0;"><strong>คอร์ส:</strong> {{ $course->title }}</p>
        <p style="margin: 10px 0 0 0;"><strong>ยอดที่ต้องชำระ:</strong> {{ number_format((float) $course->price, 2) }} บาท</p>
    </div>

    <p>หากคุณได้ทำรายการแล้ว ระบบจะอัปเดตสถานะภายในไม่กี่นาที และคุณจะได้รับอีเมลยืนยันอีกครั้ง</p>

    <div class="button-container">
        <a href="{{ config('app.frontend_url', 'https://antiparallel.app') }}/courses/{{ $course->id }}" class="button">
            ดูคำสั่งซื้อของคุณ
        </a>
    </div>

    <p style="margin-top: 30px;">
        หากมีคำถาม กรุณาติดต่อทีมซัพพอร์ตได้ตลอดเวลา<br>
        <strong>ทีมงาน Antiparallel</strong>
    </p>
@endsection
