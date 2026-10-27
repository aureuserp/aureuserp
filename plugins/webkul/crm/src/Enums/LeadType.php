<?php

namespace Webkul\Crm\Enums;

use Filament\Support\Contracts\HasColor;
use Filament\Support\Contracts\HasLabel;

enum LeadType: string implements HasColor, HasLabel
{
    case LEAD = 'lead';

    case OPPORTUNITY = 'opportunity';

    public static function options(): array
    {
        return collect(self::cases())->mapWithKeys(fn (self $type): array => [$type->value => $type->getLabel()])->all();
    }

    public function getLabel(): string
    {
        return match ($this) {
            self::LEAD        => __('crm::enums/lead-type.lead'),
            self::OPPORTUNITY => __('crm::enums/lead-type.opportunity'),
        };
    }

    public function getColor(): string
    {
        return match ($this) {
            self::LEAD        => 'gray',
            self::OPPORTUNITY => 'info',
        };
    }
}
