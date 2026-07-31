<?php

namespace Webkul\Support\Filament\Forms\Components;

use Carbon\Carbon;
use Exception;
use Filament\Forms\Components\Hidden;
use Filament\Schemas\Components\Component;
use Filament\Schemas\Components\Utilities\Set;
use Malzariey\FilamentDaterangepickerFilter\Fields\DateRangePicker;

/**
 * Dashboard filter date range.
 *
 * The picker keeps a single string state (`01/01/2026 - 31/12/2026`), but the widgets
 * read two separate filter keys. The hidden fields below mirror the picked range into
 * those keys so widgets keep working unchanged.
 */
class DashboardDateRange
{
    /**
     * @return array<int, Component>
     */
    public static function make(
        string $label,
        string $startKey = 'startDate',
        string $endKey = 'endDate',
        string $name = 'date_range',
    ): array {
        return [
            DateRangePicker::make($name)
                ->label($label)
                ->suffixIcon('heroicon-o-calendar')
                ->defaultThisYear()
                ->ranges(static::ranges())
                ->alwaysShowCalendar()
                ->live()
                ->afterStateUpdated(function ($state, Set $set) use ($startKey, $endKey) {
                    [$start, $end] = static::split($state);

                    $set($startKey, $start);
                    $set($endKey, $end);
                }),

            Hidden::make($startKey)
                ->default(now()->startOfYear()->toDateString()),

            Hidden::make($endKey)
                ->default(now()->endOfYear()->toDateString()),
        ];
    }

    /**
     * Selectable presets, matching the ones already used on the General Ledger report.
     *
     * @return array<string, array<int, Carbon>>
     */
    public static function ranges(): array
    {
        return [
            'Today'        => [now()->startOfDay(), now()->endOfDay()],
            'Yesterday'    => [now()->subDay()->startOfDay(), now()->subDay()->endOfDay()],
            'This Month'   => [now()->startOfMonth(), now()->endOfMonth()],
            'Last Month'   => [now()->subMonth()->startOfMonth(), now()->subMonth()->endOfMonth()],
            'This Quarter' => [now()->startOfQuarter(), now()->endOfQuarter()],
            'Last Quarter' => [now()->subQuarter()->startOfQuarter(), now()->subQuarter()->endOfQuarter()],
            'This Year'    => [now()->startOfYear(), now()->endOfYear()],
            'Last Year'    => [now()->subYear()->startOfYear(), now()->subYear()->endOfYear()],
        ];
    }

    /**
     * Split the picker state into two `Y-m-d` dates, or nulls when it cannot be read.
     *
     * @return array{0: ?string, 1: ?string}
     */
    public static function split(mixed $state): array
    {
        if (is_array($state) && isset($state['startDate'], $state['endDate'])) {
            return [static::toDate($state['startDate']), static::toDate($state['endDate'])];
        }

        if (is_array($state) && count($state) === 2) {
            return [static::toDate($state[0]), static::toDate($state[1])];
        }

        if (is_string($state)) {
            $dates = explode(' - ', $state);

            if (count($dates) === 2) {
                return [static::toDate(trim($dates[0])), static::toDate(trim($dates[1]))];
            }
        }

        return [null, null];
    }

    /**
     * The picker hands back `d/m/Y`, which `Carbon::parse()` would read as `m/d/Y`.
     */
    protected static function toDate(mixed $date): ?string
    {
        if (blank($date)) {
            return null;
        }

        if ($date instanceof Carbon) {
            return $date->toDateString();
        }

        if (preg_match('/^(\d{2})\/(\d{2})\/(\d{4})$/', (string) $date, $parts)) {
            return "{$parts[3]}-{$parts[2]}-{$parts[1]}";
        }

        try {
            return Carbon::parse($date)->toDateString();
        } catch (Exception) {
            return null;
        }
    }
}
