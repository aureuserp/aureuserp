<?php

namespace Webkul\Account\Enums;

use Filament\Support\Contracts\HasLabel;

enum DelayType: string implements HasLabel
{
    case DAYS_AFTER = 'days_after';

    case DAYS_AFTER_END_OF_MONTH = 'days_after_end_of_month';

    case DAYS_AFTER_END_OF_NEXT_MONTH = 'days_after_end_of_next_month';

    case DAYS_END_OF_MONTH_NO_THE = 'days_end_of_month_no_the';

    public function getLabel(): ?string
    {
        return match ($this) {
            self::DAYS_AFTER                   => __('accounts::enums/delay-type.days-after'),
            self::DAYS_AFTER_END_OF_MONTH      => __('accounts::enums/delay-type.days-after-end-of-month'),
            self::DAYS_AFTER_END_OF_NEXT_MONTH => __('accounts::enums/delay-type.days-after-end-of-next-month'),
            self::DAYS_END_OF_MONTH_NO_THE     => __('accounts::enums/delay-type.days-end-of-month-no-the'),
        };
    }

    public static function options(): array
    {
        return [
            self::DAYS_AFTER->value                   => __('accounts::enums/delay-type.days-after'),
            self::DAYS_AFTER_END_OF_MONTH->value      => __('accounts::enums/delay-type.days-after-end-of-month'),
            self::DAYS_AFTER_END_OF_NEXT_MONTH->value => __('accounts::enums/delay-type.days-after-end-of-next-month'),
            self::DAYS_END_OF_MONTH_NO_THE->value     => __('accounts::enums/delay-type.days-end-of-month-no-the'),
        ];
    }
}
