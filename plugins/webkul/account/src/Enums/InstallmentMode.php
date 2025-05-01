<?php

namespace Webkul\Account\Enums;

use Filament\Support\Contracts\HasLabel;

enum InstallmentMode: string implements HasLabel
{
    case NEXT = 'next';

    case OVERDUE = 'overdue';

    case BEFORE_DATE = 'before_date';

    case FULL = 'full';

    public function getLabel(): ?string
    {
        return match ($this) {
            self::NEXT        => __('accounts::enums/installment-mode.next'),
            self::OVERDUE     => __('accounts::enums/installment-mode.overdue'),
            self::BEFORE_DATE => __('accounts::enums/installment-mode.before-date'),
            self::FULL        => __('accounts::enums/installment-mode.full'),
        };
    }

    public static function options(): array
    {
        return [
            self::NEXT->value        => __('accounts::enums/installment-mode.next'),
            self::OVERDUE->value     => __('accounts::enums/installment-mode.overdue'),
            self::BEFORE_DATE->value => __('accounts::enums/installment-mode.before-date'),
            self::FULL->value        => __('accounts::enums/installment-mode.full'),
        ];
    }
}
