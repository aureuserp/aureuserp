<?php

namespace Webkul\Asset\Filament\Resources\AssetCategoryResource\Schemas;

use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Group;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Support\Enums\FontWeight;
use Filament\Support\Enums\TextSize;
use Webkul\Asset\Models\AssetCategory;

class AssetCategoryInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Group::make()
                    ->schema([
                        Section::make(__('assets::filament/resources/category.infolist.sections.general.title'))
                            ->schema([
                                TextEntry::make('name')
                                    ->label(__('assets::filament/resources/category.infolist.sections.general.entries.name'))
                                    ->size(TextSize::Large)
                                    ->weight(FontWeight::Bold)
                                    ->icon('heroicon-o-tag')
                                    ->columnSpanFull(),

                                TextEntry::make('full_name')
                                    ->label(__('assets::filament/resources/category.infolist.sections.general.entries.full-name'))
                                    ->placeholder('—'),

                                TextEntry::make('parent.full_name')
                                    ->label(__('assets::filament/resources/category.infolist.sections.general.entries.parent'))
                                    ->placeholder('—')
                                    ->icon('heroicon-o-folder'),

                                TextEntry::make('products_count')
                                    ->label(__('assets::filament/resources/category.infolist.sections.general.entries.products-count'))
                                    ->getStateUsing(fn (AssetCategory $record): int => $record->products()->count())
                                    ->badge()
                                    ->placeholder('0'),

                                TextEntry::make('assets_count')
                                    ->label(__('assets::filament/resources/category.infolist.sections.general.entries.assets-count'))
                                    ->getStateUsing(fn (AssetCategory $record): int => $record->assets()->count())
                                    ->badge()
                                    ->color('warning')
                                    ->placeholder('0'),

                                TextEntry::make('assets_sum_cost')
                                    ->label(__('assets::filament/resources/category.infolist.sections.general.entries.total-cost'))
                                    ->getStateUsing(fn (AssetCategory $record): float => (float) $record->assets()->sum('assets.cost'))
                                    ->numeric(decimalPlaces: 2)
                                    ->placeholder('0.00'),
                            ])
                            ->columns(2),
                    ])
                    ->columnSpan(['lg' => 2]),

                Group::make()
                    ->schema([
                        Section::make(__('assets::filament/resources/category.form.sections.settings.title'))
                            ->schema([
                                TextEntry::make('company.name')
                                    ->label(__('assets::filament/resources/category.form.sections.settings.fields.company'))
                                    ->icon('heroicon-o-building-office'),
                            ]),

                        Section::make(__('assets::filament/resources/category.infolist.sections.audit.title'))
                            ->schema([
                                TextEntry::make('creator.name')
                                    ->label(__('assets::filament/resources/category.infolist.sections.audit.entries.creator'))
                                    ->icon('heroicon-m-user')
                                    ->placeholder('—'),

                                TextEntry::make('created_at')
                                    ->label(__('assets::filament/resources/category.infolist.sections.audit.entries.created-at'))
                                    ->dateTime()
                                    ->icon('heroicon-m-calendar'),

                                TextEntry::make('updated_at')
                                    ->label(__('assets::filament/resources/category.infolist.sections.audit.entries.updated-at'))
                                    ->dateTime()
                                    ->icon('heroicon-m-calendar-days'),
                            ]),
                    ])
                    ->columnSpan(['lg' => 1]),
            ])
            ->columns(3);
    }
}
