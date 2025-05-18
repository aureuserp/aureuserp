<?php

namespace Webkul\Account\Mail\Invoice\Actions;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Address;
use Illuminate\Mail\Mailables\Attachment;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class InvoiceEmail extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * Array to store attachments
     */
    protected array $attachmentData = [];

    /**
     * Create a new message instance.
     */
    public function __construct(
        public string $viewTemplate,
        public array $payload
    ) {}

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: $this->payload['subject'],
            from: new Address($this->payload['from']['address'], '"'.addslashes($this->payload['from']['name']).'"'),
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(view: $this->viewTemplate);
    }

    /**
     * Get the attachments for the message.
     *
     * @return array<int, \Illuminate\Mail\Mailables\Attachment>
     */
    public function attachments(): array
    {
        $attachments = [];

        foreach ($this->attachmentData as $attachment) {
            if (isset($attachment['path'])) {
                $attachments[] = Attachment::fromPath($attachment['path'])
                    ->as($attachment['name'] ?? null)
                    ->withMime($attachment['mime'] ?? null);
            } elseif (isset($attachment['data'])) {
                $attachments[] = Attachment::fromData(
                    fn () => $attachment['data'],
                    $attachment['name']
                )->withMime($attachment['mime'] ?? null);
            }
        }

        return $attachments;
    }

    /**
     * Add attachments to the email
     */
    public function withAttachments(array $attachments): static
    {
        $this->attachmentData = $attachments;

        return $this;
    }
}
