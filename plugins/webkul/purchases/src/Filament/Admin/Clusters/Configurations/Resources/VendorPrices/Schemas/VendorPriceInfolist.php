<?php

namespace Webkul\Purchase\Filament\Admin\Clusters\Configurations\Resources\VendorPrices\Schemas;

use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Group;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class VendorPriceInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Group::make()
                    ->schema([
                        Section::make(__('purchases::filament/admin/clusters/configurations/resources/vendor-price.infolist.sections.general.entries'))
                            ->icon('heroicon-o-information-circle')
                            ->schema([
                                TextEntry::make('partner.name')
                                    ->label(__('purchases::filament/admin/clusters/configurations/resources/vendor-price.infolist.sections.general.entries.vendor'))
                                    ->icon('heroicon-o-user-group'),

                                TextEntry::make('product_name')
                                    ->label(__('purchases::filament/admin/clusters/configurations/resources/vendor-price.infolist.sections.general.entries.vendor-product-name'))
                                    ->icon('heroicon-o-tag')
                                    ->placeholder('—'),

                                TextEntry::make('product_code')
                                    ->label(__('purchases::filament/admin/clusters/configurations/resources/vendor-price.infolist.sections.general.entries.vendor-product-code'))
                                    ->placeholder('—'),

                                TextEntry::make('delay')
                                    ->label(__('purchases::filament/admin/clusters/configurations/resources/vendor-price.infolist.sections.general.entries.delay'))
                                    ->icon('heroicon-o-clock')
                                    ->suffix(' days'),
                            ]),
                    ])
                    ->columnSpan(['lg' => 2]),

                Group::make()
                    ->schema([
                        Section::make(__('purchases::filament/admin/clusters/configurations/resources/vendor-price.infolist.sections.prices.entries'))
                            ->icon('heroicon-o-currency-dollar')
                            ->schema([
                                TextEntry::make('product.name')
                                    ->label(__('purchases::filament/admin/clusters/configurations/resources/vendor-price.infolist.sections.prices.entries.product'))
                                    ->icon('heroicon-o-cube'),

                                TextEntry::make('min_qty')
                                    ->label(__('purchases::filament/admin/clusters/configurations/resources/vendor-price.infolist.sections.prices.entries.quantity'))
                                    ->icon('heroicon-o-calculator'),
                                Group::make()
                                    ->schema([
                                        TextEntry::make('price')
                                            ->label(__('purchases::filament/admin/clusters/configurations/resources/vendor-price.infolist.sections.prices.entries.unit-price'))
                                            ->icon('heroicon-o-banknotes')
                                            ->money(fn ($record) => $record->currency->code ?? 'USD'),

                                        TextEntry::make('currency.name')
                                            ->label(__('purchases::filament/admin/clusters/configurations/resources/vendor-price.infolist.sections.prices.entries.currency'))
                                            ->icon('heroicon-o-globe-alt'),

                                        TextEntry::make('starts_at')
                                            ->label(__('purchases::filament/admin/clusters/configurations/resources/vendor-price.infolist.sections.prices.entries.valid-from'))
                                            ->icon('heroicon-o-calendar')
                                            ->date()
                                            ->placeholder('—'),

                                        TextEntry::make('ends_at')
                                            ->label(__('purchases::filament/admin/clusters/configurations/resources/vendor-price.infolist.sections.prices.entries.valid-to'))
                                            ->icon('heroicon-o-calendar')
                                            ->date()
                                            ->placeholder('—'),
                                    ])
                                    ->columns(2),

                                TextEntry::make('discount')
                                    ->label(__('purchases::filament/admin/clusters/configurations/resources/vendor-price.infolist.sections.prices.entries.discount'))
                                    ->icon('heroicon-o-gift')
                                    ->suffix('%'),

                                TextEntry::make('company.name')
                                    ->label(__('purchases::filament/admin/clusters/configurations/resources/vendor-price.infolist.sections.prices.entries.company'))
                                    ->icon('heroicon-o-building-office'),

                                TextEntry::make('created_at')
                                    ->label(__('purchases::filament/admin/clusters/configurations/resources/vendor-price.table.columns.created-at'))
                                    ->icon('heroicon-o-clock')
                                    ->dateTime(),

                                TextEntry::make('updated_at')
                                    ->label(__('purchases::filament/admin/clusters/configurations/resources/vendor-price.table.columns.updated-at'))
                                    ->icon('heroicon-o-arrow-path')
                                    ->dateTime(),
                            ]),

                        Group::make()
                            ->schema([
                                Section::make(__('purchases::filament/admin/clusters/configurations/resources/vendor-price.infolist.sections.record-information.title'))
                                    ->schema([
                                        TextEntry::make('created_at')
                                            ->label(__('purchases::filament/admin/clusters/configurations/resources/vendor-price.infolist.sections.record-information.entries.created-at'))
                                            ->dateTime()
                                            ->icon('heroicon-m-calendar'),

                                        TextEntry::make('creator.name')
                                            ->label(__('purchases::filament/admin/clusters/configurations/resources/vendor-price.infolist.sections.record-information.entries.created-by'))
                                            ->icon('heroicon-m-user'),

                                        TextEntry::make('updated_at')
                                            ->label(__('purchases::filament/admin/clusters/configurations/resources/vendor-price.infolist.sections.record-information.entries.last-updated'))
                                            ->dateTime()
                                            ->icon('heroicon-m-calendar-days'),
                                    ]),
                            ])
                            ->columnSpan(['lg' => 1]),

                    ])
                    ->columnSpan(['lg' => 1]),
            ])
            ->columns(3);
    }
}
