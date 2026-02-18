<?php

namespace Webkul\Account\Enums;

use Filament\Support\Contracts\HasLabel;

enum AutoPostBills: string implements HasLabel
{
    case ALWAYS = 'always';

    case ASK = 'ask';

    case NEVER = 'never';

    public function getLabel(): ?string
    {
        return match ($this) {
            self::ALWAYS => __('accounts::enums/auto-post-bills.always'),
            self::ASK    => __('accounts::enums/auto-post-bills.ask'),
            self::NEVER  => __('accounts::enums/auto-post-bills.never'),
        };
    }

    public static function options(): array
    {
        return [
            self::ALWAYS->value => __('accounts::enums/auto-post-bills.always'),
            self::ASK->value    => __('accounts::enums/auto-post-bills.ask'),
            self::NEVER->value  => __('accounts::enums/auto-post-bills.never'),
        ];
    }
}
