@extends('emails.layout')

@section('title', 'ชำระเงินไม่สำเร็จ')

@section('content')
    <h1>การชำระเงินไม่สำเร็จ</h1>

    <p>สวัสดี {{ $user->full_name }},</p>

    <p>ขออภัย การชำระเงินสำหรับคำสั่งซื้อของคุณไม่สำเร็จ คุณสามารถลองชำระอีกครั้งได้</p>

    <div class="info-box">
        <p style="margin: 0;"><strong>หมายเลขคำสั่งซื้อ:</strong> #{{ $enrollment->order_no }}</p>
        <p style="margin: 10px 0 0 0;"><strong>คอร์ส:</strong> {{ $course->title }}</p>
        <p style="margin: 10px 0 0 0;"><strong>ยอด:</strong> {{ number_format((float) $course->price, 2) }} บาท</p>
        @if(!empty($reason))
            <p style="margin: 10px 0 0 0; color: #9b4d4d;"><strong>เหตุผล:</strong> {{ $reason }}</p>
        @endif
    </div>

    <div class="button-container">
        <a href="{{ config('app.frontend_url', 'https://antiparallel.co') }}/courses/{{ $course->id }}/checkout" class="button">
            ลองชำระอีกครั้ง
        </a>
    </div>

    <p style="margin-top: 30px;">
        หากปัญหายังคงอยู่ กรุณาติดต่อทีมซัพพอร์ต เราพร้อมช่วยเหลือคุณ<br>
        <strong>ทีมงาน Antiparallel</strong>
    </p>
@endsection
