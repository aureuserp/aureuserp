<?php

namespace Webkul\TimeOff\Filament\Clusters\Management\Resources\TimeOffs\Schemas;

use Filament\Infolists\Components\ImageEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Group;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Illuminate\Support\Carbon;

class TimeOffInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make()
                    ->schema([
                        Group::make()
                            ->schema([
                                TextEntry::make('holidayStatus.name')
                                    ->label(__('time-off::filament/clusters/my-time/resources/my-time-off.infolist.entries.time-off-type'))
                                    ->icon('heroicon-o-calendar'),

                                TextEntry::make('request_unit_half')
                                    ->label(__('time-off::filament/clusters/my-time/resources/my-time-off.infolist.entries.half-day'))
                                    ->formatStateUsing(fn ($record) => $record->request_unit_half ? 'Yes' : 'No')
                                    ->icon('heroicon-o-clock'),

                                TextEntry::make('request_date_from')
                                    ->label(__('time-off::filament/clusters/my-time/resources/my-time-off.infolist.entries.request-date-from'))
                                    ->date()
                                    ->icon('heroicon-o-calendar'),

                                TextEntry::make('request_date_to')
                                    ->label(__('time-off::filament/clusters/my-time/resources/my-time-off.infolist.entries.request-date-to'))
                                    ->date()
                                    ->hidden(fn ($record) => $record->request_unit_half)
                                    ->icon('heroicon-o-calendar'),

                                TextEntry::make('request_date_from_period')
                                    ->label(__('time-off::filament/clusters/my-time/resources/my-time-off.infolist.entries.period'))
                                    ->visible(fn ($record) => $record->request_unit_half)
                                    ->icon('heroicon-o-sun'),

                                TextEntry::make('private_name')
                                    ->label(__('time-off::filament/clusters/my-time/resources/my-time-off.infolist.entries.description'))
                                    ->icon('heroicon-o-document-text'),

                                TextEntry::make('duration_display')
                                    ->label(__('time-off::filament/clusters/my-time/resources/my-time-off.infolist.entries.requested-days'))
                                    ->formatStateUsing(function ($record) {
                                        if ($record->request_unit_half) {
                                            return __('time-off::filament/clusters/my-time/resources/my-time-off.infolist.entries.day', ['day' => '0.5']);
                                        }

                                        $startDate = Carbon::parse($record->request_date_from);
                                        $endDate = $record->request_date_to ? Carbon::parse($record->request_date_to) : $startDate;

                                        // Calculate business days (excluding weekends)
                                        $businessDays = 0;
                                        $currentDate = $startDate->copy();

                                        while ($currentDate->lte($endDate)) {
                                            // Check if the current date is not Saturday (6) or Sunday (0)
                                            if (! in_array($currentDate->dayOfWeek, [0, 6])) {
                                                $businessDays++;
                                            }
                                            $currentDate->addDay();
                                        }

                                        return __('time-off::filament/clusters/my-time/resources/my-time-off.infolist.entries.days', ['days' => $businessDays]);
                                    })
                                    ->icon('heroicon-o-calendar-days'),

                                ImageEntry::make('attachment')
                                    ->label(__('time-off::filament/clusters/my-time/resources/my-time-off.infolist.entries.attachment'))
                                    ->visible(fn ($record) => $record->holidayStatus?->support_document),
                            ]),
                    ])->columnSpanFull(),
            ]);
    }
}
