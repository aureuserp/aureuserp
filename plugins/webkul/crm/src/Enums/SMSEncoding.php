<?php

namespace Webkul\Crm\Enums;

enum SMSEncoding: string
{
    case GSM7 = 'GSM7';

    case UCS2 = 'UCS2';

    public function getSinglePartLength(): int
    {
        return match ($this) {
            self::GSM7 => 160,
            self::UCS2 => 70,
        };
    }

    public function getMultiPartLength(): int
    {
        return match ($this) {
            self::GSM7 => 153,
            self::UCS2 => 67,
        };
    }

    public function isUnicode(): bool
    {
        return $this === self::UCS2;
    }
}
