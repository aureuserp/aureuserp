<?php

namespace Webkul\Crm\Enums;

use Filament\Support\Contracts\HasLabel;

enum MailServerEncryption: string implements HasLabel
{
    case SSL = 'ssl';

    case TLS = 'tls';

    case NONE = 'none';

    public function getLabel(): string
    {
        return match ($this) {
            self::SSL  => __('crm::enums/mail-server-encryption.ssl'),
            self::TLS  => __('crm::enums/mail-server-encryption.tls'),
            self::NONE => __('crm::enums/mail-server-encryption.none'),
        };
    }

    public function getImapFlags(bool $validateCertificate = true): string
    {
        $flags = match ($this) {
            self::SSL  => '/imap/ssl',
            self::TLS  => '/imap/tls',
            self::NONE => '/imap/notls',
        };

        return $validateCertificate || $this === self::NONE ? $flags : $flags.'/novalidate-cert';
    }
}
