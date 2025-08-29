<?php

namespace Webkul\Purchase\Filament\Admin\Clusters\Orders\Resources\PurchaseAgreements\Schemas;

use Filament\Infolists\Components\RepeatableEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Group;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Tabs;
use Filament\Schemas\Components\Tabs\Tab;
use Filament\Schemas\Schema;
use Webkul\Field\Filament\Traits\HasCustomFields;
use Webkul\Purchase\Enums\RequisitionType;
use Webkul\Purchase\Filament\Admin\Clusters\Orders\Resources\PurchaseAgreements\PurchaseAgreementResource;
use Webkul\Purchase\Settings\ProductSettings;

class PurchaseAgreementInfolist
{
    use HasCustomFields;
    use HasCustomFields;

    public static function getModel()
    {
        return PurchaseAgreementResource::getModel();
    }

    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make()
                    ->schema([
                        TextEntry::make('state')
                            ->badge(),
                    ])
                    ->compact(),

                Section::make(__('purchases::filament/admin/clusters/orders/resources/purchase-agreement.infolist.sections.general.title'))
                    ->icon('heroicon-o-document-text')
                    ->schema([
                        Grid::make(2)
                            ->schema([
                                Group::make([
                                    TextEntry::make('partner.name')
                                        ->label(__('purchases::filament/admin/clusters/orders/resources/purchase-agreement.infolist.sections.general.entries.vendor'))
                                        ->icon('heroicon-o-building-storefront'),

                                    TextEntry::make('user.name')
                                        ->label(__('purchases::filament/admin/clusters/orders/resources/purchase-agreement.infolist.sections.general.entries.buyer'))
                                        ->icon('heroicon-o-user'),

                                    TextEntry::make('type')
                                        ->label(__('purchases::filament/admin/clusters/orders/resources/purchase-agreement.infolist.sections.general.entries.agreement-type'))
                                        ->icon('heroicon-o-document')
                                        ->badge(),

                                    TextEntry::make('currency.name')
                                        ->label(__('purchases::filament/admin/clusters/orders/resources/purchase-agreement.infolist.sections.general.entries.currency'))
                                        ->icon('heroicon-o-currency-dollar'),
                                ]),

                                Group::make([
                                    Grid::make(2)
                                        ->schema([
                                            TextEntry::make('starts_at')
                                                ->label(__('purchases::filament/admin/clusters/orders/resources/purchase-agreement.infolist.sections.general.entries.valid-from'))
                                                ->icon('heroicon-o-calendar')
                                                ->date(),

                                            TextEntry::make('ends_at')
                                                ->label(__('purchases::filament/admin/clusters/orders/resources/purchase-agreement.infolist.sections.general.entries.valid-to'))
                                                ->icon('heroicon-o-calendar')
                                                ->date(),
                                        ])
                                        ->visible(fn ($record) => $record->type === RequisitionType::BLANKET_ORDER),

                                    TextEntry::make('reference')
                                        ->label(__('purchases::filament/admin/clusters/orders/resources/purchase-agreement.infolist.sections.general.entries.reference'))
                                        ->icon('heroicon-o-identification'),

                                    TextEntry::make('company.name')
                                        ->label(__('purchases::filament/admin/clusters/orders/resources/purchase-agreement.infolist.sections.general.entries.company'))
                                        ->icon('heroicon-o-building-office'),
                                ]),
                            ]),
                    ]),

                Tabs::make('Tabs')
                    ->tabs([
                        Tab::make(__('purchases::filament/admin/clusters/orders/resources/purchase-agreement.infolist.tabs.products.title'))
                            ->icon('heroicon-o-cube')
                            ->schema([
                                RepeatableEntry::make('lines')
                                    ->schema([
                                        TextEntry::make('product.name')
                                            ->label(__('purchases::filament/admin/clusters/orders/resources/purchase-agreement.infolist.tabs.products.entries.product')),

                                        TextEntry::make('qty')
                                            ->label(__('purchases::filament/admin/clusters/orders/resources/purchase-agreement.infolist.tabs.products.entries.quantity')),

                                        TextEntry::make('uom.name')
                                            ->label(__('inventories::filament/clusters/operations/resources/operation.form.tabs.operations.entries.unit'))
                                            ->visible(fn (ProductSettings $settings) => $settings->enable_uom),

                                        TextEntry::make('price_unit')
                                            ->label(__('purchases::filament/admin/clusters/orders/resources/purchase-agreement.infolist.tabs.products.entries.unit-price'))
                                            ->money(fn ($record) => $record->requisition->currency->code ?? 'USD'),
                                    ])
                                    ->columns([
                                        'sm' => 2,
                                        'xl' => 4,
                                    ]),
                            ]),

                        Section::make(__('purchases::filament/admin/clusters/orders/resources/purchase-agreement.infolist.tabs.additional.title'))
                            ->visible(! empty($customInfolistEntries = static::getCustomInfolistEntries()))
                            ->schema($customInfolistEntries),

                        Tab::make(__('purchases::filament/admin/clusters/orders/resources/purchase-agreement.infolist.tabs.terms.title'))
                            ->icon('heroicon-o-document-text')
                            ->schema([
                                TextEntry::make('description')
                                    ->hiddenLabel()
                                    ->markdown()
                                    ->prose(),
                            ]),
                    ]),

                Section::make(__('purchases::filament/admin/clusters/orders/resources/purchase-agreement.infolist.sections.metadata.title'))
                    ->icon('heroicon-o-information-circle')
                    ->schema([
                        Grid::make(3)
                            ->schema([
                                TextEntry::make('created_at')
                                    ->label(__('purchases::filament/admin/clusters/orders/resources/purchase-agreement.infolist.sections.metadata.entries.created-at'))
                                    ->dateTime()
                                    ->icon('heroicon-o-clock'),

                                TextEntry::make('creator.name')
                                    ->label(__('purchases::filament/admin/clusters/orders/resources/purchase-agreement.infolist.sections.metadata.entries.created-by'))
                                    ->icon('heroicon-o-user'),

                                TextEntry::make('updated_at')
                                    ->label(__('purchases::filament/admin/clusters/orders/resources/purchase-agreement.infolist.sections.metadata.entries.updated-at'))
                                    ->dateTime()
                                    ->icon('heroicon-o-arrow-path'),
                            ]),
                    ]),
            ])
            ->columns(1);
    }
}
