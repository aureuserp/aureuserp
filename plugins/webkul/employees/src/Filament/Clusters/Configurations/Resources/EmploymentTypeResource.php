<?php

namespace Webkul\Employee\Filament\Clusters\Configurations\Resources;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\CreateAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Select;
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
use Filament\Tables\Grouping\Group;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Auth;
use Webkul\Employee\Filament\Clusters\Configurations;
use Webkul\Employee\Filament\Clusters\Configurations\Resources\EmploymentTypeResource\Pages\ListEmploymentTypes;
use Webkul\Employee\Models\EmploymentType;

class EmploymentTypeResource extends Resource
{
    protected static ?string $model = EmploymentType::class;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-cube-transparent';

    public static function getModelLabel(): string
    {
        return __('employees::filament/clusters/configurations/resources/employment-type.title');
    }

    public static function getNavigationGroup(): string
    {
        return __('employees::filament/clusters/configurations/resources/employment-type.navigation.group');
    }

    public static function getNavigationLabel(): string
    {
        return __('employees::filament/clusters/configurations/resources/employment-type.navigation.title');
    }

    protected static ?string $cluster = Configurations::class;

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Hidden::make('creator_id')
                    ->default(Auth::user()->id),
                TextInput::make('name')
                    ->label(__('employees::filament/clusters/configurations/resources/employment-type.form.fields.name'))
                    ->required()
                    ->maxLength(255)
                    ->live(onBlur: true),
                TextInput::make('code')
                    ->label(__('employees::filament/clusters/configurations/resources/employment-type.form.fields.code')),
                Select::make('country_id')
                    ->searchable()
                    ->preload()
                    ->label(__('employees::filament/clusters/configurations/resources/employment-type.form.fields.country'))
                    ->relationship('country', 'name'),
            ])
            ->columns(2);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('id')
                    ->label(__('employees::filament/clusters/configurations/resources/employment-type.table.columns.id'))
                    ->searchable()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('name')
                    ->sortable()
                    ->searchable()
                    ->label(__('employees::filament/clusters/configurations/resources/employment-type.table.columns.name')),
                TextColumn::make('code')
                    ->sortable()
                    ->searchable()
                    ->label(__('employees::filament/clusters/configurations/resources/employment-type.table.columns.code')),
                TextColumn::make('country.name')
                    ->sortable()
                    ->searchable()
                    ->label(__('employees::filament/clusters/configurations/resources/employment-type.table.columns.country')),
                TextColumn::make('createdBy.name')
                    ->label(__('employees::filament/clusters/configurations/resources/employment-type.table.columns.created-by'))
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('created_at')
                    ->label(__('employees::filament/clusters/configurations/resources/employment-type.table.columns.created-at'))
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('updated_at')
                    ->label(__('employees::filament/clusters/configurations/resources/employment-type.table.columns.updated-at'))
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                QueryBuilder::make()
                    ->constraintPickerColumns(2)
                    ->constraints([
                        TextConstraint::make('name')
                            ->label(__('employees::filament/clusters/configurations/resources/employment-type.table.filters.name'))
                            ->icon('heroicon-o-user'),
                        RelationshipConstraint::make('country')
                            ->label(__('employees::filament/clusters/configurations/resources/employment-type.table.filters.country'))
                            ->icon('heroicon-o-map')
                            ->multiple()
                            ->selectable(
                                IsRelatedToOperator::make()
                                    ->titleAttribute('name')
                                    ->searchable()
                                    ->multiple()
                                    ->preload(),
                            ),
                        RelationshipConstraint::make('createdBy')
                            ->label(__('employees::filament/clusters/configurations/resources/employment-type.table.filters.created-by'))
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
                            ->label(__('employees::filament/clusters/configurations/resources/employment-type.table.filters.created-at')),
                        DateConstraint::make('updated_at')
                            ->label(__('employees::filament/clusters/configurations/resources/employment-type.table.filters.updated-at')),
                    ]),
            ])
            ->groups([
                Group::make('name')
                    ->label(__('employees::filament/clusters/configurations/resources/employment-type.table.groups.name'))
                    ->collapsible(),
                Group::make('code')
                    ->label(__('employees::filament/clusters/configurations/resources/employment-type.table.groups.code'))
                    ->collapsible(),
                Group::make('country.name')
                    ->label(__('employees::filament/clusters/configurations/resources/employment-type.table.groups.country'))
                    ->collapsible(),
                Group::make('createdBy.name')
                    ->label(__('employees::filament/clusters/configurations/resources/employment-type.table.groups.created-by'))
                    ->collapsible(),
                Group::make('created_at')
                    ->label(__('employees::filament/clusters/configurations/resources/employment-type.table.groups.created-at'))
                    ->collapsible(),
                Group::make('updated_at')
                    ->label(__('employees::filament/clusters/configurations/resources/employment-type.table.groups.updated-at'))
                    ->date()
                    ->collapsible(),
            ])
            ->recordActions([
                ViewAction::make(),
                EditAction::make()
                    ->mutateDataUsing(function (array $data): array {
                        $data['code'] = $data['code'] ?? $data['name'];

                        return $data;
                    })
                    ->successNotification(
                        Notification::make()
                            ->success()
                            ->title(__('employees::filament/clusters/configurations/resources/employment-type.table.actions.edit.notification.title'))
                            ->body(__('employees::filament/clusters/configurations/resources/employment-type.table.actions.edit.notification.body'))
                    ),
                DeleteAction::make()
                    ->successNotification(
                        Notification::make()
                            ->success()
                            ->title(__('employees::filament/clusters/configurations/resources/employment-type.table.actions.delete.notification.title'))
                            ->body(__('employees::filament/clusters/configurations/resources/employment-type.table.actions.delete.notification.body'))
                    ),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make()
                        ->successNotification(
                            Notification::make()
                                ->success()
                                ->title(__('employees::filament/clusters/configurations/resources/employment-type.table.bulk-actions.delete.notification.title'))
                                ->body(__('employees::filament/clusters/configurations/resources/employment-type.table.bulk-actions.delete.notification.body'))
                        ),
                ]),
            ])
            ->emptyStateActions([
                CreateAction::make()
                    ->successNotification(
                        Notification::make()
                            ->success()
                            ->title(__('employees::filament/clusters/configurations/resources/employment-type.table.empty-state-actions.create.notification.title'))
                            ->body(__('employees::filament/clusters/configurations/resources/employment-type.table.empty-state-actions.create.notification.body'))
                    )
                    ->icon('heroicon-o-plus-circle'),
            ])
            ->reorderable('sort')
            ->defaultSort('sort', 'desc');
    }

    public static function infolist(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextEntry::make('name')
                    ->icon('heroicon-o-user')
                    ->placeholder('—')
                    ->label(__('employees::filament/clusters/configurations/resources/employment-type.infolist.entries.name')),
                TextEntry::make('code')
                    ->placeholder('—')
                    ->icon('heroicon-o-user')
                    ->label(__('employees::filament/clusters/configurations/resources/employment-type.infolist.entries.code')),
                TextEntry::make('country.name')
                    ->placeholder('—')
                    ->icon('heroicon-o-map')
                    ->label(__('employees::filament/clusters/configurations/resources/employment-type.infolist.entries.country')),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListEmploymentTypes::route('/'),
        ];
    }
}
