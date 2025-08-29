<?php

namespace Webkul\Purchase\Filament\Admin\Clusters\Orders\Resources\PurchaseAgreements\Tables;

use Filament\Actions\Action;
use Filament\Actions\ActionGroup;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ForceDeleteAction;
use Filament\Actions\ForceDeleteBulkAction;
use Filament\Actions\RestoreAction;
use Filament\Actions\RestoreBulkAction;
use Filament\Actions\ViewAction;
use Filament\Notifications\Notification;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Enums\FiltersLayout;
use Filament\Tables\Filters\QueryBuilder;
use Filament\Tables\Filters\QueryBuilder\Constraints\DateConstraint;
use Filament\Tables\Filters\QueryBuilder\Constraints\RelationshipConstraint;
use Filament\Tables\Filters\QueryBuilder\Constraints\RelationshipConstraint\Operators\IsRelatedToOperator;
use Filament\Tables\Filters\QueryBuilder\Constraints\SelectConstraint;
use Filament\Tables\Filters\QueryBuilder\Constraints\TextConstraint;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\QueryException;
use Webkul\Field\Filament\Traits\HasCustomFields;
use Webkul\Purchase\Enums\RequisitionState;
use Webkul\Purchase\Filament\Admin\Clusters\Orders\Resources\PurchaseAgreements\PurchaseAgreementResource;
use Webkul\Purchase\Models\Requisition;

class PurchaseAgreementsTable
{
    use HasCustomFields;
    use HasCustomFields;

    public static function getModel()
    {
        return PurchaseAgreementResource::getModel();
    }

    public static function configure(Table $table): Table
    {
        return $table
            ->columns(static::mergeCustomTableColumns([
                TextColumn::make('name')
                    ->label(__('purchases::filament/admin/clusters/orders/resources/purchase-agreement.table.columns.agreement'))
                    ->searchable()
                    ->sortable(),
                TextColumn::make('partner.name')
                    ->label(__('purchases::filament/admin/clusters/orders/resources/purchase-agreement.table.columns.vendor'))
                    ->searchable()
                    ->sortable()
                    ->toggleable(),
                TextColumn::make('type')
                    ->label(__('purchases::filament/admin/clusters/orders/resources/purchase-agreement.table.columns.agreement-type'))
                    ->sortable()
                    ->toggleable(),
                TextColumn::make('user.name')
                    ->label(__('purchases::filament/admin/clusters/orders/resources/purchase-agreement.table.columns.buyer'))
                    ->sortable()
                    ->placeholder('—')
                    ->toggleable(),
                TextColumn::make('company.name')
                    ->label(__('purchases::filament/admin/clusters/orders/resources/purchase-agreement.table.columns.company'))
                    ->sortable()
                    ->placeholder('—')
                    ->toggleable(),
                TextColumn::make('starts_at')
                    ->label(__('purchases::filament/admin/clusters/orders/resources/purchase-agreement.table.columns.valid-from'))
                    ->sortable()
                    ->placeholder('—')
                    ->toggleable(),
                TextColumn::make('ends_at')
                    ->label(__('purchases::filament/admin/clusters/orders/resources/purchase-agreement.table.columns.valid-to'))
                    ->sortable()
                    ->placeholder('—')
                    ->toggleable(),
                TextColumn::make('reference')
                    ->label(__('purchases::filament/admin/clusters/orders/resources/purchase-agreement.table.columns.reference'))
                    ->sortable()
                    ->placeholder('—')
                    ->toggleable(),
                TextColumn::make('state')
                    ->label(__('purchases::filament/admin/clusters/orders/resources/purchase-agreement.table.columns.status'))
                    ->sortable()
                    ->badge()
                    ->toggleable(),
            ]))
            ->groups([
                Tables\Grouping\Group::make('partner.name')
                    ->label(__('purchases::filament/admin/clusters/orders/resources/purchase-agreement.table.groups.vendor')),
                Tables\Grouping\Group::make('state')
                    ->label(__('purchases::filament/admin/clusters/orders/resources/purchase-agreement.table.groups.state')),
                Tables\Grouping\Group::make('type')
                    ->label(__('purchases::filament/admin/clusters/orders/resources/purchase-agreement.table.groups.agreement-type')),
                Tables\Grouping\Group::make('created_at')
                    ->label(__('purchases::filament/admin/clusters/orders/resources/purchase-agreement.table.groups.created-at'))
                    ->collapsible(),
                Tables\Grouping\Group::make('updated_at')
                    ->label(__('purchases::filament/admin/clusters/orders/resources/purchase-agreement.table.groups.updated-at'))
                    ->date()
                    ->collapsible(),
            ])
            ->filters([
                QueryBuilder::make()
                    ->constraints(collect(static::mergeCustomTableQueryBuilderConstraints([
                        TextConstraint::make('name')
                            ->label(__('purchases::filament/admin/clusters/orders/resources/purchase-agreement.table.filters.agreement')),
                        SelectConstraint::make('state')
                            ->label(__('purchases::filament/admin/clusters/orders/resources/purchase-agreement.table.filters.status'))
                            ->multiple()
                            ->options(RequisitionState::class)
                            ->icon('heroicon-o-bars-2'),
                        RelationshipConstraint::make('partner')
                            ->label(__('purchases::filament/admin/clusters/orders/resources/purchase-agreement.table.filters.vendor'))
                            ->multiple()
                            ->selectable(
                                IsRelatedToOperator::make()
                                    ->titleAttribute('name')
                                    ->searchable()
                                    ->multiple()
                                    ->preload(),
                            )
                            ->icon('heroicon-o-user'),
                        RelationshipConstraint::make('user')
                            ->label(__('purchases::filament/admin/clusters/orders/resources/purchase-agreement.table.filters.buyer'))
                            ->multiple()
                            ->selectable(
                                IsRelatedToOperator::make()
                                    ->titleAttribute('name')
                                    ->searchable()
                                    ->multiple()
                                    ->preload(),
                            )
                            ->icon('heroicon-o-user'),
                        RelationshipConstraint::make('company')
                            ->label(__('purchases::filament/admin/clusters/orders/resources/purchase-agreement.table.filters.company'))
                            ->multiple()
                            ->selectable(
                                IsRelatedToOperator::make()
                                    ->titleAttribute('name')
                                    ->searchable()
                                    ->multiple()
                                    ->preload(),
                            )
                            ->icon('heroicon-o-building-office'),
                        DateConstraint::make('starts_at')
                            ->label(__('purchases::filament/admin/clusters/orders/resources/purchase-agreement.table.filters.valid-from')),
                        DateConstraint::make('ends_at')
                            ->label(__('purchases::filament/admin/clusters/orders/resources/purchase-agreement.table.filters.valid-to')),
                        TextConstraint::make('reference')
                            ->label(__('purchases::filament/admin/clusters/orders/resources/purchase-agreement.table.filters.reference'))
                            ->icon('heroicon-o-identification'),
                        DateConstraint::make('created_at')
                            ->label(__('purchases::filament/admin/clusters/orders/resources/purchase-agreement.table.filters.created-at')),
                        DateConstraint::make('updated_at')
                            ->label(__('purchases::filament/admin/clusters/orders/resources/purchase-agreement.table.filters.updated-at')),
                    ]))->filter()->values()->all()),
            ], layout: FiltersLayout::Modal)
            ->filtersTriggerAction(
                fn (Action $action) => $action
                    ->slideOver(),
            )
            ->filtersFormColumns(2)
            ->recordActions([
                ActionGroup::make([
                    ViewAction::make()
                        ->hidden(fn ($record) => $record->trashed()),
                    EditAction::make()
                        ->hidden(fn ($record) => $record->trashed()),
                    RestoreAction::make()
                        ->successNotification(
                            Notification::make()
                                ->success()
                                ->title(__('purchases::filament/admin/clusters/orders/resources/purchase-agreement.table.actions.restore.notification.title'))
                                ->body(__('purchases::filament/admin/clusters/orders/resources/purchase-agreement.table.actions.restore.notification.body')),
                        ),
                    DeleteAction::make()
                        ->hidden(fn (Model $record) => $record->state == RequisitionState::CLOSED)
                        ->successNotification(
                            Notification::make()
                                ->success()
                                ->title(__('purchases::filament/admin/clusters/orders/resources/purchase-agreement.table.actions.delete.notification.title'))
                                ->body(__('purchases::filament/admin/clusters/orders/resources/purchase-agreement.table.actions.delete.notification.body')),
                        ),
                    ForceDeleteAction::make()
                        ->action(function (Requisition $record) {
                            try {
                                $record->forceDelete();
                            } catch (QueryException $e) {
                                Notification::make()
                                    ->danger()
                                    ->title(__('purchases::filament/admin/clusters/orders/resources/purchase-agreement.table.actions.force-delete.notification.error.title'))
                                    ->body(__('purchases::filament/admin/clusters/orders/resources/purchase-agreement.table.actions.force-delete.notification.error.body'))
                                    ->send();
                            }
                        })
                        ->successNotification(
                            Notification::make()
                                ->success()
                                ->title(__('purchases::filament/admin/clusters/orders/resources/purchase-agreement.table.actions.force-delete.notification.success.title'))
                                ->body(__('purchases::filament/admin/clusters/orders/resources/purchase-agreement.table.actions.force-delete.notification.success.body')),
                        ),
                ]),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    RestoreBulkAction::make()
                        ->successNotification(
                            Notification::make()
                                ->success()
                                ->title(__('purchases::filament/admin/clusters/orders/resources/purchase-agreement.table.bulk-actions.restore.notification.title'))
                                ->body(__('purchases::filament/admin/clusters/orders/resources/purchase-agreement.table.bulk-actions.restore.notification.body')),
                        ),
                    DeleteBulkAction::make()
                        ->successNotification(
                            Notification::make()
                                ->success()
                                ->title(__('purchases::filament/admin/clusters/orders/resources/purchase-agreement.table.bulk-actions.delete.notification.title'))
                                ->body(__('purchases::filament/admin/clusters/orders/resources/purchase-agreement.table.bulk-actions.delete.notification.body')),
                        ),
                    ForceDeleteBulkAction::make()
                        ->action(function (Collection $records) {
                            try {
                                $records->each(fn (Model $record) => $record->forceDelete());
                            } catch (QueryException $e) {
                                Notification::make()
                                    ->danger()
                                    ->title(__('purchases::filament/admin/clusters/orders/resources/purchase-agreement.table.bulk-actions.force-delete.notification.error.title'))
                                    ->body(__('purchases::filament/admin/clusters/orders/resources/purchase-agreement.table.bulk-actions.force-delete.notification.error.body'))
                                    ->send();
                            }
                        })
                        ->successNotification(
                            Notification::make()
                                ->success()
                                ->title(__('purchases::filament/admin/clusters/orders/resources/purchase-agreement.table.bulk-actions.force-delete.notification.success.title'))
                                ->body(__('purchases::filament/admin/clusters/orders/resources/purchase-agreement.table.bulk-actions.force-delete.notification.success.body')),
                        ),
                ]),
            ])
            ->checkIfRecordIsSelectableUsing(
                fn (Model $record): bool => PurchaseAgreementResource::can('delete', $record) && $record->state !== RequisitionState::CLOSED,
            );
    }
}
