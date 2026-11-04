<?php

namespace Webkul\Crm\Enums;

use Filament\Support\Contracts\HasLabel;

enum LeadConversionCustomer: string implements HasLabel
{
    case CREATE = 'create';

    case LINK = 'link';

    case NONE = 'none';

    public function getLabel(): string
    {
        return match ($this) {
            self::CREATE => __('crm::enums/lead-conversion-customer.create'),
            self::LINK   => __('crm::enums/lead-conversion-customer.link'),
            self::NONE   => __('crm::enums/lead-conversion-customer.none'),
        };
    }
}
