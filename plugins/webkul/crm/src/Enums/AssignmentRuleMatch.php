<?php

namespace Webkul\Crm\Enums;

use Filament\Support\Contracts\HasLabel;

enum AssignmentRuleMatch: string implements HasLabel
{
    case ALL = 'all';

    case ANY = 'any';

    public function getLabel(): string
    {
        return match ($this) {
            self::ALL => __('crm::enums/assignment-rule-match.all'),
            self::ANY => __('crm::enums/assignment-rule-match.any'),
        };
    }
}
