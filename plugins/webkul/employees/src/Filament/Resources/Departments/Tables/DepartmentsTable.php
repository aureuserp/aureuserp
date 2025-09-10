<?php

namespace Webkul\Employee\Filament\Resources\Departments\Tables;

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
use Filament\Support\Enums\FontWeight;
use Filament\Tables;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\Layout\Stack;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\QueryBuilder;
use Filament\Tables\Filters\QueryBuilder\Constraints\DateConstraint;
use Filament\Tables\Filters\QueryBuilder\Constraints\RelationshipConstraint;
use Filament\Tables\Filters\QueryBuilder\Constraints\RelationshipConstraint\Operators\IsRelatedToOperator;
use Filament\Tables\Filters\QueryBuilder\Constraints\TextConstraint;
use Filament\Tables\Table;
use Webkul\Employee\Filament\Resources\Departments\DepartmentResource;
use Webkul\Field\Filament\Traits\HasCustomFields;

class DepartmentsTable
{
    use HasCustomFields;

    public static function getModel()
    {
        return DepartmentResource::getModel();
    }

    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                Stack::make([
                    ImageColumn::make('manager.partner.avatar')
                        ->height(35)
                        ->circular()
                        ->width(35),
                    Stack::make(schema: [
                        TextColumn::make('name')
                            ->weight(FontWeight::Bold)
                            ->label(__('employees::filament/resources/department.table.columns.name'))
                            ->searchable()
                            ->sortable(),
                        Stack::make([
                            TextColumn::make('manager.name')
                                ->icon('heroicon-m-briefcase')
                                ->label(__('employees::filament/resources/department.table.columns.manager-name'))
                                ->sortable()
                                ->searchable(),
                        ])
                            ->visible(fn ($record) => filled($record?->manager?->name)),
                        Stack::make([
                            TextColumn::make('company.name')
                                ->searchable()
                                ->label(__('employees::filament/resources/department.table.columns.company-name'))
                                ->icon('heroicon-m-building-office-2')
                                ->searchable(),
                        ])
                            ->visible(fn ($record) => filled($record?->company?->name)),
                    ])->space(1),
                ])->space(4),
            ])
            ->contentGrid([
                'md' => 2,
                'xl' => 4,
            ])
            ->groups([
                Tables\Grouping\Group::make('name')
                    ->label(__('employees::filament/resources/department.table.groups.name'))
                    ->collapsible(),
                Tables\Grouping\Group::make('company.name')
                    ->label(__('employees::filament/resources/department.table.groups.company'))
                    ->collapsible(),
                Tables\Grouping\Group::make('manager.name')
                    ->label(__('employees::filament/resources/department.table.groups.manager'))
                    ->collapsible(),
                Tables\Grouping\Group::make('created_at')
                    ->label(__('employees::filament/resources/department.table.groups.created-at'))
                    ->collapsible(),
                Tables\Grouping\Group::make('updated_at')
                    ->label(__('employees::filament/resources/department.table.groups.updated-at'))
                    ->date()
                    ->collapsible(),
            ])
            ->filtersFormColumns(2)
            ->filters(static::mergeCustomTableFilters([
                QueryBuilder::make()
                    ->constraintPickerColumns(2)
                    ->constraints([
                        TextConstraint::make('name')
                            ->label(__('employees::filament/resources/department.table.filters.name'))
                            ->icon('heroicon-o-building-office-2'),
                        RelationshipConstraint::make('manager')
                            ->label(__('employees::filament/resources/department.table.filters.manager-name'))
                            ->icon('heroicon-o-user')
                            ->multiple()
                            ->selectable(
                                IsRelatedToOperator::make()
                                    ->titleAttribute('name')
                                    ->label(__('employees::filament/resources/department.table.filters.manager-name'))
                                    ->searchable()
                                    ->multiple()
                                    ->preload(),
                            ),
                        RelationshipConstraint::make('company')
                            ->label(__('employees::filament/resources/department.table.filters.company-name'))
                            ->icon('heroicon-o-building-office-2')
                            ->multiple()
                            ->selectable(
                                IsRelatedToOperator::make()
                                    ->titleAttribute('name')
                                    ->label(__('employees::filament/resources/department.table.filters.company-name'))
                                    ->searchable()
                                    ->multiple()
                                    ->preload(),
                            ),
                        DateConstraint::make('created_at')
                            ->label(__('employees::filament/resources/department.table.filters.created-at')),
                        DateConstraint::make('updated_at')
                            ->label(__('employees::filament/resources/department.table.filters.updated-at')),
                    ]),
            ]))
            ->recordActions([
                ViewAction::make(),
                EditAction::make(),
                DeleteAction::make()
                    ->successNotification(
                        Notification::make()
                            ->success()
                            ->title(__('employees::filament/resources/department.table.actions.delete.notification.title'))
                            ->body(__('employees::filament/resources/department.table.actions.delete.notification.body')),
                    ),
                ActionGroup::make([
                    RestoreAction::make()
                        ->successNotification(
                            Notification::make()
                                ->success()
                                ->title(__('employees::filament/resources/department.table.actions.restore.notification.title'))
                                ->body(__('employees::filament/resources/department.table.actions.restore.notification.body')),
                        ),
                    ForceDeleteAction::make()
                        ->successNotification(
                            Notification::make()
                                ->success()
                                ->title(__('employees::filament/resources/department.table.actions.force-delete.notification.title'))
                                ->body(__('employees::filament/resources/department.table.actions.force-delete.notification.body')),
                        ),
                ]),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    RestoreBulkAction::make()
                        ->successNotification(
                            Notification::make()
                                ->success()
                                ->title(__('employees::filament/resources/department.table.bulk-actions.restore.notification.title'))
                                ->body(__('employees::filament/resources/department.table.bulk-actions.restore.notification.body')),
                        ),
                    DeleteBulkAction::make()
                        ->successNotification(
                            Notification::make()
                                ->success()
                                ->title(__('employees::filament/resources/department.table.bulk-actions.delete.notification.title'))
                                ->body(__('employees::filament/resources/department.table.bulk-actions.delete.notification.body')),
                        ),
                    ForceDeleteBulkAction::make()
                        ->successNotification(
                            Notification::make()
                                ->success()
                                ->title(__('employees::filament/resources/department.table.bulk-actions.force-delete.notification.title'))
                                ->body(__('employees::filament/resources/department.table.bulk-actions.force-delete.notification.body')),
                        ),
                ]),
            ]);
    }
}
