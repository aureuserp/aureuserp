<?php

namespace Webkul\Accounting\Filament\Clusters\Accounting\Resources;

use Filament\Actions\ExportAction;
use Filament\Resources\Resource;
use Filament\Support\Enums\FontWeight;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\Summarizers\Sum;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\QueryBuilder;
use Filament\Tables\Filters\QueryBuilder\Constraints\BooleanConstraint;
use Filament\Tables\Filters\QueryBuilder\Constraints\DateConstraint;
use Filament\Tables\Filters\QueryBuilder\Constraints\NumberConstraint;
use Filament\Tables\Filters\QueryBuilder\Constraints\RelationshipConstraint;
use Filament\Tables\Filters\QueryBuilder\Constraints\RelationshipConstraint\Operators\IsRelatedToOperator;
use Filament\Tables\Filters\QueryBuilder\Constraints\TextConstraint;
use Filament\Tables\Grouping\Group;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Webkul\Accounting\Filament\Clusters\Accounting;
use Webkul\Accounting\Filament\Clusters\Accounting\Resources\JournalItemResource\Pages\ListJournalItems;
use Webkul\Accounting\Filament\Exports\JournalItemExporter;
use Webkul\Accounting\Models\JournalItem;

class JournalItemResource extends Resource
{
    protected static ?string $model = JournalItem::class;

    protected static bool $shouldRegisterNavigation = true;

    protected static ?string $cluster = Accounting::class;

    protected static ?int $navigationSort = 2;

    protected static ?string $recordTitleAttribute = 'name';

    protected static bool $isGloballySearchable = false;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-queue-list';

    public static function getModelLabel(): string
    {
        return __('accounting::filament/clusters/accounting/resources/journal-item.title');
    }

    public static function getNavigationLabel(): string
    {
        return __('accounting::filament/clusters/accounting/resources/journal-item.navigation.title');
    }

    public static function table(Table $table): Table
    {
        return $table
            ->reorderableColumns()
            ->columnManagerColumns(2)
            ->columns([
                TextColumn::make('move_name')
                    ->placeholder('-')
                    ->label(__('accounting::filament/clusters/accounting/resources/journal-item.table.columns.number'))
                    ->searchable()
                    ->sortable(),
                TextColumn::make('date')
                    ->date()
                    ->placeholder('-')
                    ->label(__('accounting::filament/clusters/accounting/resources/journal-item.table.columns.date'))
                    ->searchable()
                    ->sortable(),
                TextColumn::make('account.name')
                    ->placeholder('-')
                    ->label(__('accounting::filament/clusters/accounting/resources/journal-item.table.columns.account'))
                    ->searchable()
                    ->sortable(),
                TextColumn::make('partner.name')
                    ->placeholder('-')
                    ->label(__('accounting::filament/clusters/accounting/resources/journal-item.table.columns.partner'))
                    ->searchable()
                    ->sortable()
                    ->toggleable(),
                TextColumn::make('name')
                    ->placeholder('-')
                    ->label(__('accounting::filament/clusters/accounting/resources/journal-item.table.columns.label'))
                    ->searchable()
                    ->sortable()
                    ->toggleable(),
                TextColumn::make('reference')
                    ->placeholder('-')
                    ->label(__('accounting::filament/clusters/accounting/resources/journal-item.table.columns.reference'))
                    ->searchable()
                    ->sortable()
                    ->toggleable(),
                TextColumn::make('journal.name')
                    ->placeholder('-')
                    ->label(__('accounting::filament/clusters/accounting/resources/journal-item.table.columns.journal'))
                    ->searchable()
                    ->sortable()
                    ->toggleable(),
                TextColumn::make('debit')
                    ->placeholder('-')
                    ->label(__('accounting::filament/clusters/accounting/resources/journal-item.table.columns.debit'))
                    ->money(fn ($record) => $record->currency?->name)
                    ->sortable()
                    ->summarize(Sum::make()->label(__('accounting::filament/clusters/accounting/resources/journal-item.table.total')))
                    ->weight(FontWeight::Bold),
                TextColumn::make('credit')
                    ->placeholder('-')
                    ->label(__('accounting::filament/clusters/accounting/resources/journal-item.table.columns.credit'))
                    ->money(fn ($record) => $record->currency?->name)
                    ->sortable()
                    ->summarize(Sum::make()->label(__('accounting::filament/clusters/accounting/resources/journal-item.table.total')))
                    ->weight(FontWeight::Bold),
                TextColumn::make('balance')
                    ->placeholder('-')
                    ->label(__('accounting::filament/clusters/accounting/resources/journal-item.table.columns.balance'))
                    ->money(fn ($record) => $record->currency?->name)
                    ->sortable()
                    ->summarize(Sum::make()->label(__('accounting::filament/clusters/accounting/resources/journal-item.table.total')))
                    ->toggleable(),
                TextColumn::make('currency.name')
                    ->placeholder('-')
                    ->label(__('accounting::filament/clusters/accounting/resources/journal-item.table.columns.currency'))
                    ->searchable()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('company.name')
                    ->placeholder('-')
                    ->label(__('accounting::filament/clusters/accounting/resources/journal-item.table.columns.company'))
                    ->searchable()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('parent_state')
                    ->placeholder('-')
                    ->label(__('accounting::filament/clusters/accounting/resources/journal-item.table.columns.status'))
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('amount_currency')
                    ->placeholder('-')
                    ->label(__('accounting::filament/clusters/accounting/resources/journal-item.table.columns.amount-currency'))
                    ->money(fn ($record) => $record->currency?->name)
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('amount_residual')
                    ->placeholder('-')
                    ->label(__('accounting::filament/clusters/accounting/resources/journal-item.table.columns.amount-residual'))
                    ->money(fn ($record) => $record->currency?->name)
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                IconColumn::make('reconciled')
                    ->boolean()
                    ->placeholder('-')
                    ->label(__('accounting::filament/clusters/accounting/resources/journal-item.table.columns.reconciled'))
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('date_maturity')
                    ->date()
                    ->placeholder('-')
                    ->label(__('accounting::filament/clusters/accounting/resources/journal-item.table.columns.due-date'))
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->groups([
                Group::make('move.name')
                    ->label(__('accounting::filament/clusters/accounting/resources/journal-item.table.groups.journal-entry'))
                    ->collapsible(),
                Group::make('account.name')
                    ->label(__('accounting::filament/clusters/accounting/resources/journal-item.table.groups.account'))
                    ->collapsible(),
                Group::make('partner.name')
                    ->label(__('accounting::filament/clusters/accounting/resources/journal-item.table.groups.partner'))
                    ->collapsible(),
                Group::make('journal.name')
                    ->label(__('accounting::filament/clusters/accounting/resources/journal-item.table.groups.journal'))
                    ->collapsible(),
                Group::make('date')
                    ->date()
                    ->label(__('accounting::filament/clusters/accounting/resources/journal-item.table.groups.date'))
                    ->collapsible(),
                Group::make('invoice_date')
                    ->date()
                    ->label(__('accounting::filament/clusters/accounting/resources/journal-item.table.groups.invoice-date'))
                    ->collapsible(),
                Group::make('matching_number')
                    ->label(__('accounting::filament/clusters/accounting/resources/journal-item.table.groups.matching'))
                    ->collapsible(),
                Group::make('parent_state')
                    ->label(__('accounting::filament/clusters/accounting/resources/journal-item.table.groups.status'))
                    ->collapsible(),
                Group::make('company.name')
                    ->label(__('accounting::filament/clusters/accounting/resources/journal-item.table.groups.company'))
                    ->collapsible(),
            ])
            ->filtersFormColumns(2)
            ->filters([
                QueryBuilder::make()
                    ->constraintPickerColumns(2)
                    ->constraints([
                        TextConstraint::make('move_name')
                            ->label(__('accounting::filament/clusters/accounting/resources/journal-item.table.filters.number')),
                        TextConstraint::make('name')
                            ->label(__('accounting::filament/clusters/accounting/resources/journal-item.table.filters.label')),
                        TextConstraint::make('reference')
                            ->label(__('accounting::filament/clusters/accounting/resources/journal-item.table.filters.reference')),
                        TextConstraint::make('matching_number')
                            ->label(__('accounting::filament/clusters/accounting/resources/journal-item.table.filters.matching-number')),
                        RelationshipConstraint::make('account')
                            ->label(__('accounting::filament/clusters/accounting/resources/journal-item.table.filters.account'))
                            ->multiple()
                            ->selectable(
                                IsRelatedToOperator::make()
                                    ->titleAttribute('name')
                                    ->searchable()
                                    ->multiple()
                                    ->preload(),
                            ),
                        RelationshipConstraint::make('partner')
                            ->label(__('accounting::filament/clusters/accounting/resources/journal-item.table.filters.partner'))
                            ->multiple()
                            ->selectable(
                                IsRelatedToOperator::make()
                                    ->titleAttribute('name')
                                    ->searchable()
                                    ->multiple()
                                    ->preload(),
                            ),
                        RelationshipConstraint::make('journal')
                            ->label(__('accounting::filament/clusters/accounting/resources/journal-item.table.filters.journal'))
                            ->multiple()
                            ->selectable(
                                IsRelatedToOperator::make()
                                    ->titleAttribute('name')
                                    ->searchable()
                                    ->multiple()
                                    ->preload(),
                            ),
                        RelationshipConstraint::make('currency')
                            ->label(__('accounting::filament/clusters/accounting/resources/journal-item.table.filters.currency'))
                            ->multiple()
                            ->selectable(
                                IsRelatedToOperator::make()
                                    ->titleAttribute('name')
                                    ->searchable()
                                    ->multiple()
                                    ->preload(),
                            ),
                        RelationshipConstraint::make('company')
                            ->label(__('accounting::filament/clusters/accounting/resources/journal-item.table.filters.company'))
                            ->multiple()
                            ->selectable(
                                IsRelatedToOperator::make()
                                    ->titleAttribute('name')
                                    ->searchable()
                                    ->multiple()
                                    ->preload(),
                            ),
                        DateConstraint::make('date')
                            ->label(__('accounting::filament/clusters/accounting/resources/journal-item.table.filters.date')),
                        DateConstraint::make('invoice_date')
                            ->label(__('accounting::filament/clusters/accounting/resources/journal-item.table.filters.invoice-date')),
                        DateConstraint::make('date_maturity')
                            ->label(__('accounting::filament/clusters/accounting/resources/journal-item.table.filters.due-date')),
                        DateConstraint::make('discount_date')
                            ->label(__('accounting::filament/clusters/accounting/resources/journal-item.table.filters.discount-date')),
                        NumberConstraint::make('debit')
                            ->label(__('accounting::filament/clusters/accounting/resources/journal-item.table.filters.debit')),
                        NumberConstraint::make('credit')
                            ->label(__('accounting::filament/clusters/accounting/resources/journal-item.table.filters.credit')),
                        NumberConstraint::make('balance')
                            ->label(__('accounting::filament/clusters/accounting/resources/journal-item.table.filters.balance')),
                        NumberConstraint::make('amount_currency')
                            ->label(__('accounting::filament/clusters/accounting/resources/journal-item.table.filters.amount-currency')),
                        NumberConstraint::make('amount_residual')
                            ->label(__('accounting::filament/clusters/accounting/resources/journal-item.table.filters.amount-residual')),
                        NumberConstraint::make('quantity')
                            ->label(__('accounting::filament/clusters/accounting/resources/journal-item.table.filters.quantity')),
                        NumberConstraint::make('price_unit')
                            ->label(__('accounting::filament/clusters/accounting/resources/journal-item.table.filters.price-unit')),
                        NumberConstraint::make('discount')
                            ->label(__('accounting::filament/clusters/accounting/resources/journal-item.table.filters.discount')),
                        BooleanConstraint::make('reconciled')
                            ->label(__('accounting::filament/clusters/accounting/resources/journal-item.table.filters.reconciled')),
                        BooleanConstraint::make('is_imported')
                            ->label(__('accounting::filament/clusters/accounting/resources/journal-item.table.filters.imported')),
                        BooleanConstraint::make('is_downpayment')
                            ->label(__('accounting::filament/clusters/accounting/resources/journal-item.table.filters.down-payment')),
                        DateConstraint::make('created_at')
                            ->label(__('accounting::filament/clusters/accounting/resources/journal-item.table.filters.created-at')),
                        DateConstraint::make('updated_at')
                            ->label(__('accounting::filament/clusters/accounting/resources/journal-item.table.filters.updated-at')),
                    ]),
            ])
            ->recordActions([])
            ->toolbarActions([
                ExportAction::make()
                    ->label(__('accounting::filament/clusters/accounting/resources/journal-item.table.toolbar-actions.export.label'))
                    ->icon('heroicon-o-arrow-up-tray')
                    ->exporter(JournalItemExporter::class),
            ])
            ->modifyQueryUsing(function (Builder $query) {
                $query->with(['currency', 'account', 'partner', 'journal', 'company', 'move']);
            })
            ->defaultSort('date', 'desc');
    }

    public static function canCreate(): bool
    {
        return false;
    }

    public static function canEdit(Model $record): bool
    {
        return false;
    }

    public static function canDelete(Model $record): bool
    {
        return false;
    }

    public static function canDeleteAny(): bool
    {
        return false;
    }

    public static function getPages(): array
    {
        return [
            'index' => ListJournalItems::route('/'),
        ];
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->orderByDesc('date');
    }
}
