<?php

namespace Webkul\Product\Filament\Resources\Packaging\Schemas;

use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Support\Enums\FontWeight;
use Filament\Support\Enums\TextSize;

class PackagingInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make(__('products::filament/resources/packaging.infolist.sections.general.title'))
                    ->schema([
                        TextEntry::make('name')
                            ->label(__('products::filament/resources/packaging.infolist.sections.general.entries.name'))
                            ->weight(FontWeight::Bold)
                            ->size(TextSize::Large)
                            ->columnSpan(2)
                            ->icon('heroicon-o-gift'),

                        TextEntry::make('barcode')
                            ->label(__('products::filament/resources/packaging.infolist.sections.general.entries.barcode'))
                            ->icon('heroicon-o-bars-4')
                            ->placeholder('—'),

                        TextEntry::make('product.name')
                            ->label(__('products::filament/resources/packaging.infolist.sections.general.entries.product'))
                            ->icon('heroicon-o-cube')
                            ->placeholder('—'),

                        TextEntry::make('qty')
                            ->label(__('products::filament/resources/packaging.infolist.sections.general.entries.qty'))
                            ->icon('heroicon-o-scale')
                            ->placeholder('—'),
                    ])
                    ->columns(2),

                Section::make(__('products::filament/resources/packaging.infolist.sections.organization.title'))
                    ->schema([
                        TextEntry::make('company.name')
                            ->label(__('products::filament/resources/packaging.infolist.sections.organization.entries.company'))
                            ->icon('heroicon-o-building-office')
                            ->placeholder('—'),

                        TextEntry::make('creator.name')
                            ->label(__('products::filament/resources/packaging.infolist.sections.organization.entries.creator'))
                            ->icon('heroicon-o-user')
                            ->placeholder('—'),

                        TextEntry::make('created_at')
                            ->label(__('products::filament/resources/packaging.infolist.sections.organization.entries.created_at'))
                            ->dateTime()
                            ->icon('heroicon-o-calendar')
                            ->placeholder('—'),

                        TextEntry::make('updated_at')
                            ->label(__('products::filament/resources/packaging.infolist.sections.organization.entries.updated_at'))
                            ->dateTime()
                            ->icon('heroicon-o-clock')
                            ->placeholder('—'),
                    ])
                    ->collapsible()
                    ->columns(2),
            ]);
    }
}
