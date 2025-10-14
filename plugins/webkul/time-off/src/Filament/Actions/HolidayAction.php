<?php

namespace Webkul\TimeOff\Filament\Actions;

use Filament\Actions\Action;
use Filament\Forms\Components\Placeholder;
use Filament\Support\Enums\Width;
use Illuminate\Support\HtmlString;
use Webkul\Employee\Models\CalendarLeaves;
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
                Placeholder::make('public_holiday')
                    ->label(__('time-off::filament/actions/holiday-action.form.placeholders.public-holiday'))
                    ->content(function () {
                        $publicHolidays = CalendarLeaves::with('company')->get();

                        if ($publicHolidays->isEmpty()) {
                            return new HtmlString('<p class="text-gray-500 dark:text-gray-400">No public holidays found.</p>');
                        }

                        $html = '<div class="flex flex-col gap-4">';

                        foreach ($publicHolidays as $holiday) {
                            $dateRange = $holiday->date_from === $holiday->date_to
                                ? $holiday->date_from
                                : "{$holiday->date_from} - {$holiday->date_to}";

                            $companyName = $holiday->calendar?->company ? $holiday->calendar?->company->name : 'N/A';

                            $html .= "
                                <div class='flex items-center justify-between rounded-lg bg-gray-100 p-4 dark:bg-gray-800'>
                                    <div class='flex-1'>
                                        <h3 class='text-sm font-medium text-gray-900 dark:text-white'>{$holiday->name}</h3>
                                        <p class='mt-1 text-xs text-gray-600 dark:text-gray-400'>{$companyName}</p>
                                    </div>
                                    <div class='flex-1 text-right'>
                                        <p class='text-sm text-gray-800 dark:text-gray-300'>{$dateRange}</p>
                                    </div>
                                </div>
                            ";
                        }

                        $html .= '</div>';

                        return new HtmlString($html);
                    }),

                Placeholder::make('mandatory_holiday')
                    ->label(__('time-off::filament/actions/holiday-action.form.placeholders.mandatory-holiday'))
                    ->content(function () {
                        $mandatoryHolidays = LeaveMandatoryDay::with('company', 'createdBy')->get();

                        if ($mandatoryHolidays->isEmpty()) {
                            return new HtmlString('<p class="text-gray-500 dark:text-gray-400">No mandatory holidays found.</p>');
                        }

                        $html = '<div class="flex flex-col gap-4">';

                        foreach ($mandatoryHolidays as $mandatoryHoliday) {
                            $dateRange = $mandatoryHoliday->start_date === $mandatoryHoliday->end_date
                                ? $mandatoryHoliday->start_date
                                : "{$mandatoryHoliday->start_date} - {$mandatoryHoliday->end_date}";

                            $companyName = $mandatoryHoliday->company?->name ?? 'N/A';

                            $html .= "
                                <div class='flex items-center justify-between rounded-lg bg-gray-100 p-4 dark:bg-gray-800'>
                                    <div class='flex-1'>
                                        <h3 class='text-sm font-medium text-blue-900 dark:text-white'>{$mandatoryHoliday->name}</h3>
                                        <p class='mt-1 text-xs text-blue-600 dark:text-gray-400'>{$companyName}</p>
                                    </div>
                                    <div class='flex-1 text-right'>
                                        <p class='text-sm text-blue-800 dark:text-blue-300'>{$dateRange}</p>
                                    </div>
                                </div>
                            ";
                        }

                        $html .= '</div>';

                        return new HtmlString($html);
                    }),
            ])
            ->modalIcon('heroicon-o-lifebuoy')
            ->label(__('time-off::filament/actions/holiday-action.title'))
            ->modalSubmitAction(false)
            ->modalCancelAction(false);
    }
}
