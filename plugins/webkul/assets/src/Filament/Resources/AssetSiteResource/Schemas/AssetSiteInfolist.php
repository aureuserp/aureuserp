<?php

namespace Webkul\Asset\Filament\Resources\AssetSiteResource\Schemas;

use Filament\Infolists\Components\IconEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Group;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Support\Colors\Color;
use Filament\Support\Enums\FontWeight;
use Filament\Support\Enums\TextSize;
use Webkul\Asset\Models\AssetSite;

class AssetSiteInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Group::make()
                    ->schema([
                        Section::make(__('assets::filament/resources/asset-site.form.sections.general.title'))
                            ->schema([
                                TextEntry::make('name')
                                    ->label(__('assets::filament/resources/asset-site.form.sections.general.fields.name'))
                                    ->size(TextSize::Large)
                                    ->weight(FontWeight::Bold)
                                    ->icon('heroicon-o-map-pin')
                                    ->columnSpanFull(),

                                TextEntry::make('full_name')
                                    ->label(__('assets::filament/resources/asset-site.infolist.sections.general.entries.full-name'))
                                    ->placeholder('—'),

                                TextEntry::make('parent.full_name')
                                    ->label(__('assets::filament/resources/asset-site.infolist.sections.general.entries.parent'))
                                    ->placeholder('—')
                                    ->icon('heroicon-o-map-pin'),

                                TextEntry::make('usage_type')
                                    ->label(__('assets::filament/resources/asset-site.infolist.sections.general.entries.usage-type'))
                                    ->badge(),

                                TextEntry::make('code')
                                    ->label(__('assets::filament/resources/asset-site.form.sections.general.fields.code'))
                                    ->icon('heroicon-m-hashtag')
                                    ->placeholder('—'),

                                TextEntry::make('siteType.name')
                                    ->label(__('assets::filament/resources/asset-site.form.sections.general.fields.type'))
                                    ->badge()
                                    ->icon(fn (AssetSite $record): ?string => $record->siteType?->icon)
                                    ->color(fn (AssetSite $record): string|array => filled($record->siteType?->color) ? Color::hex($record->siteType->color) : 'gray'),

                                TextEntry::make('defaultCustodian.name')
                                    ->label(__('assets::filament/resources/asset-site.form.sections.settings.fields.default-custodian'))
                                    ->placeholder('—')
                                    ->icon('heroicon-o-user'),

                                IconEntry::make('is_active')
                                    ->label(__('assets::filament/resources/asset-site.form.sections.settings.fields.is-active'))
                                    ->boolean(),

                                TextEntry::make('note')
                                    ->label(__('assets::filament/resources/asset-site.form.sections.general.fields.note'))
                                    ->placeholder('—')
                                    ->columnSpanFull(),
                            ])
                            ->columns(2),
                    ])
                    ->columnSpan(['lg' => 2]),

                Group::make()
                    ->schema([
                        Section::make(__('assets::filament/resources/asset-site.form.sections.settings.title'))
                            ->schema([
                                TextEntry::make('company.name')
                                    ->label(__('assets::filament/resources/asset-site.form.sections.settings.fields.company'))
                                    ->icon('heroicon-o-building-office'),
                            ]),

                        Section::make(__('assets::filament/resources/asset-site.infolist.sections.audit.title'))
                            ->schema([
                                TextEntry::make('creator.name')
                                    ->label(__('assets::filament/resources/asset-site.infolist.sections.audit.entries.creator'))
                                    ->icon('heroicon-m-user')
                                    ->placeholder('—'),

                                TextEntry::make('created_at')
                                    ->label(__('assets::filament/resources/asset-site.infolist.sections.audit.entries.created-at'))
                                    ->dateTime()
                                    ->icon('heroicon-m-calendar'),

                                TextEntry::make('updated_at')
                                    ->label(__('assets::filament/resources/asset-site.infolist.sections.audit.entries.updated-at'))
                                    ->dateTime()
                                    ->icon('heroicon-m-calendar-days'),
                            ]),
                    ])
                    ->columnSpan(['lg' => 1]),
            ])
            ->columns(3);
    }
}
