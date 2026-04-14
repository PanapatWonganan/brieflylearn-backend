<?php

namespace App\Mail;

use App\Models\EmailSequenceStep;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class DripSequenceMail extends Mailable
{
    use Queueable, SerializesModels;

    public string $renderedBody;

    public function __construct(
        public $user,
        public EmailSequenceStep $step,
        public string $unsubscribeUrl,
    ) {
        $this->renderedBody = $this->processPlaceholders($step->body_html);
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: $this->processPlaceholders($this->step->subject),
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.drip-sequence',
        );
    }

    /**
     * Replace placeholders in body/subject with user data.
     *
     * Supported: {name}, {first_name}, {email}, {app_url}
     */
    private function processPlaceholders(string $text): string
    {
        $firstName = explode(' ', $this->user->full_name ?? '')[0];

        return str_replace(
            ['{name}', '{first_name}', '{email}', '{app_url}'],
            [
                $this->user->full_name ?? '',
                $firstName,
                $this->user->email ?? '',
                config('app.frontend_url', 'https://brieflylearn.com'),
            ],
            $text
        );
    }

    public function attachments(): array
    {
        return [];
    }
}
