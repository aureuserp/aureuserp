<?php

namespace Webkul\Website\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Address;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class ContactFormMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(public array $data) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            from: new Address($this->data['email'], $this->data['name']),
            replyTo: [new Address($this->data['email'], $this->data['name'])],
            subject: __('website::filament/customer/contact-us.mail.subject', ['subject' => $this->data['subject']]),
        );
    }

    public function content(): Content
    {
        return new Content(view: 'website::mail.contact-form');
    }
}
