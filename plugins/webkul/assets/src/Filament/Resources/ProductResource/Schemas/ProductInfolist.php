<?php

namespace Webkul\Asset\Filament\Resources\ProductResource\Schemas;

use Filament\Infolists\Components\IconEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Group;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Support\Enums\FontWeight;
use Filament\Support\Enums\TextSize;

class ProductInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Group::make()
                    ->schema([
                        Section::make(__('assets::filament/resources/product.infolist.sections.general.title'))
                            ->schema([
                                TextEntry::make('name')
                                    ->label(__('assets::filament/resources/product.infolist.sections.general.entries.name'))
                                    ->size(TextSize::Large)
                                    ->weight(FontWeight::Bold)
                                    ->columnSpanFull(),

                                TextEntry::make('type')
                                    ->label(__('assets::filament/resources/product.infolist.sections.general.entries.type'))
                                    ->badge(),

                                TextEntry::make('category.full_name')
                                    ->label(__('assets::filament/resources/product.infolist.sections.general.entries.category'))
                                    ->badge()
                                    ->placeholder('—'),

                                TextEntry::make('model')
                                    ->label(__('assets::filament/resources/product.infolist.sections.general.entries.model'))
                                    ->placeholder('—'),

                                TextEntry::make('cost')
                                    ->label(__('assets::filament/resources/product.infolist.sections.general.entries.cost'))
                                    ->numeric(decimalPlaces: 2)
                                    ->placeholder('0.00'),

                                IconEntry::make('is_active')
                                    ->label(__('assets::filament/resources/product.infolist.sections.general.entries.is-active'))
                                    ->boolean(),

                                TextEntry::make('note')
                                    ->label(__('assets::filament/resources/product.infolist.sections.general.entries.note'))
                                    ->placeholder('—')
                                    ->columnSpanFull(),
                            ])
                            ->columns(2),
                    ])
                    ->columnSpan(['lg' => 2]),

                Group::make()
                    ->schema([
                        Section::make(__('assets::filament/resources/product.infolist.sections.settings.title'))
                            ->schema([
                                TextEntry::make('company.name')
                                    ->label(__('assets::filament/resources/product.infolist.sections.settings.entries.company'))
                                    ->icon('heroicon-o-building-office'),
                            ]),

                        Section::make(__('assets::filament/resources/product.infolist.sections.audit.title'))
                            ->schema([
                                TextEntry::make('creator.name')
                                    ->label(__('assets::filament/resources/product.infolist.sections.audit.entries.creator'))
                                    ->icon('heroicon-m-user')
                                    ->placeholder('—'),

                                TextEntry::make('created_at')
                                    ->label(__('assets::filament/resources/product.infolist.sections.audit.entries.created-at'))
                                    ->dateTime()
                                    ->icon('heroicon-m-calendar'),

                                TextEntry::make('updated_at')
                                    ->label(__('assets::filament/resources/product.infolist.sections.audit.entries.updated-at'))
                                    ->dateTime()
                                    ->icon('heroicon-m-calendar-days'),
                            ]),
                    ])
                    ->columnSpan(['lg' => 1]),
            ])
            ->columns(3);
    }
}
