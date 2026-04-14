@extends('emails.layout')

@section('title', $step->subject)

@section('content')
    {{--
        Storytelling-friendly template (Sean D'Souza style)

        Content is stored as HTML in the database (body_html).
        Admin writes the story in Filament, rendered here with
        minimal wrapper — letting the narrative breathe.

        Available variables in body_html via simple placeholders:
        {name}  → user's full name or first name
        {email} → user's email
        {app_url} → frontend URL
    --}}

    {!! $renderedBody !!}

    <div style="margin-top: 40px; padding-top: 20px; border-top: 1px solid #e5e7eb;">
        <p style="font-size: 13px; color: #9ca3af; text-align: center;">
            ไม่อยากได้รับอีเมลซีรีส์นี้อีก?
            <a href="{{ $unsubscribeUrl }}" style="color: #4a7a5a;">ยกเลิกการรับอีเมล</a>
        </p>
    </div>
@endsection
