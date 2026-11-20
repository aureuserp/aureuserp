<?php

namespace Webkul\Account\Enums;

use Filament\Support\Contracts\HasColor;
use Filament\Support\Contracts\HasDescription;
use Filament\Support\Contracts\HasLabel;

enum AccountingSetupResult: string implements HasColor, HasDescription, HasLabel
{
    case COMPLETED = 'completed';

    case ALREADY_SET_UP = 'already_set_up';

    case NO_TEMPLATE = 'no_template';

    case INCOMPLETE = 'incomplete';

    public function getLabel(): string
    {
        return match ($this) {
            self::COMPLETED      => __('accounts::enums/accounting-setup-result.completed'),
            self::ALREADY_SET_UP => __('accounts::enums/accounting-setup-result.already-set-up'),
            self::NO_TEMPLATE    => __('accounts::enums/accounting-setup-result.no-template'),
            self::INCOMPLETE     => __('accounts::enums/accounting-setup-result.incomplete'),
        };
    }

    public function getDescription(): string
    {
        return match ($this) {
            self::COMPLETED      => __('accounts::enums/accounting-setup-result.descriptions.completed'),
            self::ALREADY_SET_UP => __('accounts::enums/accounting-setup-result.descriptions.already-set-up'),
            self::NO_TEMPLATE    => __('accounts::enums/accounting-setup-result.descriptions.no-template'),
            self::INCOMPLETE     => __('accounts::enums/accounting-setup-result.descriptions.incomplete'),
        };
    }

    public function getColor(): string
    {
        return match ($this) {
            self::COMPLETED      => 'success',
            self::ALREADY_SET_UP => 'info',
            self::NO_TEMPLATE    => 'danger',
            self::INCOMPLETE     => 'danger',
        };
    }
}
