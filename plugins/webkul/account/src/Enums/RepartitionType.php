<?php

namespace Webkul\Account\Enums;

use Filament\Support\Contracts\HasLabel;

enum RepartitionType: string implements HasLabel
{
    case BASE = 'base';

    case TAX = 'tax';

    public function getLabel(): ?string
    {
        return match ($this) {
            self::BASE  => __('accounts::enums/repartition-type.base'),
            self::TAX   => __('accounts::enums/repartition-type.tax'),
        };
    }

    public static function options(): array
    {
        return [
            self::BASE->value => __('accounts::enums/repartition-type.base'),
            self::TAX->value  => __('accounts::enums/repartition-type.tax'),
        ];
    }
}
