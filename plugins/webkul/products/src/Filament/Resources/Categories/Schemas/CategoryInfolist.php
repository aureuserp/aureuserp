<?php

namespace Webkul\Product\Filament\Resources\Categories\Schemas;

use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Group;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Support\Enums\FontWeight;
use Filament\Support\Enums\TextSize;

class CategoryInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Group::make()
                    ->schema([
                        Section::make(__('products::filament/resources/category.infolist.sections.general.title'))
                            ->schema([
                                TextEntry::make('name')
                                    ->label(__('products::filament/resources/category.infolist.sections.general.entries.name'))
                                    ->weight(FontWeight::Bold)
                                    ->size(TextSize::Large)
                                    ->icon('heroicon-o-document-text'),

                                TextEntry::make('parent.name')
                                    ->label(__('products::filament/resources/category.infolist.sections.general.entries.parent'))
                                    ->icon('heroicon-o-folder')
                                    ->placeholder('—'),

                                TextEntry::make('full_name')
                                    ->label(__('products::filament/resources/category.infolist.sections.general.entries.full_name'))
                                    ->icon('heroicon-o-folder-open')
                                    ->placeholder('—'),

                                TextEntry::make('parent_path')
                                    ->label(__('products::filament/resources/category.infolist.sections.general.entries.parent_path'))
                                    ->icon('heroicon-o-arrows-right-left')
                                    ->placeholder('—'),
                            ]),
                    ])
                    ->columnSpan(['lg' => 2]),

                Group::make()
                    ->schema([
                        Section::make(__('products::filament/resources/category.infolist.sections.record-information.title'))
                            ->schema([
                                TextEntry::make('creator.name')
                                    ->label(__('products::filament/resources/category.infolist.sections.record-information.entries.creator'))
                                    ->icon('heroicon-o-user')
                                    ->placeholder('—'),

                                TextEntry::make('created_at')
                                    ->label(__('products::filament/resources/category.infolist.sections.record-information.entries.created_at'))
                                    ->dateTime()
                                    ->icon('heroicon-o-calendar')
                                    ->placeholder('—'),

                                TextEntry::make('updated_at')
                                    ->label(__('products::filament/resources/category.infolist.sections.record-information.entries.updated_at'))
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
