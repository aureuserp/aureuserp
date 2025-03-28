<?php

namespace Webkul\TimeOff\Enums;

use Filament\Support\Contracts\HasLabel;

enum LeaveValidationType: string implements HasLabel
{
    case NO_VALIDATION = 'no_validation';

    case HR = 'hr';

    case MANAGER = 'manager';

    case BOTH = 'both';

    public function getLabel(): ?string
    {
        return match ($this) {
            self::NO_VALIDATION => __('time-off::enums/leave-type.no-validation'),
            self::HR            => __('time-off::enums/leave-type.by-time-off-officer'),
            self::MANAGER       => __('time-off::enums/leave-type.by-employees-approver'),
            self::BOTH          => __('time-off::enums/leave-type.by-employees-approver-and-time-off-officer'),
        };
    }

    public static function options(): array
    {
        return [
            self::NO_VALIDATION->value => __('time-off::enums/leave-type.no-validation'),
            self::HR->value            => __('time-off::enums/leave-type.by-time-off-officer'),
            self::MANAGER->value       => __('time-off::enums/leave-type.by-employees-approver'),
            self::BOTH->value          => __('time-off::enums/leave-type.by-employees-approver-and-time-off-officer'),
        ];
    }
}
