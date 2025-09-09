<?php

namespace Webkul\TimeOff\Filament\Clusters\Configurations\Resources\MandatoryDays\Schemas;

use Filament\Infolists\Components\ColorEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Schema;

class MandatoryDayInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                ColorEntry::make('color')
                    ->placeholder('—')
                    ->label(__('time-off::filament/clusters/configurations/resources/mandatory-days.infolist.entries.color')),
                TextEntry::make('name')
                    ->placeholder('-')
                    ->label(__('time-off::filament/clusters/configurations/resources/mandatory-days.infolist.entries.name')),
                TextEntry::make('start_date')
                    ->date()
                    ->placeholder('-')
                    ->icon('heroicon-o-calendar')
                    ->label(__('time-off::filament/clusters/configurations/resources/mandatory-days.infolist.entries.start-date')),
                TextEntry::make('end_date')
                    ->date()
                    ->placeholder('-')
                    ->icon('heroicon-o-calendar')
                    ->label(__('time-off::filament/clusters/configurations/resources/mandatory-days.infolist.entries.end-date')),
            ]);
    }
}
