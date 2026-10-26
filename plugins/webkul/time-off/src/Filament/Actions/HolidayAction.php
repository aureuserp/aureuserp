<?php

namespace Webkul\TimeOff\Filament\Actions;

use Filament\Actions\Action;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Grid;
use Filament\Support\Enums\FontWeight;
use Filament\Support\Enums\Width;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;
use Webkul\Support\Filament\Infolists\Components\RepeatableEntry;
use Webkul\Support\Models\CalendarLeave;
use Webkul\TimeOff\Models\LeaveMandatoryDay;

class HolidayAction extends Action
{
    public static function getDefaultName(): ?string
    {
        return 'time_off.holiday_action';
    }

    protected function setUp(): void
    {
        parent::setUp();

        $this
            ->hiddenLabel()
            ->icon('heroicon-o-lifebuoy')
            ->modalWidth(Width::TwoExtraLarge)
            ->slideOver()
            ->schema([
                TextEntry::make('public_holidays_empty')
                    ->hiddenLabel()
                    ->state(__('time-off::filament/actions/holiday-action.form.empty.public-holiday'))
                    ->color('gray')
                    ->visible(fn (): bool => CalendarLeave::query()->where('time_type', 'leave')->doesntExist()),

                RepeatableEntry::make('public_holidays')
                    ->label(__('time-off::filament/actions/holiday-action.form.placeholders.public-holiday'))
                    ->state(fn (): Collection => CalendarLeave::query()->where('time_type', 'leave')->get())
                    ->visible(fn (): bool => CalendarLeave::query()->where('time_type', 'leave')->exists())
                    ->contained(false)
                    ->schema([
                        Grid::make(2)
                            ->extraAttributes(['class' => 'items-center rounded-lg bg-gray-100 p-4 dark:bg-gray-800'])
                            ->schema([
                                TextEntry::make('name')
                                    ->hiddenLabel()
                                    ->weight(FontWeight::Medium),

                                TextEntry::make('date_range')
                                    ->hiddenLabel()
                                    ->alignEnd()
                                    ->state(fn (CalendarLeave $record): string => $this->formatDateRange($record->date_from, $record->date_to)),
                            ]),
                    ]),

                TextEntry::make('mandatory_holidays_empty')
                    ->hiddenLabel()
                    ->state(__('time-off::filament/actions/holiday-action.form.empty.mandatory-holiday'))
                    ->color('gray')
                    ->visible(fn (): bool => LeaveMandatoryDay::query()->doesntExist()),

                RepeatableEntry::make('mandatory_holidays')
                    ->label(__('time-off::filament/actions/holiday-action.form.placeholders.mandatory-holiday'))
                    ->state(fn (): Collection => LeaveMandatoryDay::query()->get())
                    ->visible(fn (): bool => LeaveMandatoryDay::query()->exists())
                    ->contained(false)
                    ->schema([
                        Grid::make(2)
                            ->extraAttributes(['class' => 'items-center rounded-lg bg-gray-100 p-4 dark:bg-gray-800'])
                            ->schema([
                                TextEntry::make('name')
                                    ->hiddenLabel()
                                    ->weight(FontWeight::Medium),

                                TextEntry::make('date_range')
                                    ->hiddenLabel()
                                    ->alignEnd()
                                    ->state(fn (LeaveMandatoryDay $record): string => $this->formatDateRange($record->start_date, $record->end_date)),
                            ]),
                    ]),
            ])
            ->modalIcon('heroicon-o-lifebuoy')
            ->label(__('time-off::filament/actions/holiday-action.title'))
            ->modalSubmitAction(false)
            ->modalCancelAction(false);
    }

    protected function formatDateRange(mixed $from, mixed $to): string
    {
        $fromLabel = filled($from) ? Carbon::parse($from)->isoFormat('D MMM YYYY') : '—';

        $toLabel = filled($to) ? Carbon::parse($to)->isoFormat('D MMM YYYY') : $fromLabel;

        return $fromLabel === $toLabel ? $fromLabel : "{$fromLabel} - {$toLabel}";
    }
}
