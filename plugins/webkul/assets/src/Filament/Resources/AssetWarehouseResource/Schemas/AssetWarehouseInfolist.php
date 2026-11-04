<?php

namespace Webkul\Asset\Filament\Resources\AssetWarehouseResource\Schemas;

use Filament\Infolists\Components\IconEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Group;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Support\Enums\FontWeight;
use Filament\Support\Enums\TextSize;

class AssetWarehouseInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Group::make()
                    ->schema([
                        Section::make(__('assets::filament/resources/asset-warehouse.form.sections.general.title'))
                            ->schema([
                                TextEntry::make('name')
                                    ->label(__('assets::filament/resources/asset-warehouse.form.sections.general.fields.name'))
                                    ->size(TextSize::Large)
                                    ->weight(FontWeight::Bold)
                                    ->columnSpanFull(),

                                TextEntry::make('code')
                                    ->label(__('assets::filament/resources/asset-warehouse.form.sections.general.fields.code'))
                                    ->icon('heroicon-m-hashtag')
                                    ->placeholder('—'),

                                TextEntry::make('custodian.name')
                                    ->label(__('assets::filament/resources/asset-warehouse.form.sections.settings.fields.custodian'))
                                    ->icon('heroicon-o-user')
                                    ->placeholder('—'),

                                IconEntry::make('is_active')
                                    ->label(__('assets::filament/resources/asset-warehouse.form.sections.settings.fields.is-active'))
                                    ->boolean()
                                    ->columnSpanFull(),

                                TextEntry::make('note')
                                    ->label(__('assets::filament/resources/asset-warehouse.form.sections.general.fields.note'))
                                    ->placeholder('—')
                                    ->columnSpanFull(),
                            ])
                            ->columns(2),
                    ])
                    ->columnSpan(['lg' => 2]),

                Group::make()
                    ->schema([
                        Section::make(__('assets::filament/resources/asset-warehouse.form.sections.settings.title'))
                            ->schema([
                                TextEntry::make('company.name')
                                    ->label(__('assets::filament/resources/asset-warehouse.form.sections.settings.fields.company'))
                                    ->icon('heroicon-o-building-office'),
                            ]),

                        Section::make(__('assets::filament/resources/asset-warehouse.infolist.sections.audit.title'))
                            ->schema([
                                TextEntry::make('creator.name')
                                    ->label(__('assets::filament/resources/asset-warehouse.infolist.sections.audit.entries.creator'))
                                    ->icon('heroicon-m-user')
                                    ->placeholder('—'),

                                TextEntry::make('created_at')
                                    ->label(__('assets::filament/resources/asset-warehouse.infolist.sections.audit.entries.created-at'))
                                    ->dateTime()
                                    ->icon('heroicon-m-calendar'),

                                TextEntry::make('updated_at')
                                    ->label(__('assets::filament/resources/asset-warehouse.infolist.sections.audit.entries.updated-at'))
                                    ->dateTime()
                                    ->icon('heroicon-m-calendar-days'),
                            ]),
                    ])
                    ->columnSpan(['lg' => 1]),
            ])
            ->columns(3);
    }
}
