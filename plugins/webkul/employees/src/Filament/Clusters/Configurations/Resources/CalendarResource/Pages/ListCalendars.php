<?php

namespace Webkul\Employee\Filament\Clusters\Configurations\Resources\CalendarResource\Pages;

use Webkul\Employee\Filament\Clusters\Configurations\Resources\CalendarResource;
use Webkul\Support\Filament\Clusters\Configurations\Resources\CalendarResource\Pages\ListCalendars as BaseListCalendars;

class ListCalendars extends BaseListCalendars
{
    protected static string $resource = CalendarResource::class;
}
