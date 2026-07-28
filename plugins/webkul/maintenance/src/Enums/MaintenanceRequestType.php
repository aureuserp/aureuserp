<?php

namespace Webkul\Maintenance\Enums;

use Filament\Support\Contracts\HasLabel;

enum MaintenanceRequestType: string implements HasLabel
{
    case CORRECTIVE = 'corrective';

    case PREVENTIVE = 'preventive';

    public function getLabel(): string
    {
        return match ($this) {
            self::CORRECTIVE => __('maintenance::enums/maintenance-request-type.corrective'),
            self::PREVENTIVE => __('maintenance::enums/maintenance-request-type.preventive'),
        };
    }
}
