<?php

namespace Webkul\Asset\Filament\Resources\AssetResource\Schemas;

use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Group;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Support\Enums\FontWeight;
use Filament\Support\Enums\TextSize;
use Webkul\Asset\Filament\Resources\ProductResource;
use Webkul\Asset\Models\Asset;

class AssetInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Group::make()
                    ->schema([
                        Section::make(__('assets::filament/resources/asset.form.sections.general.title'))
                            ->schema([
                                TextEntry::make('product.name')
                                    ->label(__('assets::filament/resources/asset.form.sections.general.fields.product'))
                                    ->size(TextSize::Large)
                                    ->weight(FontWeight::Bold)
                                    ->url(fn (Asset $record): ?string => $record->product_id ? ProductResource::getUrl('view', ['record' => $record->product_id]) : null)
                                    ->columnSpanFull(),

                                TextEntry::make('product.type')
                                    ->label(__('assets::filament/resources/asset.form.sections.general.fields.product-type'))
                                    ->badge(),

                                TextEntry::make('initial_quantity')
                                    ->label(__('assets::filament/resources/asset.infolist.sections.custody.entries.initial-quantity'))
                                    ->numeric(decimalPlaces: 0)
                                    ->visible(fn (Asset $record): bool => (bool) $record->product?->isConsumable()),

                                TextEntry::make('quantity')
                                    ->label(__('assets::filament/resources/asset.infolist.sections.custody.entries.available-quantity'))
                                    ->numeric(decimalPlaces: 0)
                                    ->visible(fn (Asset $record): bool => (bool) $record->product?->isConsumable()),

                                TextEntry::make('asset_tag')
                                    ->label(__('assets::filament/resources/asset.form.sections.general.fields.asset-tag'))
                                    ->icon('heroicon-o-qr-code')
                                    ->weight(FontWeight::Bold)
                                    ->copyable(),

                                TextEntry::make('serial_no')
                                    ->label(__('assets::filament/resources/asset.form.sections.general.fields.serial-no'))
                                    ->visible(fn (Asset $record): bool => ! (bool) $record->product?->isConsumable())
                                    ->placeholder('—'),

                                TextEntry::make('product.model')
                                    ->label(__('assets::filament/resources/asset.form.sections.general.fields.model'))
                                    ->placeholder('—'),

                                TextEntry::make('product.category.full_name')
                                    ->label(__('assets::filament/resources/asset.form.sections.general.fields.category'))
                                    ->badge()
                                    ->placeholder('—'),

                                TextEntry::make('condition')
                                    ->label(__('assets::filament/resources/asset.form.sections.general.fields.condition'))
                                    ->badge()
                                    ->visible(fn (Asset $record): bool => ! (bool) $record->product?->isConsumable()),
                            ])
                            ->columns(2),

                        Section::make(__('assets::filament/resources/asset.infolist.sections.acquisition.title'))
                            ->schema([
                                TextEntry::make('purchase_date')
                                    ->label(__('assets::filament/resources/asset.form.sections.general.fields.purchase-date'))
                                    ->date()
                                    ->placeholder('—'),

                                TextEntry::make('warranty_until')
                                    ->label(__('assets::filament/resources/asset.form.sections.general.fields.warranty-until'))
                                    ->date()
                                    ->visible(fn (Asset $record): bool => ! (bool) $record->product?->isConsumable())
                                    ->placeholder('—'),

                                TextEntry::make('cost')
                                    ->label(__('assets::filament/resources/asset.form.sections.general.fields.cost'))
                                    ->numeric(decimalPlaces: 2)
                                    ->placeholder('0.00'),
                            ])
                            ->columns(3),

                        Section::make(__('assets::filament/resources/asset.infolist.sections.custody.title'))
                            ->schema([
                                TextEntry::make('status')
                                    ->label(__('assets::filament/resources/asset.infolist.sections.identity.entries.status'))
                                    ->badge(),

                                TextEntry::make('warehouse.name')
                                    ->label(__('assets::filament/resources/asset.infolist.sections.custody.entries.warehouse'))
                                    ->icon('heroicon-o-building-storefront')
                                    ->placeholder('—'),

                                TextEntry::make('currentEmployee.name')
                                    ->label(__('assets::filament/resources/asset.infolist.sections.custody.entries.current-employee'))
                                    ->icon('heroicon-o-user')
                                    ->placeholder('—'),

                                TextEntry::make('currentSite.full_name')
                                    ->label(__('assets::filament/resources/asset.infolist.sections.custody.entries.current-site'))
                                    ->icon('heroicon-o-map-pin')
                                    ->placeholder('—'),

                                TextEntry::make('currentSite.usage_type')
                                    ->label(__('assets::filament/resources/asset.infolist.sections.custody.entries.site-usage'))
                                    ->badge()
                                    ->placeholder('—'),

                                TextEntry::make('issued_at')
                                    ->label(__('assets::filament/resources/asset.infolist.sections.custody.entries.issued-at'))
                                    ->dateTime()
                                    ->placeholder('—'),

                                TextEntry::make('expected_return_at')
                                    ->label(__('assets::filament/resources/asset.infolist.sections.custody.entries.expected-return-at'))
                                    ->date()
                                    ->visible(fn (Asset $record): bool => ! (bool) $record->product?->isConsumable())
                                    ->placeholder('—'),

                                TextEntry::make('custody_note')
                                    ->label(__('assets::filament/resources/asset.infolist.sections.custody.entries.custody-note'))
                                    ->placeholder('—')
                                    ->columnSpanFull(),
                            ])
                            ->columns(2),

                        Section::make(__('assets::filament/resources/asset.infolist.sections.notes.title'))
                            ->schema([
                                TextEntry::make('note')
                                    ->label(__('assets::filament/resources/asset.form.sections.general.fields.note'))
                                    ->placeholder('—')
                                    ->columnSpanFull(),
                            ]),
                    ])
                    ->columnSpan(['lg' => 2]),

                Group::make()
                    ->schema([
                        Section::make(__('assets::filament/resources/asset.form.sections.settings.title'))
                            ->schema([
                                TextEntry::make('company.name')
                                    ->label(__('assets::filament/resources/asset.form.sections.settings.fields.company'))
                                    ->icon('heroicon-o-building-office'),
                            ]),

                        Section::make(__('assets::filament/resources/asset.infolist.sections.audit.title'))
                            ->schema([
                                TextEntry::make('creator.name')
                                    ->label(__('assets::filament/resources/asset.infolist.sections.acquisition.entries.creator'))
                                    ->icon('heroicon-m-user')
                                    ->placeholder('—'),

                                TextEntry::make('created_at')
                                    ->label(__('assets::filament/resources/asset.infolist.sections.audit.entries.created-at'))
                                    ->dateTime()
                                    ->icon('heroicon-m-calendar'),

                                TextEntry::make('updated_at')
                                    ->label(__('assets::filament/resources/asset.infolist.sections.audit.entries.updated-at'))
                                    ->dateTime()
                                    ->icon('heroicon-m-calendar-days'),
                            ]),
                    ])
                    ->columnSpan(['lg' => 1]),
            ])
            ->columns(3);
    }
}
