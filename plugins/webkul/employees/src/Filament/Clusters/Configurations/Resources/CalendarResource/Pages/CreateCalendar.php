<?php

namespace Webkul\Employee\Filament\Clusters\Configurations\Resources\CalendarResource\Pages;

use Webkul\Employee\Filament\Clusters\Configurations\Resources\CalendarResource;
use Webkul\Support\Filament\Clusters\Configurations\Resources\CalendarResource\Pages\CreateCalendar as BaseCreateCalendar;

class CreateCalendar extends BaseCreateCalendar
{
    protected static string $resource = CalendarResource::class;
}
