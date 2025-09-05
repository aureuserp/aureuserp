<?php

namespace Webkul\Account\Filament\Resources\PaymentTerms\Schemas;

use Filament\Infolists\Components\IconEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Group;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Illuminate\Support\HtmlString;

class PaymentTermInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make()
                    ->schema([
                        Grid::make(['default' => 3])
                            ->schema([
                                TextEntry::make('name')
                                    ->label(__('accounts::filament/resources/payment-term.infolist.sections.entries.payment-term'))
                                    ->icon('heroicon-o-briefcase')
                                    ->placeholder('—'),
                                IconEntry::make('early_discount')
                                    ->label(__('accounts::filament/resources/payment-term.infolist.sections.entries.early-discount'))
                                    ->boolean(),
                                Group::make()
                                    ->schema([
                                        TextEntry::make('discount_percentage')
                                            ->suffix('%')
                                            ->label(__('accounts::filament/resources/payment-term.infolist.sections.entries.discount-percentage'))
                                            ->placeholder('—'),

                                        TextEntry::make('discount_days')
                                            ->label(__('accounts::filament/resources/payment-term.infolist.sections.entries.discount-days-prefix'))
                                            ->suffix(__('accounts::filament/resources/payment-term.infolist.sections.entries.discount-days-suffix'))
                                            ->placeholder('—'),
                                    ])->columns(2),
                                TextEntry::make('early_pay_discount')
                                    ->label(__('accounts::filament/resources/payment-term.infolist.sections.entries.reduced-tax'))
                                    ->placeholder('—'),
                                TextEntry::make('note')
                                    ->label(__('accounts::filament/resources/payment-term.infolist.sections.entries.note'))
                                    ->columnSpanFull()
                                    ->formatStateUsing(fn ($state) => new HtmlString($state))
                                    ->placeholder('—'),
                            ]),
                    ]),
            ]);
    }
}
