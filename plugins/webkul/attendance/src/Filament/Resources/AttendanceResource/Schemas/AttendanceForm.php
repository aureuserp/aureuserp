<?php

namespace Webkul\Attendance\Filament\Resources\AttendanceResource\Schemas;

use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\Select;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Components\Utilities\Set;
use Filament\Schemas\Schema;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;
use Webkul\Attendance\Models\Attendance;
use Webkul\Employee\Models\Employee;

class AttendanceForm
{
    /**
     * Time zone the manual wall-clock values are interpreted in:
     * the employee's own zone, app timezone fallback.
     */
    public static function employeeTimezone(?int $employeeId): string
    {
        if ($employeeId) {
            $timezone = Employee::query()->whereKey($employeeId)->value('time_zone');

            if ($timezone) {
                return $timezone;
            }
        }

        return config('app.timezone');
    }

    /**
     * "Today" on the employee's wall clock. The server date is wrong
     * for anyone past UTC midnight in their own zone.
     */
    public static function employeeToday(?int $employeeId): string
    {
        return Carbon::now(static::employeeTimezone($employeeId))->toDateString();
    }

    /**
     * Calendar day of a wall-clock punch, interpreted in the employee's
     * time zone (app timezone fallback). Single source of truth so the
     * form never compares a UTC default against a browser-local default.
     */
    public static function resolveWorkDate(string $checkIn, ?int $employeeId): string
    {
        return Carbon::parse($checkIn, static::employeeTimezone($employeeId))->toDateString();
    }

    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('employee_id')
                    ->label(__('attendance::filament/resources/attendance.form.employee'))
                    ->relationship('employee', 'name')
                    ->searchable()
                    ->preload()
                    ->required()
                    ->live()
                    ->disabledOn('edit')
                    ->afterStateUpdated(function (Get $get, Set $set, $state): void {
                        if (! blank($get('work_date'))) {
                            return;
                        }

                        $set('work_date', static::employeeToday($state ?: null));
                    }),
                DatePicker::make('work_date')
                    ->label(__('attendance::filament/resources/attendance.form.work-date'))
                    ->required()
                    ->maxDate(fn (Get $get): string => static::employeeToday($get('employee_id')))
                    ->default(fn (Get $get): ?string => $get('employee_id') ? static::employeeToday($get('employee_id')) : null)
                    ->live()
                    ->disabledOn('edit')
                    ->rules(fn (Get $get, ?Model $record): array => [
                        function (string $attribute, mixed $value, \Closure $fail) use ($get, $record) {
                            if (! $value || ! $get('employee_id')) {
                                return;
                            }

                            $query = Attendance::query()
                                ->where('employee_id', $get('employee_id'))
                                ->where('work_date', $value)
                                ->where('source', $get('source') ?? 'manual');

                            if ($record?->exists) {
                                $query->whereKeyNot($record->getKey());
                            }

                            if ($query->exists()) {
                                $employeeName = Employee::query()->whereKey($get('employee_id'))->value('name');
                                $date = Carbon::parse($value)->format('d/m/Y');

                                $fail(__('attendance::filament/resources/attendance.form.duplicate-day', [
                                    'employee' => $employeeName,
                                    'date'     => $date,
                                ]));
                            }
                        },
                    ]),
                DateTimePicker::make('check_in')
                    ->label(__('attendance::filament/resources/attendance.form.check-in'))
                    ->required()
                    ->seconds()
                    ->live()
                    ->rules(fn (Get $get): array => [
                        function (string $attribute, mixed $value, \Closure $fail) use ($get) {
                            if (! $value || ! $get('work_date')) {
                                return;
                            }

                            if (static::resolveWorkDate($value, $get('employee_id')) !== Carbon::parse($get('work_date'))->toDateString()) {
                                $fail(__('attendance::filament/resources/attendance.form.check-in-not-in-work-date'));
                            }
                        },
                    ]),
                DateTimePicker::make('check_out')
                    ->label(__('attendance::filament/resources/attendance.form.check-out'))
                    ->seconds()
                    ->live()
                    ->rule('after_or_equal:check_in')
                    ->rules(fn (Get $get): array => [
                        function (string $attribute, mixed $value, \Closure $fail) use ($get) {
                            if (! $value || ! $get('work_date')) {
                                return;
                            }

                            if (static::resolveWorkDate($value, $get('employee_id')) !== Carbon::parse($get('work_date'))->toDateString()) {
                                $fail(__('attendance::filament/resources/attendance.form.check-out-not-in-work-date'));
                            }
                        },
                    ])
                    ->helperText(function (Get $get): ?string {
                        if (! $get('check_in') || ! $get('check_out')) {
                            return null;
                        }

                        $minutes = Carbon::parse($get('check_in'))->diffInMinutes(Carbon::parse($get('check_out')));

                        if ($minutes <= Attendance::LONG_SHIFT_MINUTES) {
                            return null;
                        }

                        return __('attendance::filament/resources/attendance.form.long-shift-warning', [
                            'hours' => round($minutes / 60, 1),
                        ]);
                    }),
                Placeholder::make('source_label')
                    ->label(__('attendance::filament/resources/attendance.form.source'))
                    ->content(fn (Get $get, ?Model $record): string => Attendance::sourceDisplayName(
                        $record?->source ?? $get('source'),
                        $record?->source_label,
                    )),
                Hidden::make('source')
                    ->default('manual')
                    ->dehydrated(false),
            ])
            ->columns(2);
    }
}
