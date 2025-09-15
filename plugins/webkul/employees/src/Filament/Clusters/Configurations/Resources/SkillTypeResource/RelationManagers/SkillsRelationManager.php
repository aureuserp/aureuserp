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
use Filament\Infolists\Components\TextEntry;
use Filament\Notifications\Notification;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\TrashedFilter;
use Filament\Tables\Grouping\Group;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Auth;

class SkillsRelationManager extends RelationManager
{
    protected static string $relationship = 'skills';

    protected static ?string $recordTitleAttribute = 'name';

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('name')
                    ->label(__('employees::filament/clusters/configurations/resources/skill-type/relation-managers/skills.form.name'))
                    ->required(),
                Hidden::make('creator_id')
                    ->default(Auth::user()->id),
            ])->columns(1);
    }

    public function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')
                    ->label(__('employees::filament/clusters/configurations/resources/skill-type/relation-managers/skills.table.columns.name'))
                    ->searchable()
                    ->sortable(),
                TextColumn::make('created_at')
                    ->label(__('employees::filament/clusters/configurations/resources/skill-type/relation-managers/skills.table.columns.created-at'))
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('updated_at')
                    ->label(__('employees::filament/clusters/configurations/resources/skill-type/relation-managers/skills.table.columns.updated-at'))
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->groups([
                Group::make('created_at')
                    ->label(__('employees::filament/clusters/configurations/resources/skill-type/relation-managers/skills.table.groups.created-at'))
                    ->date()
                    ->collapsible(),
            ])
            ->headerActions([
                CreateAction::make()
                    ->icon('heroicon-o-plus-circle')
                    ->modal('form'),
            ])
            ->filters([
                TrashedFilter::make()
                    ->label(__('employees::filament/clusters/configurations/resources/skill-type/relation-managers/skills.table.filters.deleted-records')),
            ])
            ->recordActions([
                ActionGroup::make([
                    ViewAction::make(),
                    EditAction::make()
                        ->successNotification(
                            Notification::make()
                                ->success()
                                ->title(__('employees::filament/clusters/configurations/resources/skill-type/relation-managers/skills.table.actions.edit.notification.title'))
                                ->body(__('employees::filament/clusters/configurations/resources/skill-type/relation-managers/skills.table.actions.edit.notification.body')),
                        ),
                    DeleteAction::make()
                        ->successNotification(
                            Notification::make()
                                ->success()
                                ->title(__('employees::filament/clusters/configurations/resources/skill-type/relation-managers/skills.table.actions.delete.notification.title'))
                                ->body(__('employees::filament/clusters/configurations/resources/skill-type/relation-managers/skills.table.actions.delete.notification.body')),
                        ),
                    RestoreAction::make()
                        ->successNotification(
                            Notification::make()
                                ->success()
                                ->title(__('employees::filament/clusters/configurations/resources/skill-type/relation-managers/skills.table.actions.restore.notification.title'))
                                ->body(__('employees::filament/clusters/configurations/resources/skill-type/relation-managers/skills.table.actions.restore.notification.body')),
                        ),
                ]),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make()
                        ->successNotification(
                            Notification::make()
                                ->success()
                                ->title(__('employees::filament/clusters/configurations/resources/skill-type/relation-managers/skills.table.bulk-actions.delete.notification.title'))
                                ->body(__('employees::filament/clusters/configurations/resources/skill-type/relation-managers/skills.table.bulk-actions.delete.notification.body')),
                        ),
                    ForceDeleteBulkAction::make()
                        ->successNotification(
                            Notification::make()
                                ->success()
                                ->title(__('employees::filament/clusters/configurations/resources/skill-type/relation-managers/skills.table.bulk-actions.force-delete.notification.title'))
                                ->body(__('employees::filament/clusters/configurations/resources/skill-type/relation-managers/skills.table.bulk-actions.force-delete.notification.body')),
                        ),
                    RestoreBulkAction::make()
                        ->successNotification(
                            Notification::make()
                                ->success()
                                ->title(__('employees::filament/clusters/configurations/resources/skill-type/relation-managers/skills.table.bulk-actions.restore.notification.title'))
                                ->body(__('employees::filament/clusters/configurations/resources/skill-type/relation-managers/skills.table.bulk-actions.restore.notification.body')),
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
                    ->label(__('employees::filament/clusters/configurations/resources/skill-type/relation-managers/skills.infolist.entries.name')),
            ]);
    }
}
