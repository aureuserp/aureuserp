<?php

namespace Webkul\Maintenance\Filament\Clusters\Configurations\Resources\EquipmentCategoryResource\Schemas;

use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class EquipmentCategoryInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make(__('maintenance::filament/clusters/configurations/resources/equipment-category.infolist.sections.general.title'))
                    ->schema([
                        TextEntry::make('name')
                            ->label(__('maintenance::filament/clusters/configurations/resources/equipment-category.infolist.sections.general.entries.name'))
                            ->placeholder('—'),

                        TextEntry::make('technician.name')
                            ->label(__('maintenance::filament/clusters/configurations/resources/equipment-category.infolist.sections.general.entries.technician'))
                            ->placeholder('—'),

                        TextEntry::make('company.name')
                            ->label(__('maintenance::filament/clusters/configurations/resources/equipment-category.infolist.sections.general.entries.company'))
                            ->placeholder('—'),

                        TextEntry::make('note')
                            ->label(__('maintenance::filament/clusters/configurations/resources/equipment-category.infolist.sections.general.entries.note'))
                            ->placeholder('—')
                            ->columnSpanFull(),
                    ])
                    ->columns(2)
                    ->columnSpanFull(),
            ]);
    }
}
