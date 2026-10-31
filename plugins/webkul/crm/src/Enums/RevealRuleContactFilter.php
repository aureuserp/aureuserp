<?php

namespace Webkul\Crm\Enums;

use Filament\Support\Contracts\HasLabel;

enum RevealRuleContactFilter: string implements HasLabel
{
    case ROLE = 'role';

    case SENIORITY = 'seniority';

    public function getLabel(): string
    {
        return match ($this) {
            self::ROLE      => __('crm::enums/reveal-rule-contact-filter.role'),
            self::SENIORITY => __('crm::enums/reveal-rule-contact-filter.seniority'),
        };
    }
}
