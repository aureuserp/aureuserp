<?php

namespace Webkul\Employee\Filament\Clusters\Configurations\Resources\SkillTypeResource\RelationManagers;

use Filament\Actions\ActionGroup;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\CreateAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ForceDeleteBulkAction;
use Filament\Actions\RestoreAction;
use Filament\Actions\RestoreBulkAction;
use Filament\Actions\ViewAction;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Infolists\Components\IconEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Notifications\Notification;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\TrashedFilter;
use Filament\Tables\Grouping\Group;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Auth;
use Webkul\Support\Filament\Tables as CustomTables;
use Webkul\Support\Filament\Tables\Infolists\ProgressBarEntry;

class SkillLevelRelationManager extends RelationManager
{
    protected static string $relationship = 'skillLevels';

    protected static ?string $recordTitleAttribute = 'name';

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('name')
                    ->label(__('employees::filament/clusters/configurations/resources/skill-type/relation-managers/levels.form.name'))
                    ->required(),
                Hidden::make('creator_id')
                    ->default(Auth::user()->id),
                TextInput::make('level')
                    ->label(__('employees::filament/clusters/configurations/resources/skill-type/relation-managers/levels.form.level'))
                    ->numeric()
                    ->minValue(0)
                    ->maxValue(100),
                Toggle::make('default_level')
                    ->label(__('employees::filament/clusters/configurations/resources/skill-type/relation-managers/levels.form.default-level')),
            ])->columns(2);
    }

    public function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')
                    ->label(__('employees::filament/clusters/configurations/resources/skill-type/relation-managers/levels.table.columns.name'))
                    ->searchable()
                    ->sortable(),
                CustomTables\Columns\ProgressBarEntry::make('level')
                    ->label(__('employees::filament/clusters/configurations/resources/skill-type/relation-managers/levels.table.columns.level'))
                    ->getStateUsing(fn ($record) => $record->level)
                    ->color(fn ($record): string => match (true) {
                        $record->level === 100                      => 'success',
                        $record->level >= 50 && $record->level < 80 => 'warning',
                        $record->level < 20                         => 'danger',
                        default                                     => 'info',
                    }),
                IconColumn::make('default_level')
                    ->sortable()
                    ->label(__('employees::filament/clusters/configurations/resources/skill-type/relation-managers/levels.table.columns.default-level'))
                    ->boolean(),
                TextColumn::make('created_at')
                    ->label(__('employees::filament/clusters/configurations/resources/skill-type/relation-managers/levels.table.columns.created-at'))
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('updated_at')
                    ->label(__('employees::filament/clusters/configurations/resources/skill-type/relation-managers/levels.table.columns.updated-at'))
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->groups([
                Group::make('created_at')
                    ->label(__('employees::filament/clusters/configurations/resources/skill-type/relation-managers/levels.table.groups.created-at'))
                    ->date()
                    ->collapsible(),
            ])
            ->filters([
                TrashedFilter::make()
                    ->label(__('employees::filament/clusters/configurations/resources/skill-type/relation-managers/levels.table.filters.deleted-records')),
            ])
            ->headerActions([
                CreateAction::make()
                    ->icon('heroicon-o-plus-circle')
                    ->modal('form')
                    ->mutateDataUsing(function ($data) {
                        if ($data['default_level'] ?? false) {
                            $this->getRelationship()->update(['default_level' => false]);
                        }

                        return $data;
                    })
                    ->successNotification(
                        Notification::make()
                            ->success()
                            ->title(__('employees::filament/clusters/configurations/resources/skill-type/relation-managers/levels.table.actions.create.notification.title'))
                            ->body(__('employees::filament/clusters/configurations/resources/skill-type/relation-managers/levels.table.actions.create.notification.body')),
                    ),
            ])
            ->recordActions([
                ActionGroup::make([
                    ViewAction::make(),
                    EditAction::make()
                        ->mutateDataUsing(function ($data, $record) {
                            if ($data['default_level'] ?? false) {
                                $this->getRelationship()->where('id', '!=', $record->id)->update(['default_level' => false]);
                            }

                            return $data;
                        })
                        ->successNotification(
                            Notification::make()
                                ->success()
                                ->title(__('employees::filament/clusters/configurations/resources/skill-type/relation-managers/levels.table.actions.edit.notification.title'))
                                ->body(__('employees::filament/clusters/configurations/resources/skill-type/relation-managers/levels.table.actions.edit.notification.body')),
                        ),
                    DeleteAction::make()
                        ->successNotification(
                            Notification::make()
                                ->success()
                                ->title(__('employees::filament/clusters/configurations/resources/skill-type/relation-managers/levels.table.actions.delete.notification.title'))
                                ->body(__('employees::filament/clusters/configurations/resources/skill-type/relation-managers/levels.table.actions.delete.notification.body')),
                        ),
                    RestoreAction::make()
                        ->successNotification(
                            Notification::make()
                                ->success()
                                ->title(__('employees::filament/clusters/configurations/resources/skill-type/relation-managers/levels.table.actions.restore.notification.title'))
                                ->body(__('employees::filament/clusters/configurations/resources/skill-type/relation-managers/levels.table.actions.restore.notification.body')),
                        ),
                ]),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make()
                        ->successNotification(
                            Notification::make()
                                ->success()
                                ->title(__('employees::filament/clusters/configurations/resources/skill-type/relation-managers/levels.table.bulk-actions.delete.notification.title'))
                                ->body(__('employees::filament/clusters/configurations/resources/skill-type/relation-managers/levels.table.bulk-actions.delete.notification.body')),
                        ),
                    ForceDeleteBulkAction::make()
                        ->successNotification(
                            Notification::make()
                                ->success()
                                ->title(__('employees::filament/clusters/configurations/resources/skill-type/relation-managers/levels.table.bulk-actions.force-delete.notification.title'))
                                ->body(__('employees::filament/clusters/configurations/resources/skill-type/relation-managers/levels.table.bulk-actions.force-delete.notification.body')),
                        ),
                    RestoreBulkAction::make()
                        ->successNotification(
                            Notification::make()
                                ->success()
                                ->title(__('employees::filament/clusters/configurations/resources/skill-type/relation-managers/levels.table.bulk-actions.restore.notification.title'))
                                ->body(__('employees::filament/clusters/configurations/resources/skill-type/relation-managers/levels.table.bulk-actions.restore.notification.body')),
                        ),
                ]),
            ]);
    }

    public function infolist(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextEntry::make('name')
                    ->placeholder('—')
                    ->label(__('employees::filament/clusters/configurations/resources/skill-type/relation-managers/levels.infolist.entries.name')),
                ProgressBarEntry::make('level')
                    ->label(__('employees::filament/clusters/configurations/resources/skill-type/relation-managers/levels.infolist.entries.level'))
                    ->getStateUsing(fn ($record) => $record->level)
                    ->color(fn ($record): string => match (true) {
                        $record->level === 100                      => 'success',
                        $record->level >= 50 && $record->level < 80 => 'warning',
                        $record->level < 20                         => 'danger',
                        default                                     => 'info',
                    }),
                IconEntry::make('default_level')
                    ->boolean()
                    ->label(__('employees::filament/clusters/configurations/resources/skill-type/relation-managers/levels.infolist.entries.default-level')),
            ]);
    }
}
