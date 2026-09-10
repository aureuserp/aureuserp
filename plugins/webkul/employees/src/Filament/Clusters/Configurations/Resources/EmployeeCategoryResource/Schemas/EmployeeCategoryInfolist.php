<?php

namespace Webkul\Employee\Filament\Clusters\Configurations\Resources\EmployeeCategoryResource\Schemas;

use Filament\Infolists\Components\ColorEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Schema;

class EmployeeCategoryInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextEntry::make('name')
                    ->placeholder('—')
                    ->icon('heroicon-o-tag')
                    ->label(__('employees::filament/clusters/configurations/resources/employee-category.infolist.name')),
                ColorEntry::make('color')
                    ->placeholder('—')
                    ->label(__('employees::filament/clusters/configurations/resources/employee-category.infolist.color')),
            ]);
    }
}
