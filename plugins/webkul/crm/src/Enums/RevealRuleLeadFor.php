<?php

namespace Webkul\Crm\Enums;

use Filament\Support\Contracts\HasLabel;

enum RevealRuleLeadFor: string implements HasLabel
{
    case COMPANIES = 'companies';

    case PEOPLE = 'people';

    public function getLabel(): string
    {
        return match ($this) {
            self::COMPANIES => __('crm::enums/reveal-rule-lead-for.companies'),
            self::PEOPLE    => __('crm::enums/reveal-rule-lead-for.people'),
        };
    }
}
