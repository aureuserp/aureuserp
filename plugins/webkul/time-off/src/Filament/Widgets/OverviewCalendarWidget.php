<?php

namespace Webkul\TimeOff\Filament\Widgets;

use Illuminate\Contracts\Support\Htmlable;

class OverviewCalendarWidget extends CalendarWidget
{
    public function getHeading(): string|Htmlable|null
    {
        return __('time-off::filament/widgets/overview-calendar-widget.heading.title');
    }

    public function config(): array
    {
        return [
            'initialView' => 'multiMonthYear',
        ];
    }
}
