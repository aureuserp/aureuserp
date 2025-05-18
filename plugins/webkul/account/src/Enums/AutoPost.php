<?php

namespace Webkul\Account\Enums;

use Filament\Support\Contracts\HasLabel;

enum AutoPost: string implements HasLabel
{
    case NO = 'no';

    case AT_DATE = 'at_date';

    case MONTHLY = 'monthly';

    case QUARTERLY = 'quarterly';

    case YEARLY = 'yearly';

    public function getLabel(): ?string
    {
        return match ($this) {
            self::NO        => __('accounts::enums/auto-post.no'),
            self::AT_DATE   => __('accounts::enums/auto-post.at-date'),
            self::MONTHLY   => __('accounts::enums/auto-post.monthly'),
            self::QUARTERLY => __('accounts::enums/auto-post.quarterly'),
            self::YEARLY    => __('accounts::enums/auto-post.yearly'),
        };
    }

    public static function options(): array
    {
        return [
            self::NO->value        => __('accounts::enums/auto-post.no'),
            self::AT_DATE->value   => __('accounts::enums/auto-post.at-date'),
            self::MONTHLY->value   => __('accounts::enums/auto-post.monthly'),
            self::QUARTERLY->value => __('accounts::enums/auto-post.quarterly'),
            self::YEARLY->value    => __('accounts::enums/auto-post.yearly'),
        ];
    }
}
