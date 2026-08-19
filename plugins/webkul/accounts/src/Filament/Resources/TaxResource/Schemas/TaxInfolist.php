<?php

namespace Webkul\Account\Filament\Resources\TaxResource\Schemas;

use Filament\Infolists\Components\IconEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Group;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Webkul\Account\Enums\AmountType;
use Webkul\Account\Filament\Resources\TaxResource;
use Webkul\Account\Models\Tax;

class TaxInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Grid::make(['default' => 3])
                    ->schema([
                        Group::make()
                            ->schema([
                                Section::make()
                                    ->schema([
                                        TextEntry::make('name')
                                            ->icon('heroicon-o-document-text')
                                            ->label(__('accounts::filament/resources/tax.infolist.sections.entries.name'))
                                            ->placeholder('—'),
                                        TextEntry::make('type_tax_use')
                                            ->icon('heroicon-o-calculator')
                                            ->label(__('accounts::filament/resources/tax.infolist.sections.entries.tax-type'))
                                            ->placeholder('—'),
                                        TextEntry::make('amount_type')
                                            ->icon('heroicon-o-calculator')
                                            ->label(__('accounts::filament/resources/tax.infolist.sections.entries.tax-computation'))
                                            ->placeholder('—'),
                                        TextEntry::make('tax_scope')
                                            ->icon('heroicon-o-globe-alt')
                                            ->label(__('accounts::filament/resources/tax.infolist.sections.entries.tax-scope'))
                                            ->placeholder('—'),
                                        TextEntry::make('amount')
                                            ->icon('heroicon-o-currency-dollar')
                                            ->label(__('accounts::filament/resources/tax.infolist.sections.entries.amount'))
                                            ->suffix(fn (Tax $record): ?string => $record->amount_type === AmountType::FIXED
                                                ? current_company()?->currency?->symbol
                                                : '%')
                                            ->visible(fn (Tax $record): bool => TaxResource::usesAmount($record->amount_type))
                                            ->placeholder('—'),
                                        TextEntry::make('formula')
                                            ->icon('heroicon-o-variable')
                                            ->label(__('accounts::filament/resources/tax.infolist.sections.entries.formula'))
                                            ->visible(fn (Tax $record): bool => $record->amount_type === AmountType::CODE)
                                            ->placeholder('—'),
                                        TextEntry::make('childrenTaxes.name')
                                            ->icon('heroicon-o-rectangle-stack')
                                            ->label(__('accounts::filament/resources/tax.infolist.sections.entries.children-taxes'))
                                            ->badge()
                                            ->visible(fn (Tax $record): bool => $record->amount_type === AmountType::GROUP)
                                            ->placeholder('—'),
                                        IconEntry::make('is_active')
                                            ->boolean()
                                            ->label(__('accounts::filament/resources/tax.infolist.sections.entries.status')),
                                    ])->columns(2),
                                Section::make()
                                    ->schema([
                                        TextEntry::make('description')
                                            ->label(__('accounts::filament/resources/tax.infolist.sections.field-set.description-and-legal-notes.entries.description'))
                                            ->markdown()
                                            ->placeholder('—')
                                            ->columnSpanFull(),
                                        TextEntry::make('invoice_legal_notes')
                                            ->label(__('accounts::filament/resources/tax.infolist.sections.field-set.description-and-legal-notes.entries.legal-notes'))
                                            ->markdown()
                                            ->placeholder('—')
                                            ->columnSpanFull(),
                                    ]),
                            ])->columnSpan(2),
                        Group::make([
                            Section::make()
                                ->schema([
                                    TextEntry::make('invoice_label')
                                        ->label(__('accounts::filament/resources/tax.infolist.sections.field-set.advanced-options.entries.invoice-label'))
                                        ->placeholder('—'),
                                    TextEntry::make('taxGroup.name')
                                        ->label(__('accounts::filament/resources/tax.infolist.sections.field-set.advanced-options.entries.tax-group'))
                                        ->placeholder('—'),
                                    TextEntry::make('country.name')
                                        ->label(__('accounts::filament/resources/tax.infolist.sections.field-set.advanced-options.entries.country'))
                                        ->placeholder('—'),
                                    TextEntry::make('price_include_override')
                                        ->label(__('accounts::filament/resources/tax.infolist.sections.field-set.advanced-options.entries.include-in-price')),
                                    IconEntry::make('include_base_amount')
                                        ->boolean()
                                        ->label(__('accounts::filament/resources/tax.infolist.sections.field-set.advanced-options.entries.include-base-amount')),
                                    IconEntry::make('is_base_affected')
                                        ->boolean()
                                        ->label(__('accounts::filament/resources/tax.infolist.sections.field-set.advanced-options.entries.is-base-affected')),
                                ]),
                        ])->columnSpan(1),
                    ]),
            ])->columns(1);
    }
}
