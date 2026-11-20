<?php

namespace Webkul\Crm\Enums;

use Carbon\CarbonInterface;
use Filament\Support\Contracts\HasColor;
use Filament\Support\Contracts\HasIcon;
use Filament\Support\Contracts\HasLabel;

enum ActivityDueStatus: string implements HasColor, HasIcon, HasLabel
{
    case OVERDUE = 'overdue';

    case TODAY = 'today';

    case PLANNED = 'planned';

    public static function fromDate(?CarbonInterface $date): self
    {
        return match (true) {
            $date === null      => self::PLANNED,
            $date->isToday()    => self::TODAY,
            $date->isPast()     => self::OVERDUE,
            default             => self::PLANNED,
        };
    }

    public function getLabel(): string
    {
        return match ($this) {
            self::OVERDUE => __('crm::enums/activity-due-status.overdue'),
            self::TODAY   => __('crm::enums/activity-due-status.today'),
            self::PLANNED => __('crm::enums/activity-due-status.planned'),
        };
    }

    public function getColor(): string
    {
        return match ($this) {
            self::OVERDUE => 'danger',
            self::TODAY   => 'warning',
            self::PLANNED => 'success',
        };
    }

    public function getIcon(): string
    {
        return match ($this) {
            self::OVERDUE => 'heroicon-o-exclamation-circle',
            self::TODAY   => 'heroicon-o-clock',
            self::PLANNED => 'heroicon-o-calendar-days',
        };
    }
}
