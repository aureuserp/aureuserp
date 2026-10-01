<?php

namespace Webkul\Attendance\Filament\Resources\AttendanceResource\Tables;

use Filament\Actions\Action;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Actions\ExportAction;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Select;
use Filament\Notifications\Notification;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Gate;
use Illuminate\Validation\ValidationException;
use Webkul\Attendance\Filament\Exports\AttendanceExporter;
use Webkul\Attendance\Models\Attendance;

class AttendancesTable
{
    /**
     * Stored values are naive UTC strings; always read the raw attribute so
     * the app timezone never leaks in (Carbon::parse(Carbon, 'UTC') would
     * ignore the 'UTC' argument when given an already-cast instance).
     */
    public static function employeeWallClock(Attendance $record, string $field): ?Carbon
    {
        $raw = $record->getRawOriginal($field);

        if (! $raw) {
            return null;
        }

        $timezone = $record->employee?->time_zone ?: config('app.timezone');

        return Carbon::parse($raw, 'UTC')->setTimezone($timezone);
    }

    public static function configure(Table $table): Table
    {
        return $table
            ->defaultSort('work_date', 'desc')
            ->modifyQueryUsing(fn ($query) => $query->with('employee'))
            ->columns([
                TextColumn::make('employee.name')
                    ->label(__('attendance::filament/resources/attendance.table.columns.employee'))
                    ->sortable()
                    ->searchable(),
                TextColumn::make('work_date')
                    ->label(__('attendance::filament/resources/attendance.table.columns.work-date'))
                    ->date()
                    ->sortable(),
                TextColumn::make('check_in')
                    ->label(__('attendance::filament/resources/attendance.table.columns.check-in'))
                    ->state(fn (Attendance $record) => static::employeeWallClock($record, 'check_in'))
                    ->dateTime()
                    ->sortable(),
                TextColumn::make('check_out')
                    ->label(__('attendance::filament/resources/attendance.table.columns.check-out'))
                    ->state(fn (Attendance $record) => static::employeeWallClock($record, 'check_out'))
                    ->dateTime()
                    ->sortable()
                    ->placeholder('—'),
                TextColumn::make('worked_minutes')
                    ->label(__('attendance::filament/resources/attendance.table.columns.worked'))
                    ->state(fn (Attendance $record) => $record->worked_minutes)
                    ->formatStateUsing(fn ($state) => $state === null ? '—' : intdiv((int) $state, 60).'h '.((int) $state % 60).'m'),
                TextColumn::make('source')
                    ->label(__('attendance::filament/resources/attendance.table.columns.source'))
                    ->badge()
                    ->formatStateUsing(fn (string $state, Attendance $record): string => Attendance::sourceDisplayName($state, $record->source_label))
                    ->color(fn (string $state): string => $state === Attendance::SOURCE_MANUAL ? 'success' : 'info')
                    ->sortable(),
                TextColumn::make('created_at')
                    ->label(__('attendance::filament/resources/attendance.table.columns.created-at'))
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                SelectFilter::make('employee_id')
                    ->label(__('attendance::filament/resources/attendance.table.filters.employee'))
                    ->relationship('employee', 'name')
                    ->searchable()
                    ->preload(),
                SelectFilter::make('source')
                    ->label(__('attendance::filament/resources/attendance.table.filters.source'))
                    ->options(fn (): array => Attendance::query()
                        ->select('source')
                        ->selectRaw('MAX(source_label) AS source_label')
                        ->groupBy('source')
                        ->get()
                        ->mapWithKeys(fn (Attendance $row) => [
                            $row->source => Attendance::sourceDisplayName($row->source, $row->source_label),
                        ])
                        ->all()),
                Filter::make('work_date')
                    ->label(__('attendance::filament/resources/attendance.table.filters.work-date'))
                    ->form([
                        Select::make('preset')
                            ->label(__('attendance::filament/resources/attendance.table.filters.preset'))
                            ->options([
                                'today'      => __('attendance::filament/resources/attendance.table.filters.preset-today'),
                                'this_week'  => __('attendance::filament/resources/attendance.table.filters.preset-this-week'),
                                'this_month' => __('attendance::filament/resources/attendance.table.filters.preset-this-month'),
                            ])
                            ->live(),
                        DatePicker::make('work_date_from')
                            ->label(__('attendance::filament/resources/attendance.table.filters.date-from')),
                        DatePicker::make('work_date_to')
                            ->label(__('attendance::filament/resources/attendance.table.filters.date-to')),
                    ])
                    ->query(function ($query, array $data) {
                        if (! empty($data['preset'])) {
                            [$from, $to] = match ($data['preset']) {
                                'today'      => [Carbon::today()->toDateString(), Carbon::today()->toDateString()],
                                'this_week'  => [Carbon::now()->startOfWeek()->toDateString(), Carbon::now()->endOfWeek()->toDateString()],
                                default      => [Carbon::now()->startOfMonth()->toDateString(), Carbon::now()->endOfMonth()->toDateString()],
                            };

                            return $query->whereDate('work_date', '>=', $from)->whereDate('work_date', '<=', $to);
                        }

                        return $query
                            ->when($data['work_date_from'] ?? null, fn ($query, string $date) => $query->whereDate('work_date', '>=', $date))
                            ->when($data['work_date_to'] ?? null, fn ($query, string $date) => $query->whereDate('work_date', '<=', $date));
                    }),
            ])
            ->recordActions([
                Action::make('add_checkout')
                    ->label(__('attendance::filament/resources/attendance.table.record-actions.add-checkout'))
                    ->icon('heroicon-o-arrow-right-end-on-rectangle')
                    ->color('success')
                    ->visible(fn (Attendance $record): bool => $record->check_out === null && auth()->user()?->can('update', $record))
                    ->form([
                        DateTimePicker::make('check_out')
                            ->label(__('attendance::filament/resources/attendance.form.check-out'))
                            ->required()
                            ->seconds(),
                    ])
                    ->action(function (Attendance $record, array $data): void {
                        Gate::authorize('update', $record);

                        $timezone = $record->employee?->time_zone ?: config('app.timezone');
                        $checkOut = Carbon::parse($data['check_out'], $timezone);
                        $checkIn = Carbon::parse($record->getRawOriginal('check_in'), 'UTC');

                        if ($checkOut->lt($checkIn)) {
                            throw ValidationException::withMessages([
                                'check_out' => __('attendance::filament/resources/attendance.form.check-out-before-check-in'),
                            ]);
                        }

                        if ($checkOut->toDateString() !== $record->work_date->format('Y-m-d')) {
                            throw ValidationException::withMessages([
                                'check_out' => __('attendance::filament/resources/attendance.form.check-out-not-in-work-date'),
                            ]);
                        }

                        $record->update(['check_out' => $checkOut->setTimezone('UTC')->format('Y-m-d H:i:s')]);

                        $minutes = $checkIn->diffInMinutes($checkOut);

                        if ($minutes > Attendance::LONG_SHIFT_MINUTES) {
                            Notification::make()
                                ->warning()
                                ->title(__('attendance::filament/resources/attendance.form.long-shift-warning', [
                                    'hours' => round($minutes / 60, 1),
                                ]))
                                ->send();
                        }
                    }),
                EditAction::make()
                    ->visible(fn (Attendance $record): bool => $record->check_out !== null && (bool) auth()->user()?->can('update', $record)),
                DeleteAction::make()
                    ->visible(fn (Attendance $record): bool => (bool) auth()->user()?->can('delete', $record)),
            ])
            ->toolbarActions([
                ExportAction::make()
                    ->label(__('attendance::filament/resources/attendance.table.toolbar-actions.export'))
                    ->icon('heroicon-o-arrow-up-tray')
                    ->exporter(AttendanceExporter::class),
            ]);
    }
}
