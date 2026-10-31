<?php

namespace Webkul\Crm\Mail;

use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Address;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;

class LeadMessageMail extends Mailable
{
    public function __construct(
        public string $mailSubject,
        public string $body,
        public ?string $senderName = null,
        public ?string $replyToAddress = null,
    ) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: $this->mailSubject,
            replyTo: filled($this->replyToAddress) ? [new Address($this->replyToAddress)] : [],
        );
    }

    public function content(): Content
    {
        return new Content(view: 'crm::mail.lead-message');
    }
}
