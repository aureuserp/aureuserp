<?php

namespace Webkul\TimeOff\Filament\Clusters\Configurations\Resources\PublicHolidays\Schemas;

use Filament\Infolists\Components\ColorEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Schema;

class PublicHolidayInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                ColorEntry::make('color')
                    ->placeholder('—')
                    ->label(__('time-off::filament/clusters/configurations/resources/public-holiday.infolist.entries.color')),
                TextEntry::make('name')
                    ->placeholder('-')
                    ->label(__('time-off::filament/clusters/configurations/resources/public-holiday.infolist.entries.name')),
                TextEntry::make('date_from')
                    ->date()
                    ->placeholder('-')
                    ->icon('heroicon-o-calendar')
                    ->label(__('time-off::filament/clusters/configurations/resources/public-holiday.infolist.entries.date-from')),
                TextEntry::make('date_to')
                    ->date()
                    ->placeholder('-')
                    ->icon('heroicon-o-calendar')
                    ->label(__('time-off::filament/clusters/configurations/resources/public-holiday.infolist.entries.date-to')),
            ]);
    }
}
