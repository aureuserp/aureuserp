<?php

namespace Webkul\Invoice\Enums;

use Filament\Support\Contracts\HasLabel;

enum AutoPostBills: string implements HasLabel
{
    case ALWAYS = 'always';

    case ASK = 'ask';

    case NEVER = 'never';

    public function getLabel(): ?string
    {
        return match ($this) {
            self::ALWAYS => __('invoices::enums/auto-post-bills.always'),
            self::ASK => __('invoices::enums/auto-post-bills.ask'),
            self::NEVER => __('invoices::enums/auto-post-bills.never'),
        };
    }

    public static function options(): array
    {
        return [
            self::ALWAYS->value => __('invoices::enums/auto-post-bills.always'),
            self::ASK->value => __('invoices::enums/auto-post-bills.ask'),
            self::NEVER->value => __('invoices::enums/auto-post-bills.never'),
        ];
    }
}
