<?php

namespace Webkul\Attendance\Filament\Resources\AttendanceResource\Pages;

use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;
use Webkul\Attendance\Filament\Resources\AttendanceResource;
use Webkul\Attendance\Filament\Widgets\AttendanceStatsWidget;

class ListAttendances extends ListRecords
{
    protected static string $resource = AttendanceResource::class;

    public function getHeaderWidgets(): array
    {
        return [
            AttendanceStatsWidget::make(),
        ];
    }

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
