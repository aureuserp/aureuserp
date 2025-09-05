<?php

namespace Webkul\Support\Services;

use Exception;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use Webkul\Support\Models\EmailLog;

class EmailService
{
    public function send(string $view, string $mailClass, array $payload, array $attachments = [])
    {
        try {
            $payload['from'] = [
                'address' => Auth::user()->email,
                'name'    => Auth::user()->name,
            ];

            if (Auth::user()->defaultCompany) {
                $payload['from']['company'] = Auth::user()->defaultCompany->toArray();
            }

            Mail::to($payload['to']['address'], '"'.addslashes($payload['to']['name']).'"')
                ->send((new $mailClass($view, $payload))->withAttachments($attachments));

            $this->logEmail($payload['to']['address'], $payload['to']['name'], $payload['subject'], 'sent');

            return true;
        } catch (Exception $e) {
            $this->logEmail($payload['to']['address'], $payload['to']['name'], $payload['subject'], 'failed', $e->getMessage());

            throw $e;
        }
    }

    protected function logEmail(string $recipientEmail, string $recipientName, string $subject, string $status, ?string $errorMessage = null)
    {
        EmailLog::create([
            'recipient_email' => $recipientEmail,
            'recipient_name'  => $recipientName,
            'subject'         => $subject,
            'status'          => $status,
            'error_message'   => $errorMessage,
            'sent_at'         => now(),
        ]);
    }
}
