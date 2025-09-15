<?php

namespace Webkul\Support\Services;

use Exception;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use Webkul\Support\Mail\DynamicEmail;
use Webkul\Support\Models\EmailLog;
use Webkul\Support\Models\EmailTemplate;

class EmailTemplateService
{
    public function getTemplate(string $templateCode, string $locale = 'en')
    {
        return EmailTemplate::where('code', $templateCode)
            ->where('is_active', true)
            ->firstOrFail();
    }

    public function replaceVariables(string $content, array $variables): string
    {
        return preg_replace_callback('/\{\{(.*?)\}\}/', function ($matches) use ($variables) {
            $key = trim($matches[1]);

            return $variables[$key] ?? $matches[0];
        }, $content);
    }

    public function composeEmail(string $templateCode, array $variables = [], string $locale = 'en')
    {
        $template = $this->getTemplate($templateCode, $locale);

        $composedEmail = [
            'body'    => $this->replaceVariables($template->content, $variables),
            'subject' => $this->replaceVariables($template->subject, $variables),
            'layout'  => $template->layout,
            ...$variables,
        ];

        return $composedEmail;
    }

    public function send(string $templateCode, string $recipientEmail, string $recipientName, array $variables = [], string $locale = 'en', array $attachments = [])
    {
        $emailData = $this->composeEmail($templateCode, $variables, $locale);

        $template = $this->getTemplate($templateCode, $locale);

        try {
            Mail::to($recipientEmail, $recipientName)
                ->send((new DynamicEmail($emailData, Auth::user()))->withAttachments($attachments));

            $this->logEmail($template->id, $recipientEmail, $recipientName, $emailData['subject'], $variables, 'sent');

            return true;
        } catch (Exception $e) {
            $this->logEmail($template->id, $recipientEmail, $recipientName, $emailData['subject'] ?? '', $variables, 'failed', $e->getMessage());

            throw $e;
        }
    }

    protected function logEmail(int $templateId, string $recipientEmail, string $recipientName, string $subject, array $variables, string $status, ?string $errorMessage = null)
    {
        EmailLog::create([
            'email_template_id' => $templateId,
            'recipient_email'   => $recipientEmail,
            'recipient_name'    => $recipientName,
            'subject'           => $subject,
            'variables'         => $variables,
            'status'            => $status,
            'error_message'     => $errorMessage,
            'sent_at'           => now(),
        ]);
    }
}
