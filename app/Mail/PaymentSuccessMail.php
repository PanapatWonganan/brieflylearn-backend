<?php

namespace App\Mail;

use App\Models\Enrollment;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class PaymentSuccessMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(public Enrollment $enrollment)
    {
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'ชำระเงินสำเร็จ - คำสั่งซื้อ #' . $this->enrollment->order_no,
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.payment-success',
            with: [
                'enrollment' => $this->enrollment,
                'course' => $this->enrollment->course,
                'user' => $this->enrollment->user,
            ],
        );
    }

    public function attachments(): array
    {
        return [];
    }
}
