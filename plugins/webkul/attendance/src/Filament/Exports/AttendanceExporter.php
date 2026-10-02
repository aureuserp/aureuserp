<?php

namespace Webkul\Attendance\Filament\Exports;

use Filament\Actions\Exports\ExportColumn;
use Filament\Actions\Exports\Exporter;
use Filament\Actions\Exports\Models\Export;
use Illuminate\Support\Carbon;
use Webkul\Attendance\Filament\Resources\AttendanceResource\Tables\AttendancesTable;
use Webkul\Attendance\Models\Attendance;

class AttendanceExporter extends Exporter
{
    protected static ?string $model = Attendance::class;

    public static function getColumns(): array
    {
        return [
            ExportColumn::make('employee.name')
                ->label(__('attendance::filament/exports/attendance.columns.employee')),
            ExportColumn::make('work_date')
                ->label(__('attendance::filament/exports/attendance.columns.work-date'))
                ->formatStateUsing(fn ($state): string => $state ? Carbon::parse($state)->format('Y-m-d') : ''),
            ExportColumn::make('check_in')
                ->label(__('attendance::filament/exports/attendance.columns.check-in'))
                ->formatStateUsing(fn ($state, Attendance $record): string => AttendancesTable::employeeWallClock($record, 'check_in')?->format('Y-m-d H:i:s') ?? ''),
            ExportColumn::make('check_out')
                ->label(__('attendance::filament/exports/attendance.columns.check-out'))
                ->formatStateUsing(fn ($state, Attendance $record): string => AttendancesTable::employeeWallClock($record, 'check_out')?->format('Y-m-d H:i:s') ?? ''),
            ExportColumn::make('worked_minutes')
                ->label(__('attendance::filament/exports/attendance.columns.worked'))
                ->formatStateUsing(fn ($state) => $state === null ? '' : intdiv((int) $state, 60).'h '.((int) $state % 60).'m'),
            ExportColumn::make('source')
                ->label(__('attendance::filament/exports/attendance.columns.source'))
                ->formatStateUsing(fn (?string $state, Attendance $record): string => Attendance::sourceDisplayName($state ?? $record->source, $record->source_label)),
        ];
    }

    public static function getCompletedNotificationBody(Export $export): string
    {
        $body = __('attendance::filament/exports/attendance.notification.completed', [
            'count' => number_format($export->successful_rows),
        ]);

        if ($failedRowsCount = $export->getFailedRowsCount()) {
            $body .= ' '.__('attendance::filament/exports/attendance.notification.failed', [
                'count' => number_format($failedRowsCount),
            ]);
        }

        return $body;
    }
}
