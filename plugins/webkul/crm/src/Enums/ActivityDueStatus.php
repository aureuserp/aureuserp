<?php

namespace Webkul\Crm\Enums;

use Carbon\CarbonInterface;
use Filament\Support\Contracts\HasColor;
use Filament\Support\Contracts\HasIcon;
use Filament\Support\Contracts\HasLabel;

enum ActivityDueStatus: string implements HasColor, HasIcon, HasLabel
{
    case Overdue = 'overdue';

    case Today = 'today';

    case Planned = 'planned';

    public static function fromDate(?CarbonInterface $date): self
    {
        return match (true) {
            $date === null      => self::Planned,
            $date->isToday()    => self::Today,
            $date->isPast()     => self::Overdue,
            default             => self::Planned,
        };
    }

    public function getLabel(): string
    {
        return __('crm::enums/activity-due-status.'.$this->value);
    }

    public function getColor(): string
    {
        return match ($this) {
            self::Overdue => 'danger',
            self::Today   => 'warning',
            self::Planned => 'success',
        };
    }

    public function getIcon(): string
    {
        return match ($this) {
            self::Overdue => 'heroicon-o-exclamation-circle',
            self::Today   => 'heroicon-o-clock',
            self::Planned => 'heroicon-o-calendar-days',
        };
    }
}
