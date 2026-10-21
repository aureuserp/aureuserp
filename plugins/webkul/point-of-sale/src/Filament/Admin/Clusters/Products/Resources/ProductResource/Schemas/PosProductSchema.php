<?php

namespace Webkul\PointOfSale\Filament\Admin\Clusters\Products\Resources\ProductResource\Schemas;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\Toggle;
use Filament\Infolists\Components\IconEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Utilities\Get;

class PosProductSchema
{
    public static function formSection(): Section
    {
        return Section::make(__('point-of-sale::filament/admin/clusters/products/resources/product.form.sections.point-of-sale.title'))
            ->schema([
                Toggle::make('available_in_pos')
                    ->label(__('point-of-sale::filament/admin/clusters/products/resources/product.form.sections.point-of-sale.fields.available-in-pos'))
                    ->helperText(__('point-of-sale::filament/admin/clusters/products/resources/product.form.sections.point-of-sale.fields.available-in-pos-helper'))
                    ->live(),

                Select::make('pos_categories')
                    ->label(__('point-of-sale::filament/admin/clusters/products/resources/product.form.sections.point-of-sale.fields.categories'))
                    ->relationship('posCategories', 'name')
                    ->multiple()
                    ->searchable()
                    ->preload()
                    ->visible(fn (Get $get): bool => (bool) $get('available_in_pos')),
            ]);
    }

    public static function infolistSection(): Section
    {
        return Section::make(__('point-of-sale::filament/admin/clusters/products/resources/product.infolist.sections.point-of-sale.title'))
            ->schema([
                Grid::make(2)
                    ->schema([
                        IconEntry::make('available_in_pos')
                            ->label(__('point-of-sale::filament/admin/clusters/products/resources/product.infolist.sections.point-of-sale.entries.available-in-pos'))
                            ->boolean(),

                        TextEntry::make('posCategories.name')
                            ->label(__('point-of-sale::filament/admin/clusters/products/resources/product.infolist.sections.point-of-sale.entries.categories'))
                            ->icon('heroicon-o-squares-2x2')
                            ->listWithLineBreaks()
                            ->placeholder('—'),
                    ]),
            ]);
    }
}
