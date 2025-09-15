<?php

namespace Webkul\Employee\Filament\Clusters\Configurations\Resources;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\CreateAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\TextInput;
use Filament\Infolists\Components\TextEntry;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\QueryBuilder;
use Filament\Tables\Filters\QueryBuilder\Constraints\DateConstraint;
use Filament\Tables\Filters\QueryBuilder\Constraints\RelationshipConstraint;
use Filament\Tables\Filters\QueryBuilder\Constraints\RelationshipConstraint\Operators\IsRelatedToOperator;
use Filament\Tables\Filters\QueryBuilder\Constraints\TextConstraint;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Auth;
use Webkul\Employee\Filament\Clusters\Configurations;
use Webkul\Employee\Filament\Clusters\Configurations\Resources\DepartureReasonResource\Pages\ListDepartureReasons;
use Webkul\Employee\Models\DepartureReason;

class DepartureReasonResource extends Resource
{
    protected static ?string $model = DepartureReason::class;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-fire';

    protected static ?string $cluster = Configurations::class;

    public static function getModelLabel(): string
    {
        return __('employees::filament/clusters/configurations/resources/departure-reason.title');
    }

    public static function getNavigationGroup(): string
    {
        return __('employees::filament/clusters/configurations/resources/departure-reason.navigation.group');
    }

    public static function getNavigationLabel(): string
    {
        return __('employees::filament/clusters/configurations/resources/departure-reason.navigation.title');
    }

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('name')
                    ->label(__('employees::filament/clusters/configurations/resources/departure-reason.form.fields.name'))
                    ->required(),
                Hidden::make('creator_id')
                    ->default(Auth::user()->id),
            ])->columns(1);
    }

    public static function infolist(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextEntry::make('name')
                    ->placeholder('—')
                    ->label(__('employees::filament/clusters/configurations/resources/departure-reason.infolist.name')),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('id')
                    ->label(__('employees::filament/clusters/configurations/resources/departure-reason.table.columns.id'))
                    ->searchable()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('name')
                    ->label(__('employees::filament/clusters/configurations/resources/departure-reason.table.columns.name'))
                    ->searchable()
                    ->sortable(),
                TextColumn::make('createdBy.name')
                    ->label(__('employees::filament/clusters/configurations/resources/departure-reason.table.columns.created-by'))
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('created_at')
                    ->label(__('employees::filament/clusters/configurations/resources/departure-reason.table.columns.created-at'))
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('updated_at')
                    ->label(__('employees::filament/clusters/configurations/resources/departure-reason.table.columns.updated-at'))
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                QueryBuilder::make()
                    ->constraintPickerColumns(2)
                    ->constraints([
                        TextConstraint::make('name')
                            ->label(__('employees::filament/clusters/configurations/resources/departure-reason.table.filters.name'))
                            ->icon('heroicon-o-user'),
                        RelationshipConstraint::make('employees')
                            ->label(__('employees::filament/clusters/configurations/resources/departure-reason.table.filters.employee'))
                            ->icon('heroicon-o-user')
                            ->multiple()
                            ->selectable(
                                IsRelatedToOperator::make()
                                    ->titleAttribute('name')
                                    ->searchable()
                                    ->multiple()
                                    ->preload(),
                            ),
                        RelationshipConstraint::make('createdBy')
                            ->label(__('employees::filament/clusters/configurations/resources/departure-reason.table.filters.created-by'))
                            ->icon('heroicon-o-user')
                            ->multiple()
                            ->selectable(
                                IsRelatedToOperator::make()
                                    ->titleAttribute('name')
                                    ->searchable()
                                    ->multiple()
                                    ->preload(),
                            ),
                        DateConstraint::make('created_at')
                            ->label(__('employees::filament/clusters/configurations/resources/departure-reason.table.filters.created-at')),
                        DateConstraint::make('updated_at')
                            ->label(__('employees::filament/clusters/configurations/resources/departure-reason.table.filters.updated-at')),
                    ]),
            ])
            ->recordActions([
                ViewAction::make(),
                EditAction::make()
                    ->successNotification(
                        Notification::make()
                            ->success()
                            ->title(__('employees::filament/clusters/configurations/resources/departure-reason.table.actions.edit.notification.title'))
                            ->body(__('employees::filament/clusters/configurations/resources/departure-reason.table.actions.edit.notification.body')),
                    )
                    ->mutateDataUsing(function (array $data): array {
                        $data['reason_code'] = $data['reason_code'] ?? crc32($data['name']) % 100000;

                        $data['creator_id'] = $data['creator_id'] ?? Auth::user()->id;

                        return $data;
                    }),
                DeleteAction::make()
                    ->successNotification(
                        Notification::make()
                            ->success()
                            ->title(__('employees::filament/clusters/configurations/resources/departure-reason.table.actions.delete.notification.title'))
                            ->body(__('employees::filament/clusters/configurations/resources/departure-reason.table.actions.delete.notification.body')),
                    ),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make()
                        ->successNotification(
                            Notification::make()
                                ->success()
                                ->title(__('employees::filament/clusters/configurations/resources/departure-reason.table.bulk-actions.delete.notification.title'))
                                ->body(__('employees::filament/clusters/configurations/resources/departure-reason.table.bulk-actions.delete.notification.body')),
                        ),
                ]),
            ])
            ->emptyStateActions([
                CreateAction::make()
                    ->icon('heroicon-o-plus-circle')
                    ->successNotification(
                        Notification::make()
                            ->success()
                            ->title(__('employees::filament/clusters/configurations/resources/departure-reason.table.empty-state-action.create.notification.title'))
                            ->body(__('employees::filament/clusters/configurations/resources/departure-reason.table.empty-state-action.create.notification.body')),
                    ),
            ])
            ->reorderable('sort')
            ->defaultSort('sort', 'desc');
    }

    public static function getPages(): array
    {
        return [
            'index' => ListDepartureReasons::route('/'),
        ];
    }
}
