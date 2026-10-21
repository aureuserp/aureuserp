<?php

namespace Webkul\PointOfSale\Filament\Admin\Clusters\Configurations\Resources\FloorResource\Schemas;

use Filament\Infolists\Components\ColorEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class FloorInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make(__('point-of-sale::filament/admin/clusters/configurations/resources/floor.infolist.sections.general.title'))
                    ->schema([
                        TextEntry::make('name')
                            ->label(__('point-of-sale::filament/admin/clusters/configurations/resources/floor.infolist.sections.general.entries.name'))
                            ->placeholder('—'),

                        TextEntry::make('configs.name')
                            ->label(__('point-of-sale::filament/admin/clusters/configurations/resources/floor.infolist.sections.general.entries.configs'))
                            ->badge()
                            ->placeholder('—'),
                            

                        ColorEntry::make('background_color')
                            ->label(__('point-of-sale::filament/admin/clusters/configurations/resources/floor.infolist.sections.general.entries.background-color'))
                            ->placeholder('—'),
                    ])
                    ->columns(2),
            ]);
    }
}
