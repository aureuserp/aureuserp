<?php

namespace Webkul\Purchase\Filament\Admin\Clusters\Orders\Resources\PurchaseAgreementResource\Schemas;

use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Group;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Tabs;
use Filament\Schemas\Components\Tabs\Tab;
use Filament\Schemas\Schema;
use Webkul\Field\Filament\Infolists\Components\ProgressStepper as InfolistProgressStepper;
use Webkul\Purchase\Enums\RequisitionState;
use Webkul\Purchase\Enums\RequisitionType;
use Webkul\Purchase\Filament\Admin\Clusters\Orders\Resources\PurchaseAgreementResource;
use Webkul\Support\Filament\Infolists\Components\RepeatableEntry;
use Webkul\Support\Filament\Infolists\Components\Repeater\TableColumn as InfolistTableColumn;

class PurchaseAgreementInfolist
{
    public static function configure(Schema $schema, array $customInfolistEntries = []): Schema
    {
        return $schema
            ->components([
                InfolistProgressStepper::make('state')
                    ->hiddenLabel()
                    ->inline()
                    ->options(RequisitionState::options())
                    ->default(RequisitionState::DRAFT),

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
                                    ->table(fn ($record) => [
                                        InfolistTableColumn::make('product.name')
                                            ->label(__('purchases::filament/admin/clusters/orders/resources/purchase-agreement.infolist.tabs.products.entries.product')),
                                        InfolistTableColumn::make('qty')
                                            ->label(__('purchases::filament/admin/clusters/orders/resources/purchase-agreement.infolist.tabs.products.entries.quantity')),
                                        InfolistTableColumn::make('ordered_qty')
                                            ->visible(fn (): bool => $record->type === RequisitionType::BLANKET_ORDER)
                                            ->label(__('purchases::filament/admin/clusters/orders/resources/purchase-agreement.infolist.tabs.products.entries.ordered')),
                                        InfolistTableColumn::make('uom.name')
                                            ->label(__('purchases::filament/admin/clusters/orders/resources/purchase-agreement.infolist.tabs.products.entries.uom')),
                                        InfolistTableColumn::make('price_unit')
                                            ->label(__('purchases::filament/admin/clusters/orders/resources/purchase-agreement.infolist.tabs.products.entries.unit-price')),
                                    ])
                                    ->schema([
                                        TextEntry::make('product.name'),
                                        TextEntry::make('qty'),
                                        TextEntry::make('ordered_qty'),
                                        TextEntry::make('uom.name')
                                            ->visible(PurchaseAgreementResource::getProductSettings()->enable_uom),
                                        TextEntry::make('price_unit')
                                            ->money(fn ($record) => $record->requisition->currency->code ?? 'USD'),
                                    ])
                                    ->columns([
                                        'sm' => 2,
                                        'xl' => 5,
                                    ]),
                            ]),

                        Section::make(__('purchases::filament/admin/clusters/orders/resources/purchase-agreement.infolist.tabs.additional.title'))
                            ->visible(! empty($customInfolistEntries))
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
