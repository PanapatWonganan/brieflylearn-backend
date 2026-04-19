@extends('emails.layout')

@section('title', 'รีเซ็ตรหัสผ่าน Antiparallel')

@section('content')
    <h1>รีเซ็ตรหัสผ่านของคุณ</h1>

    <p>สวัสดี {{ $user->full_name }},</p>

    <p>เราได้รับคำขอให้รีเซ็ตรหัสผ่านสำหรับบัญชี Antiparallel ของคุณ หากคุณเป็นผู้ทำการขอรีเซ็ตรหัสผ่าน กรุณาคลิกที่ปุ่มด้านล่างเพื่อดำเนินการต่อ</p>

    <div class="button-container">
        <a href="{{ $resetUrl }}" class="button">
            รีเซ็ตรหัสผ่าน
        </a>
    </div>

    <div class="info-box">
        <p><strong>หมายเหตุสำคัญ:</strong></p>
        <p style="margin-top: 10px;">
            ลิงก์นี้จะหมดอายุภายใน <strong>60 นาที</strong> เพื่อความปลอดภัยของบัญชีคุณ<br>
            หากคุณไม่ได้ขอรีเซ็ตรหัสผ่าน กรุณาเพิกเฉยอีเมลนี้
        </p>
    </div>

    <p>หากปุ่มด้านบนไม่ทำงาน คุณสามารถคัดลอกและวางลิงก์ด้านล่างนี้ในเบราว์เซอร์ของคุณ:</p>

    <p style="word-break: break-all; font-size: 14px; color: #6b7280;">
        {{ $resetUrl }}
    </p>

    <div class="divider"></div>

    <p style="font-size: 14px; color: #6b7280;">
        <strong>เคล็ดลับด้านความปลอดภัย:</strong><br>
        - ใช้รหัสผ่านที่แข็งแรงและไม่ซ้ำกับบริการอื่น<br>
        - อย่าแชร์รหัสผ่านของคุณกับใคร<br>
        - เปิดใช้งานการยืนยันตัวตนสองชั้นหากมีให้บริการ
    </p>

    <p style="margin-top: 30px;">
        หากคุณต้องการความช่วยเหลือ กรุณาติดต่อทีมสนับสนุนของเรา<br>
        <strong>ทีมงาน Antiparallel</strong>
    </p>
@endsection
