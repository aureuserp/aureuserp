<?php

namespace Webkul\Accounting\Filament\Clusters\Accounting\Resources\JournalEntryResource\Schemas;

use Filament\Infolists\Components\IconEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Fieldset;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Tabs;
use Filament\Schemas\Components\Tabs\Tab;
use Filament\Schemas\Schema;
use Filament\Support\Enums\FontWeight;
use Filament\Support\Enums\TextSize;
use Webkul\Account\Enums\MoveState;
use Webkul\Account\Enums\PaymentState;
use Webkul\Field\Filament\Infolists\Components\ProgressStepper as InfolistProgressStepper;
use Webkul\Support\Filament\Infolists\Components\RepeatableEntry;
use Webkul\Support\Filament\Infolists\Components\Repeater\TableColumn as InfolistTableColumn;

class JournalEntryInfolist
{
    public static function configure(Schema $schema, array $customInfolistEntries = []): Schema
    {
        return $schema
            ->columns(1)
            ->components([
                InfolistProgressStepper::make('state')
                    ->hiddenLabel()
                    ->inline()
                    ->options(function ($record) {
                        $options = MoveState::options();

                        if ($record?->state !== MoveState::CANCEL) {
                            unset($options[MoveState::CANCEL->value]);
                        }

                        return $options;
                    })
                    ->default(MoveState::DRAFT->value)
                    ->columnSpan('full'),
                Section::make()
                    ->schema([
                        TextEntry::make('payment_state')
                            ->badge(),
                    ])
                    ->compact()
                    ->visible(fn ($record) => in_array($record?->payment_state, [PaymentState::PAID, PaymentState::REVERSED])),

                Section::make(__('accounting::filament/clusters/accounting/resources/journal-entry.infolist.section.general.title'))
                    ->icon('heroicon-o-document-text')
                    ->schema([
                        Grid::make()
                            ->schema([
                                TextEntry::make('name')
                                    ->placeholder('-')
                                    ->label(__('accounting::filament/clusters/accounting/resources/journal-entry.infolist.section.general.entries.number'))
                                    ->icon('heroicon-o-document')
                                    ->weight('bold')
                                    ->size(TextSize::Large),
                            ])
                            ->columns(2),

                        Grid::make()
                            ->schema([
                                Grid::make()
                                    ->schema([
                                        TextEntry::make('reference')
                                            ->placeholder('-')
                                            ->label(__('accounting::filament/clusters/accounting/resources/journal-entry.infolist.section.general.entries.reference'))
                                            ->icon('heroicon-o-document-text'),
                                    ]),

                                Grid::make()
                                    ->schema([
                                        TextEntry::make('date')
                                            ->placeholder('-')
                                            ->label(__('accounting::filament/clusters/accounting/resources/journal-entry.infolist.section.general.entries.accounting-date'))
                                            ->icon('heroicon-o-calendar')
                                            ->date(),
                                        TextEntry::make('journal.name')
                                            ->placeholder('-')
                                            ->label(__('accounting::filament/clusters/accounting/resources/journal-entry.infolist.section.general.entries.journal'))
                                            ->icon('heroicon-o-book-open'),
                                    ])
                                    ->columns(1),
                            ])
                            ->columns(2),
                    ]),

                Tabs::make()
                    ->columnSpan('full')
                    ->tabs([
                        Tab::make(__('accounting::filament/clusters/accounting/resources/journal-entry.infolist.tabs.lines.title'))
                            ->icon('heroicon-o-list-bullet')
                            ->schema([
                                RepeatableEntry::make('lines')
                                    ->hiddenLabel()
                                    ->columnManager()
                                    ->live()
                                    ->table([
                                        InfolistTableColumn::make('account')
                                            ->alignCenter()
                                            ->toggleable()
                                            ->label(__('accounting::filament/clusters/accounting/resources/journal-entry.infolist.tabs.lines.repeater.entries.account')),
                                        InfolistTableColumn::make('partner')
                                            ->alignCenter()
                                            ->toggleable()
                                            ->label(__('accounting::filament/clusters/accounting/resources/journal-entry.infolist.tabs.lines.repeater.entries.partner')),
                                        InfolistTableColumn::make('name')
                                            ->alignCenter()
                                            ->toggleable()
                                            ->label(__('accounting::filament/clusters/accounting/resources/journal-entry.infolist.tabs.lines.repeater.entries.label')),
                                        InfolistTableColumn::make('currency')
                                            ->alignCenter()
                                            ->toggleable(isToggledHiddenByDefault: true)
                                            ->label(__('accounting::filament/clusters/accounting/resources/journal-entry.infolist.tabs.lines.repeater.entries.currency')),
                                        InfolistTableColumn::make('taxes')
                                            ->alignCenter()
                                            ->toggleable()
                                            ->label(__('accounting::filament/clusters/accounting/resources/journal-entry.infolist.tabs.lines.repeater.entries.taxes')),
                                        InfolistTableColumn::make('debit')
                                            ->alignCenter()
                                            ->toggleable()
                                            ->label(__('accounting::filament/clusters/accounting/resources/journal-entry.infolist.tabs.lines.repeater.entries.debit')),
                                        InfolistTableColumn::make('credit')
                                            ->alignCenter()
                                            ->toggleable()
                                            ->label(__('accounting::filament/clusters/accounting/resources/journal-entry.infolist.tabs.lines.repeater.entries.credit')),
                                    ])
                                    ->schema([
                                        TextEntry::make('account')
                                            ->placeholder('-')
                                            ->formatStateUsing(fn ($state) => $state['name'] ?? '-'),
                                        TextEntry::make('partner')
                                            ->placeholder('-')
                                            ->formatStateUsing(fn ($state) => $state ? ($state['name'] ?? '-') : '-'),
                                        TextEntry::make('name')
                                            ->placeholder('-'),
                                        TextEntry::make('currency')
                                            ->placeholder('-')
                                            ->formatStateUsing(fn ($state) => $state['name'] ?? '-'),
                                        TextEntry::make('taxes')
                                            ->badge()
                                            ->state(function ($record): array {
                                                return $record->taxes->map(fn ($tax) => [
                                                    'name' => $tax->name,
                                                ])->toArray();
                                            })
                                            ->formatStateUsing(fn ($state) => $state['name'] ?? '-')
                                            ->placeholder('-')
                                            ->weight(FontWeight::Bold),
                                        TextEntry::make('debit')
                                            ->placeholder('-')
                                            ->money(fn ($record) => $record->currency?->name),
                                        TextEntry::make('credit')
                                            ->placeholder('-')
                                            ->money(fn ($record) => $record->currency?->name),
                                    ])->columns(5),
                            ]),

                        Tab::make(__('accounting::filament/clusters/accounting/resources/journal-entry.infolist.tabs.other-information.title'))
                            ->icon('heroicon-o-information-circle')
                            ->schema([
                                Fieldset::make(__('accounting::filament/clusters/accounting/resources/journal-entry.infolist.tabs.other-information.fieldset.accounting.title'))
                                    ->schema([
                                        TextEntry::make('company.name')
                                            ->placeholder('-')
                                            ->label(__('accounting::filament/clusters/accounting/resources/journal-entry.infolist.tabs.other-information.fieldset.accounting.entries.company')),
                                        TextEntry::make('fiscalPosition.name')
                                            ->placeholder('-')
                                            ->label(__('accounting::filament/clusters/accounting/resources/journal-entry.infolist.tabs.other-information.fieldset.accounting.entries.fiscal-position')),
                                        IconEntry::make('checked')
                                            ->label(__('accounting::filament/clusters/accounting/resources/journal-entry.infolist.tabs.other-information.fieldset.accounting.entries.checked'))
                                            ->boolean(),
                                    ])
                                    ->columns(1),
                            ])
                            ->columns(1),

                        Tab::make(__('accounting::filament/clusters/accounting/resources/journal-entry.infolist.tabs.term-and-conditions.title'))
                            ->icon('heroicon-o-clipboard-document-list')
                            ->schema([
                                TextEntry::make('narration')
                                    ->placeholder('-')
                                    ->html()
                                    ->hiddenLabel(),
                            ]),
                    ]),

                Section::make()
                    ->schema($customInfolistEntries)
                    ->columns(2),
            ]);
    }
}
