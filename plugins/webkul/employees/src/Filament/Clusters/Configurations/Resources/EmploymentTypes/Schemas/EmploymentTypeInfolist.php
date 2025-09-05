<?php

namespace Webkul\Employee\Filament\Clusters\Configurations\Resources\EmploymentTypes\Schemas;

use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Schema;

class EmploymentTypeInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextEntry::make('name')
                    ->icon('heroicon-o-user')
                    ->placeholder('—')
                    ->label(__('employees::filament/clusters/configurations/resources/employment-type.infolist.entries.name')),
                TextEntry::make('code')
                    ->placeholder('—')
                    ->icon('heroicon-o-user')
                    ->label(__('employees::filament/clusters/configurations/resources/employment-type.infolist.entries.code')),
                TextEntry::make('country.name')
                    ->placeholder('—')
                    ->icon('heroicon-o-map')
                    ->label(__('employees::filament/clusters/configurations/resources/employment-type.infolist.entries.country')),
            ]);
    }
}
