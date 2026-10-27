<?php

namespace Webkul\Crm\Services\Mail;

use Illuminate\Contracts\Mail\Mailer;
use Illuminate\Mail\Mailable;
use Illuminate\Support\Facades\Mail;
use Webkul\Crm\Settings\OutgoingMailSettings;

class OutgoingMailer
{
    public function __construct(protected OutgoingMailSettings $settings) {}

    public function usesCustomServer(): bool
    {
        return $this->settings->is_active && filled($this->settings->host);
    }

    public function mailer(): Mailer
    {
        if (! $this->usesCustomServer()) {
            return Mail::mailer();
        }

        return Mail::build([
            'transport' => 'smtp',
            'scheme'    => $this->settings->encryption === 'ssl' ? 'smtps' : 'smtp',
            'host'      => $this->settings->host,
            'port'      => $this->settings->port,
            'username'  => $this->settings->username,
            'password'  => $this->settings->password,
            'timeout'   => 30,
        ]);
    }

    public function send(string $address, ?string $name, Mailable $mailable): void
    {
        if ($this->usesCustomServer() && filled($this->settings->from_address)) {
            $mailable->from($this->settings->from_address, $this->settings->from_name);
        }

        $this->mailer()->to($address, $name)->send($mailable);
    }
}
