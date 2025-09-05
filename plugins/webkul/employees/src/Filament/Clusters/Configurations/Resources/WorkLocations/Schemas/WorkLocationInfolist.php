<?php

namespace Webkul\Employee\Filament\Clusters\Configurations\Resources\WorkLocations\Schemas;

use Filament\Infolists\Components\IconEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Schema;

class WorkLocationInfoList
{
    public static function configure(Schema $schema)
    {
        return $schema
            ->components([
                TextEntry::make('name')
                    ->icon('heroicon-o-map')
                    ->placeholder('—')
                    ->label(__('employees::filament/clusters/configurations/resources/work-location.infolist.name')),
                TextEntry::make('location_type')
                    ->icon('heroicon-o-map')
                    ->placeholder('—')
                    ->label('Location Type')
                    ->label(__('employees::filament/clusters/configurations/resources/work-location.infolist.location-type')),
                TextEntry::make('location_number')
                    ->placeholder('—')
                    ->icon('heroicon-o-map')
                    ->label(__('employees::filament/clusters/configurations/resources/work-location.infolist.location-number')),
                TextEntry::make('company.name')
                    ->placeholder('—')
                    ->icon('heroicon-o-building-office')
                    ->label(__('employees::filament/clusters/configurations/resources/work-location.infolist.company')),
                IconEntry::make('is_active')
                    ->boolean()
                    ->label(__('employees::filament/clusters/configurations/resources/work-location.infolist.status')),
            ]);
    }
}
