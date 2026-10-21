<?php

namespace Webkul\PointOfSale\Filament\Admin\Clusters\Configurations\Resources\CategoryResource\Schemas;

use Filament\Infolists\Components\ColorEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class CategoryInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make(__('point-of-sale::filament/admin/clusters/configurations/resources/category.infolist.sections.general.title'))
                    ->schema([
                        TextEntry::make('name')
                            ->label(__('point-of-sale::filament/admin/clusters/configurations/resources/category.infolist.sections.general.entries.name'))
                            ->placeholder('—'),

                        TextEntry::make('parent.name')
                            ->label(__('point-of-sale::filament/admin/clusters/configurations/resources/category.infolist.sections.general.entries.parent-name'))
                            ->placeholder('—'),

                        ColorEntry::make('color')
                            ->label(__('point-of-sale::filament/admin/clusters/configurations/resources/category.infolist.sections.general.entries.color'))
                            ->placeholder('—'),
                    ])
                    ->columns(2),
            ]);
    }
}
