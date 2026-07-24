<?php

namespace Webkul\Maintenance\Filament\Clusters\Configurations\Resources;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ForceDeleteAction;
use Filament\Actions\ForceDeleteBulkAction;
use Filament\Actions\RestoreAction;
use Filament\Actions\RestoreBulkAction;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\QueryException;
use Webkul\Maintenance\Filament\Clusters\Configurations;
use Webkul\Maintenance\Filament\Clusters\Configurations\Resources\TeamResource\Pages\ManageTeams;
use Webkul\Maintenance\Models\Team;

class TeamResource extends Resource
{
    protected static ?string $model = Team::class;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-user-group';

    protected static ?int $navigationSort = 2;

    protected static ?string $cluster = Configurations::class;

    protected static ?string $recordTitleAttribute = 'name';

    public static function getModelLabel(): string
    {
        return __('maintenance::models/team.title');
    }

    public static function getNavigationLabel(): string
    {
        return __('maintenance::filament/clusters/configurations/resources/team.navigation.title');
    }

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('name')
                    ->label(__('maintenance::filament/clusters/configurations/resources/team.form.name'))
                    ->required()
                    ->maxLength(255)
                    ->autofocus()
                    ->unique(ignoreRecord: true),

                Select::make('users')
                    ->label(__('maintenance::filament/clusters/configurations/resources/team.form.users'))
                    ->relationship('users', 'name')
                    ->multiple()
                    ->searchable()
                    ->preload(),

                Select::make('company_id')
                    ->label(__('maintenance::filament/clusters/configurations/resources/team.form.company'))
                    ->relationship('company', 'name')
                    ->searchable()
                    ->preload()
                    ->default(auth()->user()?->default_company_id),
            ])
            ->columns(1);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')
                    ->label(__('maintenance::filament/clusters/configurations/resources/team.table.columns.name'))
                    ->searchable()
                    ->sortable(),

                TextColumn::make('company.name')
                    ->label(__('maintenance::filament/clusters/configurations/resources/team.table.columns.company'))
                    ->sortable(),

                TextColumn::make('users.name')
                    ->label(__('maintenance::filament/clusters/configurations/resources/team.table.columns.users'))
                    ->badge(),

                TextColumn::make('created_at')
                    ->label(__('maintenance::filament/clusters/configurations/resources/team.table.columns.created-at'))
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->recordActions([
                EditAction::make()
                    ->hidden(fn ($record) => $record->trashed())
                    ->successNotification(
                        Notification::make()
                            ->success()
                            ->title(__('maintenance::filament/clusters/configurations/resources/team.table.actions.edit.notification.title'))
                            ->body(__('maintenance::filament/clusters/configurations/resources/team.table.actions.edit.notification.body')),
                    ),
                RestoreAction::make()
                    ->successNotification(
                        Notification::make()
                            ->success()
                            ->title(__('maintenance::filament/clusters/configurations/resources/team.table.actions.restore.notification.title'))
                            ->body(__('maintenance::filament/clusters/configurations/resources/team.table.actions.restore.notification.body')),
                    ),
                DeleteAction::make()
                    ->successNotification(
                        Notification::make()
                            ->success()
                            ->title(__('maintenance::filament/clusters/configurations/resources/team.table.actions.delete.notification.title'))
                            ->body(__('maintenance::filament/clusters/configurations/resources/team.table.actions.delete.notification.body')),
                    ),
                ForceDeleteAction::make()
                    ->action(function (Team $record): void {
                        try {
                            $record->forceDelete();

                            Notification::make()
                                ->success()
                                ->title(__('maintenance::filament/clusters/configurations/resources/team.table.actions.force-delete.notification.success.title'))
                                ->body(__('maintenance::filament/clusters/configurations/resources/team.table.actions.force-delete.notification.success.body'))
                                ->send();
                        } catch (QueryException) {
                            Notification::make()
                                ->danger()
                                ->title(__('maintenance::filament/clusters/configurations/resources/team.table.actions.force-delete.notification.error.title'))
                                ->body(__('maintenance::filament/clusters/configurations/resources/team.table.actions.force-delete.notification.error.body'))
                                ->send();
                        }
                    }),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    RestoreBulkAction::make()
                        ->successNotification(
                            Notification::make()
                                ->success()
                                ->title(__('maintenance::filament/clusters/configurations/resources/team.table.bulk-actions.restore.notification.title'))
                                ->body(__('maintenance::filament/clusters/configurations/resources/team.table.bulk-actions.restore.notification.body')),
                        ),
                    DeleteBulkAction::make()
                        ->successNotification(
                            Notification::make()
                                ->success()
                                ->title(__('maintenance::filament/clusters/configurations/resources/team.table.bulk-actions.delete.notification.title'))
                                ->body(__('maintenance::filament/clusters/configurations/resources/team.table.bulk-actions.delete.notification.body')),
                        ),
                    ForceDeleteBulkAction::make()
                        ->action(function (Collection $records): void {
                            try {
                                $records->each(fn (Model $record) => $record->forceDelete());

                                Notification::make()
                                    ->success()
                                    ->title(__('maintenance::filament/clusters/configurations/resources/team.table.actions.force-delete.notification.success.title'))
                                    ->body(__('maintenance::filament/clusters/configurations/resources/team.table.actions.force-delete.notification.success.body'))
                                    ->send();
                            } catch (QueryException) {
                                Notification::make()
                                    ->danger()
                                    ->title(__('maintenance::filament/clusters/configurations/resources/team.table.actions.force-delete.notification.error.title'))
                                    ->body(__('maintenance::filament/clusters/configurations/resources/team.table.actions.force-delete.notification.error.body'))
                                    ->send();
                            }
                        }),
                ]),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => ManageTeams::route('/'),
        ];
    }
}
