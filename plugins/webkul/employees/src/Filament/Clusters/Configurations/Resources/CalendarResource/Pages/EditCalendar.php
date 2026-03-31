<?php

namespace Webkul\Employee\Filament\Clusters\Configurations\Resources\CalendarResource\Pages;

use Webkul\Employee\Filament\Clusters\Configurations\Resources\CalendarResource;
use Webkul\Support\Filament\Resources\CalendarResource\Pages\EditCalendar as BaseEditCalendar;

class EditCalendar extends BaseEditCalendar
{
    protected static string $resource = CalendarResource::class;
}
