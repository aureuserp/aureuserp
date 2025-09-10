<?php

namespace Webkul\Product\Filament\Resources\Attributes\Schemas;

use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Support\Enums\FontWeight;
use Filament\Support\Enums\TextSize;

class AttributeInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                \Filament\Schemas\Components\Group::make()
                    ->schema([
                        Section::make(__('products::filament/resources/attribute.infolist.sections.general.title'))
                            ->schema([
                                TextEntry::make('name')
                                    ->label(__('products::filament/resources/attribute.infolist.sections.general.entries.name'))
                                    ->size(TextSize::Large)
                                    ->weight(FontWeight::Bold),

                                TextEntry::make('type')
                                    ->label(__('products::filament/resources/attribute.infolist.sections.general.entries.type'))
                                    ->placeholder('—'),
                            ]),
                    ])
                    ->columnSpan(['lg' => 2]),

                \Filament\Schemas\Components\Group::make()
                    ->schema([
                        Section::make(__('products::filament/resources/attribute.infolist.sections.record-information.title'))
                            ->schema([
                                TextEntry::make('creator.name')
                                    ->label(__('products::filament/resources/attribute.infolist.sections.record-information.entries.creator'))
                                    ->icon('heroicon-o-user')
                                    ->placeholder('—'),

                                TextEntry::make('created_at')
                                    ->label(__('products::filament/resources/attribute.infolist.sections.record-information.entries.created_at'))
                                    ->dateTime()
                                    ->icon('heroicon-o-calendar')
                                    ->placeholder('—'),

                                TextEntry::make('updated_at')
                                    ->label(__('products::filament/resources/attribute.infolist.sections.record-information.entries.updated_at'))
                                    ->dateTime()
                                    ->icon('heroicon-o-clock')
                                    ->placeholder('—'),
                            ])
                            ->icon('heroicon-o-information-circle')
                            ->collapsible(),
                    ])
                    ->columnSpan(['lg' => 1]),
            ])
            ->columns(3);
    }
}
