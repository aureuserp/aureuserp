<?php

namespace Webkul\Crm\Enums;

use Filament\Support\Contracts\HasLabel;

enum LeadConversionAction: string implements HasLabel
{
    case CONVERT = 'convert';

    case MERGE = 'merge';

    public function getLabel(): string
    {
        return match ($this) {
            self::CONVERT => __('crm::enums/lead-conversion-action.convert'),
            self::MERGE   => __('crm::enums/lead-conversion-action.merge'),
        };
    }
}
