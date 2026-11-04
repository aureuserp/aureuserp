<?php

namespace Webkul\Crm\Services\Mail;

use Throwable;
use Webkul\Crm\Enums\InboundEmailStatus;
use Webkul\Crm\Models\MailServer;

class InboundMailFetcher
{
    public function __construct(protected InboundEmailProcessor $processor) {}

    public function fetch(MailServer $server): array
    {
        $counts = array_fill_keys(array_column(InboundEmailStatus::cases(), 'value'), 0);

        try {
            $this->mailbox($server)->fetchUnread(function (string $raw) use ($server, &$counts): void {
                $status = $this->processor->process($raw, $server)->status;

                $counts[$status->value]++;
            });

            $server->forceFill(['last_fetched_at' => now(), 'last_error' => null])->save();
        } catch (Throwable $exception) {
            $server->forceFill(['last_fetched_at' => now(), 'last_error' => mb_substr($exception->getMessage(), 0, 1000)])->save();

            throw $exception;
        }

        return $counts;
    }

    protected function mailbox(MailServer $server): ImapMailbox
    {
        return new ImapMailbox($server);
    }
}
