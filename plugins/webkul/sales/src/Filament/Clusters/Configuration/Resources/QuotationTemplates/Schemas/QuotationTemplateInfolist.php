<?php

namespace Webkul\Sale\Filament\Clusters\Configuration\Resources\QuotationTemplates\Schemas;

use Filament\Infolists\Components\IconEntry;
use Filament\Infolists\Components\RepeatableEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Fieldset;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Group;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Tabs;
use Filament\Schemas\Components\Tabs\Tab;
use Filament\Schemas\Schema;

class QuotationTemplateInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Grid::make(['default' => 3])
                    ->schema([
                        Group::make()
                            ->schema([
                                Tabs::make('Tabs')
                                    ->tabs([
                                        Tab::make(__('sales::filament/clusters/configurations/resources/quotation-template.infolist.tabs.products.title'))
                                            ->schema([
                                                RepeatableEntry::make('products')
                                                    ->hiddenLabel()
                                                    ->schema([
                                                        TextEntry::make('product.name')
                                                            ->placeholder('-')
                                                            ->label(__('sales::filament/clusters/configurations/resources/quotation-template.infolist.entries.product'))
                                                            ->icon('heroicon-o-shopping-bag'),
                                                        TextEntry::make('description')
                                                            ->placeholder('-')
                                                            ->label(__('sales::filament/clusters/configurations/resources/quotation-template.infolist.entries.description')),
                                                        TextEntry::make('quantity')
                                                            ->placeholder('-')
                                                            ->label(__('sales::filament/clusters/configurations/resources/quotation-template.infolist.entries.quantity'))
                                                            ->numeric(),
                                                        TextEntry::make('unit-price')
                                                            ->placeholder('-')
                                                            ->label(__('sales::filament/clusters/configurations/resources/quotation-template.infolist.entries.unit-price'))
                                                            ->money('USD'),
                                                    ])
                                                    ->columns(4),

                                                RepeatableEntry::make('sections')
                                                    ->hiddenLabel()
                                                    ->hidden(fn ($record) => $record->sections->isEmpty())
                                                    ->schema([
                                                        TextEntry::make('name')
                                                            ->placeholder('-')
                                                            ->label(__('sales::filament/clusters/configurations/resources/quotation-template.infolist.entries.section-name')),
                                                        TextEntry::make('description')
                                                            ->placeholder('-')
                                                            ->label(__('sales::filament/clusters/configurations/resources/quotation-template.infolist.entries.description')),
                                                    ])
                                                    ->columns(2),

                                                RepeatableEntry::make('notes')
                                                    ->hiddenLabel()
                                                    ->hidden(fn ($record) => $record->notes->isEmpty())
                                                    ->schema([
                                                        TextEntry::make('name')
                                                            ->placeholder('-')
                                                            ->label(__('sales::filament/clusters/configurations/resources/quotation-template.infolist.entries.note-title')),
                                                        TextEntry::make('description')
                                                            ->placeholder('-')
                                                            ->label(__('sales::filament/clusters/configurations/resources/quotation-template.infolist.entries.description')),
                                                    ])
                                                    ->columns(2),
                                            ]),
                                        Tab::make(__('sales::filament/clusters/configurations/resources/quotation-template.infolist.tabs.terms-and-conditions.title'))
                                            ->schema([
                                                TextEntry::make('note')
                                                    ->markdown()
                                                    ->hiddenLabel()
                                                    ->columnSpanFull(),
                                            ]),
                                    ])->persistTabInQueryString(),
                            ])->columnSpan(['lg' => 2]),
                        Group::make()
                            ->schema([
                                Section::make()
                                    ->schema([
                                        Fieldset::make(__('sales::filament/clusters/configurations/resources/quotation-template.infolist.sections.general.title'))
                                            ->schema([
                                                TextEntry::make('name')
                                                    ->label(__('sales::filament/clusters/configurations/resources/quotation-template.infolist.entries.name'))
                                                    ->icon('heroicon-o-document-text'),
                                                TextEntry::make('number_of_days')
                                                    ->label(__('sales::filament/clusters/configurations/resources/quotation-template.infolist.entries.quotation-validity'))
                                                    ->suffix(' days')
                                                    ->icon('heroicon-o-calendar'),
                                                TextEntry::make('journal.name')
                                                    ->label(__('sales::filament/clusters/configurations/resources/quotation-template.infolist.entries.sale-journal'))
                                                    ->icon('heroicon-o-book-open'),
                                            ]),
                                    ]),
                                Section::make()
                                    ->schema([
                                        Fieldset::make(__('sales::filament/clusters/configurations/resources/quotation-template.infolist.sections.signature_and_payment.title'))
                                            ->schema([
                                                IconEntry::make('require_signature')
                                                    ->label(__('sales::filament/clusters/configurations/resources/quotation-template.infolist.entries.online-signature'))
                                                    ->boolean(),
                                                IconEntry::make('require_payment')
                                                    ->label(__('sales::filament/clusters/configurations/resources/quotation-template.infolist.entries.online-payment'))
                                                    ->boolean(),
                                                TextEntry::make('prepayment_percentage')
                                                    ->label(__('sales::filament/clusters/configurations/resources/quotation-template.infolist.entries.prepayment-percentage'))
                                                    ->suffix('%')
                                                    ->visible(fn ($record) => $record->require_payment === true),
                                            ]),
                                    ]),
                            ])
                            ->columnSpan(['lg' => 1]),
                    ]),
            ]);
    }
}
