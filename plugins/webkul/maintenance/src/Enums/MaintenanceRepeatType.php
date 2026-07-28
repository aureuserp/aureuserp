<?php

namespace Webkul\Maintenance\Enums;

use Filament\Support\Contracts\HasLabel;

enum MaintenanceRepeatType: string implements HasLabel
{
    case FOREVER = 'forever';

    case UNTIL = 'until';

    public function getLabel(): string
    {
        return match ($this) {
            self::FOREVER => __('maintenance::enums/maintenance-repeat-type.forever'),
            self::UNTIL   => __('maintenance::enums/maintenance-repeat-type.until'),
        };
    }
}
