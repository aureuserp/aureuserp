<?php

namespace Webkul\Manufacturing\Enums;

use Filament\Support\Contracts\HasColor;
use Filament\Support\Contracts\HasLabel;

enum BillOfMaterialType: string implements HasColor, HasLabel
{
    case NORMAL = 'normal';

    case PHANTOM = 'phantom';

    public static function options(): array
    {
        return [
            self::NORMAL->value  => __('manufacturing::enums/bill-of-material-type.normal'),
            self::PHANTOM->value => __('manufacturing::enums/bill-of-material-type.phantom'),
        ];
    }

    public function getLabel(): string
    {
        return self::options()[$this->value];
    }

    public function getColor(): string
    {
        return match ($this) {
            self::NORMAL  => 'primary',
            self::PHANTOM => 'gray',
        };
    }
}
