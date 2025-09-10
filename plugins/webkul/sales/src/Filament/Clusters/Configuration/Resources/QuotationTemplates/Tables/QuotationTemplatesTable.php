<?php

namespace Webkul\Sale\Filament\Clusters\Configuration\Resources\QuotationTemplates\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Notifications\Notification;
use Filament\Tables;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\QueryBuilder;
use Filament\Tables\Filters\QueryBuilder\Constraints\DateConstraint;
use Filament\Tables\Filters\QueryBuilder\Constraints\RelationshipConstraint;
use Filament\Tables\Filters\QueryBuilder\Constraints\RelationshipConstraint\Operators\IsRelatedToOperator;
use Filament\Tables\Table;

class QuotationTemplatesTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('createdBy.name')
                    ->placeholder('-')
                    ->sortable()
                    ->searchable()
                    ->label(__('sales::filament/clusters/configurations/resources/quotation-template.table.columns.created-by'))
                    ->label(__('Created By')),
                TextColumn::make('company.name')
                    ->placeholder('-')
                    ->sortable()
                    ->searchable()
                    ->label(__('sales::filament/clusters/configurations/resources/quotation-template.table.columns.company')),
                TextColumn::make('name')
                    ->placeholder('-')
                    ->sortable()
                    ->searchable()
                    ->label(__('sales::filament/clusters/configurations/resources/quotation-template.table.columns.name')),
                TextColumn::make('number_of_days')
                    ->sortable()
                    ->searchable()
                    ->placeholder('-')
                    ->label(__('sales::filament/clusters/configurations/resources/quotation-template.table.columns.number-of-days')),
                TextColumn::make('journal.name')
                    ->sortable()
                    ->searchable()
                    ->placeholder('-')
                    ->label(__('sales::filament/clusters/configurations/resources/quotation-template.table.columns.journal')),
                IconColumn::make('require_signature')
                    ->placeholder('-')
                    ->boolean()
                    ->label(__('sales::filament/clusters/configurations/resources/quotation-template.table.columns.signature-required')),
                IconColumn::make('require_payment')
                    ->placeholder('-')
                    ->boolean()
                    ->label(__('sales::filament/clusters/configurations/resources/quotation-template.table.columns.payment-required')),
                TextColumn::make('prepayment_percentage')
                    ->placeholder('-')
                    ->label(__('sales::filament/clusters/configurations/resources/quotation-template.table.columns.prepayment-percentage')),
            ])
            ->filtersFormColumns(2)
            ->filters([
                QueryBuilder::make()
                    ->constraintPickerColumns(2)
                    ->constraints([
                        RelationshipConstraint::make('createdBy.name')
                            ->label(__('sales::filament/clusters/configurations/resources/quotation-template.table.filters.created-by'))
                            ->icon('heroicon-o-user')
                            ->multiple()
                            ->selectable(
                                IsRelatedToOperator::make()
                                    ->titleAttribute('name')
                                    ->label(__('sales::filament/clusters/configurations/resources/quotation-template.table.filters.created-by'))
                                    ->searchable()
                                    ->multiple()
                                    ->preload(),
                            ),
                        RelationshipConstraint::make('company.name')
                            ->label(__('sales::filament/clusters/configurations/resources/quotation-template.table.filters.company'))
                            ->icon('heroicon-o-user')
                            ->multiple()
                            ->selectable(
                                IsRelatedToOperator::make()
                                    ->titleAttribute('name')
                                    ->label(__('sales::filament/clusters/configurations/resources/quotation-template.table.filters.company'))
                                    ->searchable()
                                    ->multiple()
                                    ->preload(),
                            ),
                        RelationshipConstraint::make('name')
                            ->label(__('sales::filament/clusters/configurations/resources/quotation-template.table.filters.name'))
                            ->icon('heroicon-o-building-office-2')
                            ->multiple()
                            ->selectable(
                                IsRelatedToOperator::make()
                                    ->titleAttribute('name')
                                    ->label(__('sales::filament/clusters/configurations/resources/quotation-template.table.filters.name'))
                                    ->searchable()
                                    ->multiple()
                                    ->preload(),
                            ),
                        DateConstraint::make('created_at')
                            ->label(__('sales::filament/clusters/configurations/resources/quotation-template.table.filters.created-at')),
                        DateConstraint::make('updated_at')
                            ->label(__('sales::filament/clusters/configurations/resources/quotation-template.table.filters.updated-at')),
                    ]),
            ])
            ->groups([
                Tables\Grouping\Group::make('company.name')
                    ->label(__('sales::filament/clusters/configurations/resources/quotation-template.table.groups.company'))
                    ->collapsible(),
                Tables\Grouping\Group::make('name')
                    ->label(__('sales::filament/clusters/configurations/resources/quotation-template.table.groups.name'))
                    ->collapsible(),
                Tables\Grouping\Group::make('journal.name')
                    ->label(__('sales::filament/clusters/configurations/resources/quotation-template.table.groups.journal'))
                    ->collapsible(),
            ])
            ->recordActions([
                ViewAction::make(),
                EditAction::make(),
                DeleteAction::make()
                    ->successNotification(
                        Notification::make()
                            ->title(__('sales::filament/clusters/configurations/resources/quotation-template.table.actions.delete.notification.title'))
                            ->body(__('sales::filament/clusters/configurations/resources/quotation-template.table.actions.delete.notification.body'))
                    ),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make()
                        ->successNotification(
                            Notification::make()
                                ->title(__('sales::filament/clusters/configurations/resources/quotation-template.table.actions.bulk-actions.notification.title'))
                                ->body(__('sales::filament/clusters/configurations/resources/quotation-template.table.actions.bulk-actions.notification.body'))
                        ),
                ]),
            ]);
    }
}
