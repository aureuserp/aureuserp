<?php

namespace Webkul\Account\Enums;

use Filament\Support\Contracts\HasLabel;

enum JournalType: string implements HasLabel
{
    case SALE = 'sale';
    case PURCHASE = 'purchase';
    case CASH = 'cash';
    case BANK = 'bank';
    case CREDIT_CARD = 'credit';
    case GENERAL = 'general';

    public function getLabel(): ?string
    {
        return match ($this) {
            self::SALE        => __('accounts::enums/journal-type.sale'),
            self::PURCHASE    => __('accounts::enums/journal-type.purchase'),
            self::CASH        => __('accounts::enums/journal-type.cash'),
            self::BANK        => __('accounts::enums/journal-type.bank'),
            self::CREDIT_CARD => __('accounts::enums/journal-type.credit'),
            self::GENERAL     => __('accounts::enums/journal-type.general'),
        };
    }

    public static function options(): array
    {
        return [
            self::SALE->value        => __('accounts::enums/journal-type.sale'),
            self::PURCHASE->value    => __('accounts::enums/journal-type.purchase'),
            self::CASH->value        => __('accounts::enums/journal-type.cash'),
            self::BANK->value        => __('accounts::enums/journal-type.bank'),
            self::CREDIT_CARD->value => __('accounts::enums/journal-type.credit'),
            self::GENERAL->value     => __('accounts::enums/journal-type.general'),
        ];
    }
}
