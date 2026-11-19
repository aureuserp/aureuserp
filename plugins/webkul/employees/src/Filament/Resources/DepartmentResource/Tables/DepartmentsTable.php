<?php

namespace Webkul\Employee\Filament\Resources\DepartmentResource\Tables;

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
use Filament\AvatarProviders\UiAvatarsProvider;
use Filament\Notifications\Notification;
use Filament\Support\Enums\FontWeight;
use Filament\Support\Enums\TextSize;
use Filament\Tables;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\Layout\Split;
use Filament\Tables\Columns\Layout\Stack;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\QueryBuilder;
use Filament\Tables\Filters\QueryBuilder\Constraints\DateConstraint;
use Filament\Tables\Filters\QueryBuilder\Constraints\RelationshipConstraint;
use Filament\Tables\Filters\QueryBuilder\Constraints\RelationshipConstraint\Operators\IsRelatedToOperator;
use Filament\Tables\Filters\QueryBuilder\Constraints\TextConstraint;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Webkul\Chatter\Filament\Actions\ActivityTableAction;
use Webkul\Employee\Models\Department;

class DepartmentsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                Stack::make([
                    Split::make([
                        ImageColumn::make('manager.partner.avatar')
                            ->circular()
                            ->imageSize(64)
                            ->defaultImageUrl(fn (Department $record): string => (new UiAvatarsProvider)->get($record))
                            ->grow(false),

                        Stack::make([
                            TextColumn::make('name')
                                ->label(__('employees::filament/resources/department.table.columns.name'))
                                ->weight(FontWeight::Bold)
                                ->size(TextSize::Large)
                                ->searchable()
                                ->sortable()
                                ->lineClamp(1)
                                ->tooltip(fn (Department $record): string => $record->name)
                                ->extraAttributes(['class' => 'fi-ta-card-title']),

                            Stack::make([
                                TextColumn::make('parent.name')
                                    ->label(__('employees::filament/resources/department.table.columns.parent'))
                                    ->icon('heroicon-o-building-library')
                                    ->weight(FontWeight::Medium)
                                    ->tooltip(fn (Department $record): ?string => $record->parent?->complete_name ?? $record->parent?->name)
                                    ->lineClamp(1)
                                    ->extraAttributes(['class' => 'break-all']),
                            ])
                                ->visible(fn (Department $record): bool => filled($record->parent_id)),

                            Stack::make([
                                TextColumn::make('manager.name')
                                    ->label(__('employees::filament/resources/department.table.columns.manager-name'))
                                    ->icon('heroicon-o-user')
                                    ->color('gray')
                                    ->sortable()
                                    ->searchable()
                                    ->tooltip(fn (Department $record): ?string => $record->manager?->name)
                                    ->lineClamp(1)
                                    ->extraAttributes(['class' => 'break-all']),
                            ])
                                ->visible(fn (Department $record): bool => filled($record->manager_id)),

                            Stack::make([
                                TextColumn::make('company.name')
                                    ->label(__('employees::filament/resources/department.table.columns.company-name'))
                                    ->icon('heroicon-o-building-office-2')
                                    ->color('gray')
                                    ->searchable()
                                    ->tooltip(fn (Department $record): ?string => $record->company?->name)
                                    ->lineClamp(1)
                                    ->extraAttributes(['class' => 'break-all']),
                            ])
                                ->visible(fn (Department $record): bool => filled($record->company_id)),
                        ])->space(1),
                    ])->from('sm'),

                    TextColumn::make('employees_count')
                        ->label(__('employees::filament/resources/department.table.columns.employees'))
                        ->badge()
                        ->icon('heroicon-m-user-group')
                        ->color('gray')
                        ->formatStateUsing(fn ($state): string => __('employees::filament/resources/department.table.columns.employees-count', ['count' => $state])),
                ])->space(3),
            ])
            ->recordClasses('fi-ta-card-select')
            ->contentGrid([
                'sm'  => 1,
                'md'  => 2,
                'xl'  => 3,
                '2xl' => 4,
            ])
            ->paginated([
                12,
                24,
                36,
                'all',
            ])
            ->modifyQueryUsing(fn (Builder $query) => $query->with(['manager.partner', 'parent', 'company'])->withCount('employees'))
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
            ->filters([
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
            ])
            ->recordActions([
                ActivityTableAction::make()
                    ->hidden(fn (Department $record): bool => $record->trashed())
                    ->extraAttributes(['class' => 'fi-ta-card-top-right']),
                ActionGroup::make([
                    ViewAction::make()
                        ->hidden(fn (Department $record): bool => $record->trashed()),
                    EditAction::make()
                        ->hidden(fn (Department $record): bool => $record->trashed()),
                    DeleteAction::make()
                        ->successNotification(
                            Notification::make()
                                ->success()
                                ->title(__('employees::filament/resources/department.table.actions.delete.notification.title'))
                                ->body(__('employees::filament/resources/department.table.actions.delete.notification.body')),
                        ),
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
                ])
                    ->icon('heroicon-m-ellipsis-vertical')
                    ->color('gray')
                    ->extraDropdownAttributes(['class' => 'ms-auto']),
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
