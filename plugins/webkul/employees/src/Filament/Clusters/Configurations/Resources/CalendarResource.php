<?php

namespace Webkul\Employee\Filament\Clusters\Configurations\Resources;

use Webkul\Employee\Filament\Clusters\Configurations;
use Webkul\Employee\Filament\Clusters\Configurations\Resources\CalendarResource\Pages\CreateCalendar;
use Webkul\Employee\Filament\Clusters\Configurations\Resources\CalendarResource\Pages\EditCalendar;
use Webkul\Employee\Filament\Clusters\Configurations\Resources\CalendarResource\Pages\ListCalendars;
use Webkul\Employee\Filament\Clusters\Configurations\Resources\CalendarResource\Pages\ViewCalendar;
use Webkul\Support\Filament\Clusters\Configurations\Resources\CalendarResource as BaseCalendarResource;

class CalendarResource extends BaseCalendarResource
{
    protected static ?string $cluster = Configurations::class;

    public static function getPages(): array
    {
        return [
            'index'  => ListCalendars::route('/'),
            'create' => CreateCalendar::route('/create'),
            'view'   => ViewCalendar::route('/{record}'),
            'edit'   => EditCalendar::route('/{record}/edit'),
        ];
    }
}
