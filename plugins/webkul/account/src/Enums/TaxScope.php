<?php

namespace Webkul\Account\Enums;

use Filament\Support\Contracts\HasLabel;

enum TaxScope: string implements HasLabel
{
    case SERVICE = 'service';

    case CONSU = 'consu';

    public function getLabel(): ?string
    {
        return match ($this) {
            self::SERVICE => __('accounts::enums/tax-scope.service'),
            self::CONSU   => __('accounts::enums/tax-scope.consu'),
        };
    }

    public static function options(): array
    {
        return [
            self::SERVICE->value => __('accounts::enums/tax-scope.service'),
            self::SERVICE->value => __('accounts::enums/tax-scope.consu'),
        ];
    }
}
