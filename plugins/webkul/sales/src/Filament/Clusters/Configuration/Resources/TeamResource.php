<?php

namespace Webkul\Sale\Filament\Clusters\Configuration\Resources;

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
use Webkul\Sale\Filament\Clusters\Configuration;
use Webkul\Sale\Filament\Clusters\Configuration\Resources\TeamResource\Pages;
use Webkul\Sale\Models\Team;

class TeamResource extends Resource
{
    protected static ?string $model = Team::class;

    protected static ?string $navigationIcon = 'heroicon-o-users';

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

    public static function getGloballySearchableAttributes(): array
    {
        return [
            'company.name',
            'user.name',
            'name',
            'invoiced_target',
        ];
    }

    public static function getGlobalSearchResultDetails(Model $record): array
    {
        return [
            __('sales::filament/clusters/configurations/resources/team.global-search.company-name')    => $record->company?->name ?? '—',
            __('sales::filament/clusters/configurations/resources/team.global-search.user-name')       => $record->user?->name ?? '—',
            __('sales::filament/clusters/configurations/resources/team.global-search.name')            => $record->name ?? '—',
            __('sales::filament/clusters/configurations/resources/team.global-search.invoiced-target') => $record->invoiced_target ?? '—',
        ];
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make()
                    ->schema([
                        Forms\Components\Grid::make()
                            ->schema([
                                Forms\Components\TextInput::make('name')
                                    ->required()
                                    ->label(__('sales::filament/clusters/configurations/resources/team.form.sections.fields.name'))
                                    ->maxLength(255)
                                    ->extraInputAttributes(['style' => 'font-size: 1.5rem;height: 3rem;'])
                                    ->columnSpan(1),
                            ])->columns(2),
                        Forms\Components\Fieldset::make(__('sales::filament/clusters/configurations/resources/team.form.sections.fields.fieldset.team-details.title'))
                            ->schema([
                                Forms\Components\Select::make('user_id')
                                    ->relationship('user', 'name')
                                    ->preload()
                                    ->label(__('sales::filament/clusters/configurations/resources/team.form.sections.fields.fieldset.team-details.fields.team-leader'))
                                    ->searchable(),
                                Forms\Components\Select::make('company_id')
                                    ->relationship('company', 'name')
                                    ->preload()
                                    ->label(__('sales::filament/clusters/configurations/resources/team.form.sections.fields.fieldset.team-details.fields.company'))
                                    ->searchable(),
                                Forms\Components\TextInput::make('invoiced_target')
                                    ->numeric()
                                    ->default(0)
                                    ->label(__('sales::filament/clusters/configurations/resources/team.form.sections.fields.fieldset.team-details.fields.invoiced-target'))
                                    ->autocomplete(false)
                                    ->suffix(__('sales::filament/clusters/configurations/resources/team.form.sections.fields.fieldset.team-details.fields.invoiced-target-suffix')),
                                Forms\Components\ColorPicker::make('color')
                                    ->label(__('sales::filament/clusters/configurations/resources/team.form.sections.fields.fieldset.team-details.fields.color'))
                                    ->hexColor(),
                                Forms\Components\Select::make('sales_team_members')
                                    ->relationship('members', 'name')
                                    ->multiple()
                                    ->searchable()
                                    ->preload()
                                    ->label(__('sales::filament/clusters/configurations/resources/team.form.sections.fields.fieldset.team-details.fields.members')),
                            ])->columns(2),
                        Forms\Components\Toggle::make('is_active')
                            ->inline(false)
                            ->label(__('sales::filament/clusters/configurations/resources/team.form.sections.fields.status')),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('id')
                    ->dateTime()
                    ->sortable()
                    ->label(__('sales::filament/clusters/configurations/resources/team.table.columns.id'))
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('company.name')
                    ->label(__('sales::filament/clusters/configurations/resources/team.table.columns.company'))
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('user.name')
                    ->label(__('sales::filament/clusters/configurations/resources/team.table.columns.team-leader'))
                    ->numeric()
                    ->sortable(),
                Tables\Columns\ColorColumn::make('color')
                    ->label(__('sales::filament/clusters/configurations/resources/team.table.columns.color'))
                    ->searchable(),
                Tables\Columns\TextColumn::make('createdBy.name')
                    ->label(__('Created By'))
                    ->label(__('sales::filament/clusters/configurations/resources/team.table.columns.created-by'))
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('name')
                    ->label(__('sales::filament/clusters/configurations/resources/team.table.columns.name'))
                    ->searchable(),
                Tables\Columns\IconColumn::make('is_active')
                    ->label(__('sales::filament/clusters/configurations/resources/team.table.columns.status'))
                    ->boolean(),
                Tables\Columns\TextColumn::make('invoiced_target')
                    ->numeric()
                    ->label(__('sales::filament/clusters/configurations/resources/team.table.columns.invoiced-target'))
                    ->sortable(),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->label(__('sales::filament/clusters/configurations/resources/team.table.columns.created-at'))
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->label(__('sales::filament/clusters/configurations/resources/team.table.columns.updated-at'))
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\QueryBuilder::make()
                    ->constraintPickerColumns(2)
                    ->constraints([
                        Tables\Filters\QueryBuilder\Constraints\TextConstraint::make('name')
                            ->label(__('sales::filament/clusters/configurations/resources/team.table.filters.name'))
                            ->icon('heroicon-o-user'),
                        Tables\Filters\QueryBuilder\Constraints\RelationshipConstraint::make('user')
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
                        Tables\Filters\QueryBuilder\Constraints\RelationshipConstraint::make('company')
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
                        Tables\Filters\QueryBuilder\Constraints\DateConstraint::make('creator_id')
                            ->label(__('sales::filament/clusters/configurations/resources/team.table.filters.created-by')),
                        Tables\Filters\QueryBuilder\Constraints\DateConstraint::make('created_at')
                            ->label(__('sales::filament/clusters/configurations/resources/team.table.filters.created-at')),
                        Tables\Filters\QueryBuilder\Constraints\DateConstraint::make('updated_at')
                            ->label(__('sales::filament/clusters/configurations/resources/team.table.filters.updated-at')),
                    ]),
            ])
            ->groups([
                Tables\Grouping\Group::make('name')
                    ->label(__('sales::filament/clusters/configurations/resources/team.table.groups.name'))
                    ->collapsible(),
                Tables\Grouping\Group::make('company.name')
                    ->label(__('sales::filament/clusters/configurations/resources/team.table.groups.company'))
                    ->collapsible(),
                Tables\Grouping\Group::make('user.name')
                    ->label(__('sales::filament/clusters/configurations/resources/team.table.groups.team-leader'))
                    ->collapsible(),
                Tables\Grouping\Group::make('created_at')
                    ->label(__('sales::filament/clusters/configurations/resources/team.table.groups.created-at'))
                    ->collapsible(),
                Tables\Grouping\Group::make('updated_at')
                    ->label(__('sales::filament/clusters/configurations/resources/team.table.groups.updated-at'))
                    ->date()
                    ->collapsible(),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make()
                    ->successNotification(
                        Notification::make()
                            ->success()
                            ->title(__('sales::filament/clusters/configurations/resources/team.table.actions.delete.notification.title'))
                            ->body(__('sales::filament/clusters/configurations/resources/team.table.actions.delete.notification.title')),
                    ),
                Tables\Actions\RestoreAction::make()
                    ->successNotification(
                        Notification::make()
                            ->success()
                            ->title(__('sales::filament/clusters/configurations/resources/team.table.actions.restore.notification.title'))
                            ->body(__('sales::filament/clusters/configurations/resources/team.table.actions.restore.notification.title')),
                    ),
                Tables\Actions\ForceDeleteAction::make()
                    ->successNotification(
                        Notification::make()
                            ->success()
                            ->title(__('sales::filament/clusters/configurations/resources/team.table.actions.force-delete.notification.title'))
                            ->body(__('sales::filament/clusters/configurations/resources/team.table.actions.force-delete.notification.title')),
                    ),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\RestoreBulkAction::make()
                        ->successNotification(
                            Notification::make()
                                ->success()
                                ->title(__('sales::filament/clusters/configurations/resources/team.table.bulk-actions.restore.notification.title'))
                                ->body(__('sales::filament/clusters/configurations/resources/team.table.bulk-actions.restore.notification.title')),
                        ),
                    Tables\Actions\DeleteBulkAction::make()
                        ->successNotification(
                            Notification::make()
                                ->success()
                                ->title(__('sales::filament/clusters/configurations/resources/team.table.bulk-actions.delete.notification.title'))
                                ->body(__('sales::filament/clusters/configurations/resources/team.table.bulk-actions.delete.notification.title')),
                        ),
                    Tables\Actions\ForceDeleteBulkAction::make()
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

    public static function infolist(Infolist $infolist): Infolist
    {
        return $infolist
            ->schema([
                Infolists\Components\Group::make()
                    ->schema([
                        Infolists\Components\Section::make()
                            ->schema([
                                Infolists\Components\TextEntry::make('name')
                                    ->label(__('sales::filament/clusters/configurations/resources/team.infolist.sections.entries.name'))
                                    ->columnSpan(1),
                                Infolists\Components\Fieldset::make(__('sales::filament/clusters/configurations/resources/team.infolist.sections.entries.fieldset.team-details.title'))
                                    ->schema([
                                        Infolists\Components\TextEntry::make('user.name')
                                            ->label(__('sales::filament/clusters/configurations/resources/team.infolist.sections.entries.fieldset.team-details.entries.team-leader'))
                                            ->icon('heroicon-o-user'),
                                        Infolists\Components\TextEntry::make('company.name')
                                            ->label(__('sales::filament/clusters/configurations/resources/team.infolist.sections.entries.fieldset.team-details.entries.company'))
                                            ->icon('heroicon-o-building-office'),
                                        Infolists\Components\TextEntry::make('invoiced_target')
                                            ->label(__('sales::filament/clusters/configurations/resources/team.infolist.sections.entries.fieldset.team-details.entries.invoiced-target'))
                                            ->suffix(__('sales::filament/clusters/configurations/resources/team.infolist.sections.entries.fieldset.team-details.entries.invoiced-target-suffix'))
                                            ->numeric(),
                                        Infolists\Components\ColorEntry::make('color')
                                            ->label(__('sales::filament/clusters/configurations/resources/team.infolist.sections.entries.fieldset.team-details.entries.color')),
                                        Infolists\Components\TextEntry::make('members.name')
                                            ->label(__('sales::filament/clusters/configurations/resources/team.infolist.sections.entries.fieldset.team-details.entries.members'))
                                            ->listWithLineBreaks()
                                            ->bulleted(),
                                    ])
                                    ->columns(2),
                                Infolists\Components\IconEntry::make('is_active')
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
            'index'  => Pages\ListTeams::route('/'),
            'create' => Pages\CreateTeam::route('/create'),
            'view'   => Pages\ViewTeam::route('/{record}'),
            'edit'   => Pages\EditTeam::route('/{record}/edit'),
        ];
    }
}
