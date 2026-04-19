@extends('emails.layout')

@section('title', 'ลงทะเบียนคอร์สสำเร็จ')

@section('content')
    <h1>คุณลงทะเบียนคอร์สสำเร็จแล้ว!</h1>

    <p>สวัสดี {{ $user->full_name }},</p>

    <p>ยินดีด้วย! คุณได้ลงทะเบียนเรียนคอร์สใหม่เรียบร้อยแล้ว</p>

    <div class="info-box">
        <p style="margin: 0;"><strong>คอร์ส:</strong> {{ $course->title }}</p>
        @if(isset($course->description))
            <p style="margin: 10px 0 0 0; font-size: 14px;">{{ $course->description }}</p>
        @endif
    </div>

    <h2>รายละเอียดคอร์ส</h2>

    <table style="width: 100%; margin: 20px 0;">
        <tr>
            <td style="padding: 8px 0; color: #6b7280; width: 40%;">ระดับความยาก:</td>
            <td style="padding: 8px 0; font-weight: 500;">
                @if($course->level === 'beginner')
                    เริ่มต้น
                @elseif($course->level === 'intermediate')
                    ปานกลาง
                @elseif($course->level === 'advanced')
                    ขั้นสูง
                @else
                    {{ $course->level }}
                @endif
            </td>
        </tr>
        @if(isset($course->total_lessons))
        <tr>
            <td style="padding: 8px 0; color: #6b7280;">จำนวนบทเรียน:</td>
            <td style="padding: 8px 0; font-weight: 500;">{{ $course->total_lessons }} บทเรียน</td>
        </tr>
        @endif
        @if(isset($course->instructor))
        <tr>
            <td style="padding: 8px 0; color: #6b7280;">ผู้สอน:</td>
            <td style="padding: 8px 0; font-weight: 500;">{{ $course->instructor }}</td>
        </tr>
        @endif
    </table>

    <div class="button-container">
        <a href="{{ config('app.frontend_url', 'https://antiparallel.co') }}/courses/{{ $course->id }}" class="button">
            เข้าเรียนคอร์ส
        </a>
    </div>

    <div class="info-box">
        <p><strong>ข้อแนะนำในการเรียน:</strong></p>
        <p style="margin-top: 10px;">
            - เรียนอย่างสม่ำเสมอเพื่อผลลัพธ์ที่ดีที่สุด<br>
            - ทำแบบฝึกหัดและงานที่มอบหมายทุกบท<br>
            - อย่าลืมจดบันทึกสิ่งที่สำคัญ<br>
            - หากมีข้อสงสัย สามารถถามผู้สอนได้ตลอดเวลา
        </p>
    </div>

    <p style="margin-top: 30px;">
        ขอให้เรียนรู้อย่างมีความสุขและประสบความสำเร็จในคอร์สนี้!<br>
        <strong>ทีมงาน Antiparallel</strong>
    </p>
@endsection
