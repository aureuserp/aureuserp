<?php

namespace Webkul\Employee\Filament\Clusters\Configurations\Resources;

use Filament\Forms;
use Filament\Forms\Form;
use Filament\Infolists;
use Filament\Infolists\Infolist;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Filters\QueryBuilder\Constraints\RelationshipConstraint\Operators\IsRelatedToOperator;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Webkul\Employee\Filament\Clusters\Configurations;
use Webkul\Employee\Filament\Clusters\Configurations\Resources\EmployeeCategoryResource\Pages;
use Webkul\Employee\Models\EmployeeCategory;

class EmployeeCategoryResource extends Resource
{
    protected static ?string $model = EmployeeCategory::class;

    protected static ?string $navigationIcon = 'heroicon-o-tag';

    protected static ?string $cluster = Configurations::class;

    public static function getModelLabel(): string
    {
        return __('employees::filament/clusters/configurations/resources/employee-category.title');
    }

    public static function getNavigationGroup(): string
    {
        return __('employees::filament/clusters/configurations/resources/employee-category.navigation.group');
    }

    public static function getNavigationLabel(): string
    {
        return __('employees::filament/clusters/configurations/resources/employee-category.navigation.title');
    }

    public static function getGloballySearchableAttributes(): array
    {
        return ['name'];
    }

    public static function getGlobalSearchResultDetails(Model $record): array
    {
        return [
            __('employees::filament/clusters/configurations/resources/employee-category.global-search.name') => $record->name ?? '—',
        ];
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('name')
                    ->label(__('employees::filament/clusters/configurations/resources/employee-category.form.fields.name'))
                    ->required()
                    ->unique(ignoreRecord: true)
                    ->placeholder('Enter the name of the tag'),
                Forms\Components\ColorPicker::make('color')
                    ->label(__('employees::filament/clusters/configurations/resources/employee-category.form.fields.color'))
                    ->hexColor(),
                Forms\Components\Hidden::make('creator_id')
                    ->default(Auth::user()->id),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('id')
                    ->label(__('employees::filament/clusters/configurations/resources/employee-category.table.columns.id'))
                    ->searchable()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('name')
                    ->label(__('employees::filament/clusters/configurations/resources/employee-category.table.columns.name'))
                    ->searchable()
                    ->sortable(),
                Tables\Columns\ColorColumn::make('color')
                    ->searchable()
                    ->label(__('employees::filament/clusters/configurations/resources/employee-category.table.columns.color'))
                    ->toggleable(isToggledHiddenByDefault: false)
                    ->sortable(),
                Tables\Columns\TextColumn::make('createdBy.name')
                    ->label(__('employees::filament/clusters/configurations/resources/employee-category.table.columns.created-by'))
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('created_at')
                    ->label(__('employees::filament/clusters/configurations/resources/employee-category.table.columns.created-at'))
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('updated_at')
                    ->label(__('employees::filament/clusters/configurations/resources/employee-category.table.columns.updated-at'))
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\QueryBuilder::make()
                    ->constraintPickerColumns(2)
                    ->constraints([
                        Tables\Filters\QueryBuilder\Constraints\TextConstraint::make('name')
                            ->label(__('employees::filament/clusters/configurations/resources/employee-category.table.filters.name'))
                            ->icon('heroicon-o-user'),
                        Tables\Filters\QueryBuilder\Constraints\RelationshipConstraint::make('createdBy')
                            ->label(__('employees::filament/clusters/configurations/resources/employee-category.table.filters.created-by'))
                            ->icon('heroicon-o-user')
                            ->multiple()
                            ->selectable(
                                IsRelatedToOperator::make()
                                    ->titleAttribute('name')
                                    ->searchable()
                                    ->multiple()
                                    ->preload(),
                            ),
                        Tables\Filters\QueryBuilder\Constraints\DateConstraint::make('created_at')
                            ->label(__('employees::filament/clusters/configurations/resources/employee-category.table.filters.created-by')),
                        Tables\Filters\QueryBuilder\Constraints\DateConstraint::make('updated_at')
                            ->label(__('employees::filament/clusters/configurations/resources/employee-category.table.filters.updated-by')),
                    ]),
            ])
            ->groups([
                Tables\Grouping\Group::make('name')
                    ->label('Job Position')
                    ->label(__('employees::filament/clusters/configurations/resources/employee-category.table.groups.job-position'))
                    ->collapsible(),
                Tables\Grouping\Group::make('color')
                    ->label(__('employees::filament/clusters/configurations/resources/employee-category.table.groups.color'))
                    ->collapsible(),
                Tables\Grouping\Group::make('createdBy.name')
                    ->label(__('employees::filament/clusters/configurations/resources/employee-category.table.groups.created-by'))
                    ->collapsible(),
                Tables\Grouping\Group::make('created_at')
                    ->label(__('employees::filament/clusters/configurations/resources/employee-category.table.groups.created-at'))
                    ->collapsible(),
                Tables\Grouping\Group::make('updated_at')
                    ->label(__('employees::filament/clusters/configurations/resources/employee-category.table.groups.updated-at'))
                    ->date()
                    ->collapsible(),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make()
                    ->mutateFormDataUsing(function (array $data): array {
                        $data['color'] = $data['color'] ?? fake()->hexColor();

                        return $data;
                    })
                    ->successNotification(
                        Notification::make()
                            ->success()
                            ->title(__('employees::filament/clusters/configurations/resources/employee-category.table.actions.edit.notification.title'))
                            ->body(__('employees::filament/clusters/configurations/resources/employee-category.table.actions.edit.notification.body'))
                    ),
                Tables\Actions\DeleteAction::make()
                    ->successNotification(
                        Notification::make()
                            ->success()
                            ->title(__('employees::filament/clusters/configurations/resources/employee-category.table.actions.delete.notification.title'))
                            ->body(__('employees::filament/clusters/configurations/resources/employee-category.table.actions.delete.notification.body'))
                    ),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make()
                        ->successNotification(
                            Notification::make()
                                ->success()
                                ->title(__('employees::filament/clusters/configurations/resources/employee-category.table.bulk-actions.delete.notification.title'))
                                ->body(__('employees::filament/clusters/configurations/resources/employee-category.table.bulk-actions.delete.notification.body'))
                        ),
                ]),
            ])
            ->emptyStateActions([
                Tables\Actions\CreateAction::make()
                    ->icon('heroicon-o-plus-circle')
                    ->successNotification(
                        Notification::make()
                            ->success()
                            ->title(__('employees::filament/clusters/configurations/resources/employee-category.table.empty-state-action.create.notification.title'))
                            ->body(__('employees::filament/clusters/configurations/resources/employee-category.table.empty-state-action.create.notification.body'))
                    ),
            ]);
    }

    public static function infolist(Infolist $infolist): Infolist
    {
        return $infolist
            ->schema([
                Infolists\Components\TextEntry::make('name')
                    ->placeholder('—')
                    ->icon('heroicon-o-tag')
                    ->label(__('employees::filament/clusters/configurations/resources/employee-category.infolist.name')),
                Infolists\Components\ColorEntry::make('color')
                    ->placeholder('—')
                    ->label(__('employees::filament/clusters/configurations/resources/employee-category.infolist.color')),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListEmployeeCategories::route('/'),
        ];
    }
}
