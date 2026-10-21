<?php

namespace Webkul\PointOfSale\Enums;

use Filament\Support\Contracts\HasColor;
use Filament\Support\Contracts\HasLabel;

enum PrinterType: string implements HasColor, HasLabel
{
    case IOT = 'iot';

    case EPSON_EPOS = 'epson_epos';

    public function getLabel(): string
    {
        return match ($this) {
            self::IOT        => __('point-of-sale::enums/printer-type.iot'),
            self::EPSON_EPOS => __('point-of-sale::enums/printer-type.epson-epos'),
        };
    }

    public function getColor(): string
    {
        return match ($this) {
            self::IOT        => 'primary',
            self::EPSON_EPOS => 'info',
        };
    }
}
