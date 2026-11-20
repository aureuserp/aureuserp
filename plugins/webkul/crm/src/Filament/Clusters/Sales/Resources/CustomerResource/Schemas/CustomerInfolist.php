<?php

namespace Webkul\Crm\Filament\Clusters\Sales\Resources\CustomerResource\Schemas;

use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Group;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Webkul\Partner\Models\Partner;

class CustomerInfolist
{
    public static function configure(Schema $schema): Schema
    {
        $components = $schema->getComponents(withHidden: true);
        $contributed = count($components) > 2 ? array_slice($components, 1, -1) : [];
        $main = count($components) > 2 ? [$components[0], $components[array_key_last($components)]] : $components;

        return $schema
            ->components([
                Group::make($main)
                    ->columnSpan(['lg' => 2]),
                Group::make([
                    Section::make(__('crm::filament/clusters/sales/resources/customer.infolist.sections.overview'))
                        ->schema([
                            TextEntry::make('user.name')
                                ->label(__('crm::filament/clusters/sales/resources/customer.table.columns.salesperson'))
                                ->placeholder('—'),
                            TextEntry::make('open_opportunities')
                                ->label(__('crm::filament/clusters/sales/resources/customer.infolist.fields.opportunities'))
                                ->state(fn (Partner $record): int => $record->crmOpportunities()->count())
                                ->badge()
                                ->color('primary'),
                            TextEntry::make('sale_orders')
                                ->label(__('crm::filament/clusters/sales/resources/customer.infolist.fields.sale-orders'))
                                ->state(fn (Partner $record): int => $record->crmSaleOrders()->count())
                                ->badge()
                                ->color('gray'),
                        ]),
                    Section::make(__('crm::filament/clusters/configurations/resources/partner-level.contact.section'))
                        ->schema([
                            TextEntry::make('crmPartner.level.name')
                                ->label(__('crm::filament/clusters/configurations/resources/partner-level.contact.level'))
                                ->placeholder('—'),
                            TextEntry::make('level_weight')
                                ->label(__('crm::filament/clusters/configurations/resources/partner-level.contact.weight'))
                                ->state(fn ($record): ?int => $record->crmPartner?->level_id || $record->crmPartner?->level_weight !== null
                                    ? $record->crmPartner->effectiveWeight()
                                    : null)
                                ->placeholder('—'),
                        ]),
                    ...$contributed,
                ])
                    ->columnSpan(['lg' => 1]),
            ])
            ->columns(3);
    }
}
