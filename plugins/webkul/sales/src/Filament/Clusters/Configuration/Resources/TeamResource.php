<?php

namespace Webkul\Sale\Filament\Clusters\Configuration\Resources;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ForceDeleteAction;
use Filament\Actions\ForceDeleteBulkAction;
use Filament\Actions\RestoreAction;
use Filament\Actions\RestoreBulkAction;
use Filament\Actions\ViewAction;
use Filament\Forms\Components\ColorPicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Infolists\Components\ColorEntry;
use Filament\Infolists\Components\IconEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Fieldset;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\ColorColumn;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\QueryBuilder;
use Filament\Tables\Filters\QueryBuilder\Constraints\DateConstraint;
use Filament\Tables\Filters\QueryBuilder\Constraints\RelationshipConstraint;
use Filament\Tables\Filters\QueryBuilder\Constraints\RelationshipConstraint\Operators\IsRelatedToOperator;
use Filament\Tables\Filters\QueryBuilder\Constraints\TextConstraint;
use Filament\Tables\Grouping\Group;
use Filament\Tables\Table;
use Webkul\Sale\Filament\Clusters\Configuration;
use Webkul\Sale\Filament\Clusters\Configuration\Resources\TeamResource\Pages\CreateTeam;
use Webkul\Sale\Filament\Clusters\Configuration\Resources\TeamResource\Pages\EditTeam;
use Webkul\Sale\Filament\Clusters\Configuration\Resources\TeamResource\Pages\ListTeams;
use Webkul\Sale\Filament\Clusters\Configuration\Resources\TeamResource\Pages\ViewTeam;
use Webkul\Sale\Models\Team;

class TeamResource extends Resource
{
    protected static ?string $model = Team::class;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-users';

    protected static ?string $cluster = Configuration::class;

    protected static bool $shouldRegisterNavigation = false;

    public static function getModelLabel(): string
    {
        return __('sales::filament/clusters/configurations/resources/team.title');
    }

    public static function getNavigationLabel(): string
    {
        return __('sales::filament/clusters/configurations/resources/team.navigation.title');
    }

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make()
                    ->schema([
                        Grid::make()
                            ->schema([
                                TextInput::make('name')
                                    ->required()
                                    ->label(__('sales::filament/clusters/configurations/resources/team.form.sections.fields.name'))
                                    ->maxLength(255)
                                    ->extraInputAttributes(['style' => 'font-size: 1.5rem;height: 3rem;'])
                                    ->columnSpan(1),
                            ])->columns(2),
                        Fieldset::make(__('sales::filament/clusters/configurations/resources/team.form.sections.fields.fieldset.team-details.title'))
                            ->schema([
                                Select::make('user_id')
                                    ->relationship('user', 'name')
                                    ->preload()
                                    ->label(__('sales::filament/clusters/configurations/resources/team.form.sections.fields.fieldset.team-details.fields.team-leader'))
                                    ->searchable(),
                                Select::make('company_id')
                                    ->relationship('company', 'name')
                                    ->preload()
                                    ->label(__('sales::filament/clusters/configurations/resources/team.form.sections.fields.fieldset.team-details.fields.company'))
                                    ->searchable(),
                                TextInput::make('invoiced_target')
                                    ->numeric()
                                    ->default(0)
                                    ->minValue(0)
                                    ->maxValue(99999999999)
                                    ->label(__('sales::filament/clusters/configurations/resources/team.form.sections.fields.fieldset.team-details.fields.invoiced-target'))
                                    ->autocomplete(false)
                                    ->suffix(__('sales::filament/clusters/configurations/resources/team.form.sections.fields.fieldset.team-details.fields.invoiced-target-suffix')),
                                ColorPicker::make('color')
                                    ->label(__('sales::filament/clusters/configurations/resources/team.form.sections.fields.fieldset.team-details.fields.color'))
                                    ->hexColor(),
                                Select::make('sales_team_members')
                                    ->relationship('members', 'name')
                                    ->multiple()
                                    ->searchable()
                                    ->preload()
                                    ->label(__('sales::filament/clusters/configurations/resources/team.form.sections.fields.fieldset.team-details.fields.members')),
                            ])->columns(2),
                        Toggle::make('is_active')
                            ->inline(false)
                            ->label(__('sales::filament/clusters/configurations/resources/team.form.sections.fields.status')),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('id')
                    ->dateTime()
                    ->sortable()
                    ->label(__('sales::filament/clusters/configurations/resources/team.table.columns.id'))
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('company.name')
                    ->label(__('sales::filament/clusters/configurations/resources/team.table.columns.company'))
                    ->sortable(),
                TextColumn::make('user.name')
                    ->label(__('sales::filament/clusters/configurations/resources/team.table.columns.team-leader'))
                    ->sortable(),
                ColorColumn::make('color')
                    ->label(__('sales::filament/clusters/configurations/resources/team.table.columns.color'))
                    ->searchable(),
                TextColumn::make('createdBy.name')
                    ->label(__('sales::filament/clusters/configurations/resources/team.table.columns.created-by'))
                    ->sortable(),
                TextColumn::make('name')
                    ->label(__('sales::filament/clusters/configurations/resources/team.table.columns.name'))
                    ->searchable(),
                IconColumn::make('is_active')
                    ->label(__('sales::filament/clusters/configurations/resources/team.table.columns.status'))
                    ->boolean(),
                TextColumn::make('invoiced_target')
                    ->numeric()
                    ->label(__('sales::filament/clusters/configurations/resources/team.table.columns.invoiced-target'))
                    ->sortable(),
                TextColumn::make('created_at')
                    ->dateTime()
                    ->label(__('sales::filament/clusters/configurations/resources/team.table.columns.created-at'))
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->label(__('sales::filament/clusters/configurations/resources/team.table.columns.updated-at'))
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                QueryBuilder::make()
                    ->constraintPickerColumns(2)
                    ->constraints([
                        TextConstraint::make('name')
                            ->label(__('sales::filament/clusters/configurations/resources/team.table.filters.name'))
                            ->icon('heroicon-o-user'),
                        RelationshipConstraint::make('user')
                            ->label(__('sales::filament/clusters/configurations/resources/team.table.filters.team-leader'))
                            ->icon('heroicon-o-user')
                            ->multiple()
                            ->selectable(
                                IsRelatedToOperator::make()
                                    ->titleAttribute('name')
                                    ->label(__('sales::filament/clusters/configurations/resources/team.table.filters.team-leader'))
                                    ->searchable()
                                    ->multiple()
                                    ->preload(),
                            ),
                        RelationshipConstraint::make('company')
                            ->label(__('sales::filament/clusters/configurations/resources/team.table.filters.company'))
                            ->icon('heroicon-o-building-office-2')
                            ->multiple()
                            ->selectable(
                                IsRelatedToOperator::make()
                                    ->titleAttribute('name')
                                    ->label(__('sales::filament/clusters/configurations/resources/team.table.filters.company'))
                                    ->searchable()
                                    ->multiple()
                                    ->preload(),
                            ),
                        DateConstraint::make('creator_id')
                            ->label(__('sales::filament/clusters/configurations/resources/team.table.filters.created-by')),
                        DateConstraint::make('created_at')
                            ->label(__('sales::filament/clusters/configurations/resources/team.table.filters.created-at')),
                        DateConstraint::make('updated_at')
                            ->label(__('sales::filament/clusters/configurations/resources/team.table.filters.updated-at')),
                    ]),
            ])
            ->groups([
                Group::make('name')
                    ->label(__('sales::filament/clusters/configurations/resources/team.table.groups.name'))
                    ->collapsible(),
                Group::make('company.name')
                    ->label(__('sales::filament/clusters/configurations/resources/team.table.groups.company'))
                    ->collapsible(),
                Group::make('user.name')
                    ->label(__('sales::filament/clusters/configurations/resources/team.table.groups.team-leader'))
                    ->collapsible(),
                Group::make('created_at')
                    ->label(__('sales::filament/clusters/configurations/resources/team.table.groups.created-at'))
                    ->collapsible(),
                Group::make('updated_at')
                    ->label(__('sales::filament/clusters/configurations/resources/team.table.groups.updated-at'))
                    ->date()
                    ->collapsible(),
            ])
            ->recordActions([
                ViewAction::make(),
                EditAction::make(),
                DeleteAction::make()
                    ->successNotification(
                        Notification::make()
                            ->success()
                            ->title(__('sales::filament/clusters/configurations/resources/team.table.actions.delete.notification.title'))
                            ->body(__('sales::filament/clusters/configurations/resources/team.table.actions.delete.notification.title')),
                    ),
                RestoreAction::make()
                    ->successNotification(
                        Notification::make()
                            ->success()
                            ->title(__('sales::filament/clusters/configurations/resources/team.table.actions.restore.notification.title'))
                            ->body(__('sales::filament/clusters/configurations/resources/team.table.actions.restore.notification.title')),
                    ),
                ForceDeleteAction::make()
                    ->successNotification(
                        Notification::make()
                            ->success()
                            ->title(__('sales::filament/clusters/configurations/resources/team.table.actions.force-delete.notification.title'))
                            ->body(__('sales::filament/clusters/configurations/resources/team.table.actions.force-delete.notification.title')),
                    ),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    RestoreBulkAction::make()
                        ->successNotification(
                            Notification::make()
                                ->success()
                                ->title(__('sales::filament/clusters/configurations/resources/team.table.bulk-actions.restore.notification.title'))
                                ->body(__('sales::filament/clusters/configurations/resources/team.table.bulk-actions.restore.notification.title')),
                        ),
                    DeleteBulkAction::make()
                        ->successNotification(
                            Notification::make()
                                ->success()
                                ->title(__('sales::filament/clusters/configurations/resources/team.table.bulk-actions.delete.notification.title'))
                                ->body(__('sales::filament/clusters/configurations/resources/team.table.bulk-actions.delete.notification.title')),
                        ),
                    ForceDeleteBulkAction::make()
                        ->successNotification(
                            Notification::make()
                                ->success()
                                ->title(__('sales::filament/clusters/configurations/resources/team.table.bulk-actions.force-delete.notification.title'))
                                ->body(__('sales::filament/clusters/configurations/resources/team.table.bulk-actions.force-delete.notification.title')),
                        ),
                ]),
            ])
            ->reorderable('sort', 'desc');
    }

    public static function infolist(Schema $schema): Schema
    {
        return $schema
            ->components([
                \Filament\Schemas\Components\Group::make()
                    ->schema([
                        Section::make()
                            ->schema([
                                TextEntry::make('name')
                                    ->label(__('sales::filament/clusters/configurations/resources/team.infolist.sections.entries.name'))
                                    ->columnSpan(1),
                                Fieldset::make(__('sales::filament/clusters/configurations/resources/team.infolist.sections.entries.fieldset.team-details.title'))
                                    ->schema([
                                        TextEntry::make('user.name')
                                            ->label(__('sales::filament/clusters/configurations/resources/team.infolist.sections.entries.fieldset.team-details.entries.team-leader'))
                                            ->icon('heroicon-o-user'),
                                        TextEntry::make('company.name')
                                            ->label(__('sales::filament/clusters/configurations/resources/team.infolist.sections.entries.fieldset.team-details.entries.company'))
                                            ->icon('heroicon-o-building-office'),
                                        TextEntry::make('invoiced_target')
                                            ->label(__('sales::filament/clusters/configurations/resources/team.infolist.sections.entries.fieldset.team-details.entries.invoiced-target'))
                                            ->suffix(__('sales::filament/clusters/configurations/resources/team.infolist.sections.entries.fieldset.team-details.entries.invoiced-target-suffix'))
                                            ->numeric(),
                                        ColorEntry::make('color')
                                            ->label(__('sales::filament/clusters/configurations/resources/team.infolist.sections.entries.fieldset.team-details.entries.color')),
                                        TextEntry::make('members.name')
                                            ->label(__('sales::filament/clusters/configurations/resources/team.infolist.sections.entries.fieldset.team-details.entries.members'))
                                            ->listWithLineBreaks()
                                            ->bulleted(),
                                    ])
                                    ->columns(2),
                                IconEntry::make('is_active')
                                    ->label(__('sales::filament/clusters/configurations/resources/team.infolist.sections.entries.status'))
                                    ->boolean(),
                            ]),
                    ])
                    ->columnSpan('full'),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index'  => ListTeams::route('/'),
            'create' => CreateTeam::route('/create'),
            'view'   => ViewTeam::route('/{record}'),
            'edit'   => EditTeam::route('/{record}/edit'),
        ];
    }
}
