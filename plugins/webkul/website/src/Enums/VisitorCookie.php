<?php

namespace Webkul\Website\Enums;

enum VisitorCookie: string
{
    case VISITOR = 'website_visitor';

    case TIMEZONE = 'website_tz';

    public static function names(): array
    {
        return array_map(fn (self $cookie): string => $cookie->value, self::cases());
    }
}
