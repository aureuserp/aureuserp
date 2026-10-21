<?php

namespace Webkul\PointOfSale\Filament\Admin\Clusters\Configurations\Resources\ConfigResource\Schemas;

use Filament\Infolists\Components\IconEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class ConfigInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make(__('point-of-sale::filament/admin/clusters/configurations/resources/config.infolist.sections.general.title'))
                    ->schema([
                        TextEntry::make('name')
                            ->label(__('point-of-sale::filament/admin/clusters/configurations/resources/config.infolist.sections.general.entries.name'))
                            ->placeholder('—'),

                        TextEntry::make('code')
                            ->label(__('point-of-sale::filament/admin/clusters/configurations/resources/config.infolist.sections.general.entries.code'))
                            ->placeholder('—'),

                        TextEntry::make('company.name')
                            ->label(__('point-of-sale::filament/admin/clusters/configurations/resources/config.infolist.sections.general.entries.company-name'))
                            ->placeholder('—'),

                        TextEntry::make('warehouse.name')
                            ->label(__('point-of-sale::filament/admin/clusters/configurations/resources/config.infolist.sections.general.entries.warehouse-name'))
                            ->placeholder('—'),

                        TextEntry::make('journal.name')
                            ->label(__('point-of-sale::filament/admin/clusters/configurations/resources/config.infolist.sections.general.entries.journal-name'))
                            ->placeholder('—'),

                        IconEntry::make('is_restaurant')
                            ->label(__('point-of-sale::filament/admin/clusters/configurations/resources/config.infolist.sections.general.entries.is-restaurant'))
                            ->boolean(),

                        IconEntry::make('is_active')
                            ->label(__('point-of-sale::filament/admin/clusters/configurations/resources/config.infolist.sections.general.entries.is-active'))
                            ->boolean(),
                    ])
                    ->columns(2),
            ]);
    }
}
