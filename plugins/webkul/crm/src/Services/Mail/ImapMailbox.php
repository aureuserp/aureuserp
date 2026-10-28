<?php

namespace Webkul\Crm\Services\Mail;

use RuntimeException;
use Throwable;
use Webkul\Crm\Enums\MailServerEncryption;
use Webkul\Crm\Models\MailServer;

class ImapMailbox
{
    public const BATCH = 50;

    public function __construct(protected MailServer $server) {}

    public static function isAvailable(): bool
    {
        return function_exists('imap_open');
    }

    public function test(): int
    {
        $connection = $this->open();

        try {
            return (int) imap_num_msg($connection);
        } finally {
            $this->close($connection);
        }
    }

    public function fetchUnread(callable $handle, int $limit = self::BATCH): int
    {
        $connection = $this->open();

        try {
            $uids = imap_search($connection, 'UNSEEN', SE_UID) ?: [];

            $count = 0;

            foreach (array_slice($uids, 0, $limit) as $uid) {
                $raw = imap_fetchbody($connection, $uid, '', FT_UID | FT_PEEK);

                try {
                    if (is_string($raw)
                        && $raw !== '') {
                        $handle($raw);

                        $count++;
                    }
                } finally {
                    imap_setflag_full($connection, (string) $uid, '\\Seen', ST_UID);
                }
            }

            return $count;
        } finally {
            $this->close($connection);
        }
    }

    public function mailboxReference(): string
    {
        $flags = ($this->server->encryption ?? MailServerEncryption::NONE)->getImapFlags((bool) $this->server->validate_certificate);

        $folder = filled($this->server->folder) ? $this->server->folder : 'INBOX';

        return '{'.$this->server->host.':'.$this->server->port.$flags.'}'.mb_convert_encoding($folder, 'UTF7-IMAP', 'UTF-8');
    }

    protected function open(): mixed
    {
        if (! static::isAvailable()) {
            throw new RuntimeException(__('crm::filament/mail.errors.no-imap'));
        }

        $connection = @imap_open($this->mailboxReference(), (string) $this->server->username, (string) $this->server->password, 0, 1, ['DISABLE_AUTHENTICATOR' => 'GSSAPI']);

        $errors = imap_errors() ?: [];
        imap_alerts();

        if ($connection === false) {
            throw new RuntimeException(imap_last_error() ?: (end($errors) ?: __('crm::filament/mail.errors.connection')));
        }

        return $connection;
    }

    protected function close(mixed $connection): void
    {
        try {
            imap_close($connection);
        } catch (Throwable) {
        }

        imap_errors();
        imap_alerts();
    }
}
