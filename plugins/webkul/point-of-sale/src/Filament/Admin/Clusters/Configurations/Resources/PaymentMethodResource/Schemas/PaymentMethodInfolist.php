<?php

namespace Webkul\PointOfSale\Filament\Admin\Clusters\Configurations\Resources\PaymentMethodResource\Schemas;

use Filament\Infolists\Components\IconEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class PaymentMethodInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make(__('point-of-sale::filament/admin/clusters/configurations/resources/payment-method.infolist.sections.general.title'))
                    ->schema([
                        TextEntry::make('name')
                            ->label(__('point-of-sale::filament/admin/clusters/configurations/resources/payment-method.infolist.sections.general.entries.name'))
                            ->placeholder('—'),

                        TextEntry::make('type')
                            ->label(__('point-of-sale::filament/admin/clusters/configurations/resources/payment-method.infolist.sections.general.entries.type'))
                            ->placeholder('—'),

                        TextEntry::make('terminal_type')
                            ->label(__('point-of-sale::filament/admin/clusters/configurations/resources/payment-method.infolist.sections.general.entries.terminal-type'))
                            ->placeholder('—'),

                        TextEntry::make('journal.name')
                            ->label(__('point-of-sale::filament/admin/clusters/configurations/resources/payment-method.infolist.sections.general.entries.journal-name'))
                            ->placeholder('—'),

                        TextEntry::make('receivableAccount.name')
                            ->label(__('point-of-sale::filament/admin/clusters/configurations/resources/payment-method.infolist.sections.general.entries.receivableAccount-name'))
                            ->placeholder('—'),

                        TextEntry::make('outstandingAccount.name')
                            ->label(__('point-of-sale::filament/admin/clusters/configurations/resources/payment-method.infolist.sections.general.entries.outstandingAccount-name'))
                            ->placeholder('—'),

                        IconEntry::make('is_cash_count')
                            ->label(__('point-of-sale::filament/admin/clusters/configurations/resources/payment-method.infolist.sections.general.entries.is-cash-count'))
                            ->boolean(),

                        IconEntry::make('is_split_transaction')
                            ->label(__('point-of-sale::filament/admin/clusters/configurations/resources/payment-method.infolist.sections.general.entries.is-split-transaction'))
                            ->boolean(),

                        TextEntry::make('company.name')
                            ->label(__('point-of-sale::filament/admin/clusters/configurations/resources/payment-method.infolist.sections.general.entries.company-name'))
                            ->placeholder('—'),
                    ])
                    ->columns(2),
            ]);
    }
}
