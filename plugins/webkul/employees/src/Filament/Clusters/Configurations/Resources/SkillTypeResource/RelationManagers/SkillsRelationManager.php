<?php

namespace Webkul\Employee\Filament\Clusters\Configurations\Resources\SkillTypeResource\RelationManagers;

use Filament\Forms;
use Filament\Forms\Form;
use Filament\Infolists;
use Filament\Infolists\Infolist;
use Filament\Notifications\Notification;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Auth;

class SkillsRelationManager extends RelationManager
{
    protected static string $relationship = 'skills';

    protected static ?string $recordTitleAttribute = 'name';

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('name')
                    ->label(__('employees::filament/clusters/configurations/resources/skill-type/relation-managers/skills.form.name'))
                    ->required(),
                Forms\Components\Hidden::make('creator_id')
                    ->default(Auth::user()->id),
            ])->columns('full');
    }

    public function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->label(__('employees::filament/clusters/configurations/resources/skill-type/relation-managers/skills.table.columns.name'))
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('created_at')
                    ->label(__('employees::filament/clusters/configurations/resources/skill-type/relation-managers/skills.table.columns.created-at'))
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('updated_at')
                    ->label(__('employees::filament/clusters/configurations/resources/skill-type/relation-managers/skills.table.columns.updated-at'))
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->groups([
                Tables\Grouping\Group::make('created_at')
                    ->label(__('employees::filament/clusters/configurations/resources/skill-type/relation-managers/skills.table.groups.created-at'))
                    ->date()
                    ->collapsible(),
            ])
            ->headerActions([
                Tables\Actions\CreateAction::make()
                    ->icon('heroicon-o-plus-circle')
                    ->modal('form'),
            ])
            ->filters([
                Tables\Filters\TrashedFilter::make()
                    ->label(__('employees::filament/clusters/configurations/resources/skill-type/relation-managers/skills.table.filters.deleted-records')),
            ])
            ->actions([
                Tables\Actions\ActionGroup::make([
                    Tables\Actions\ViewAction::make(),
                    Tables\Actions\EditAction::make()
                        ->successNotification(
                            Notification::make()
                                ->success()
                                ->title(__('employees::filament/clusters/configurations/resources/skill-type/relation-managers/skills.table.actions.edit.notification.title'))
                                ->body(__('employees::filament/clusters/configurations/resources/skill-type/relation-managers/skills.table.actions.edit.notification.body')),
                        ),
                    Tables\Actions\DeleteAction::make()
                        ->successNotification(
                            Notification::make()
                                ->success()
                                ->title(__('employees::filament/clusters/configurations/resources/skill-type/relation-managers/skills.table.actions.delete.notification.title'))
                                ->body(__('employees::filament/clusters/configurations/resources/skill-type/relation-managers/skills.table.actions.delete.notification.body')),
                        ),
                    Tables\Actions\RestoreAction::make()
                        ->successNotification(
                            Notification::make()
                                ->success()
                                ->title(__('employees::filament/clusters/configurations/resources/skill-type/relation-managers/skills.table.actions.restore.notification.title'))
                                ->body(__('employees::filament/clusters/configurations/resources/skill-type/relation-managers/skills.table.actions.restore.notification.body')),
                        ),
                ]),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make()
                        ->successNotification(
                            Notification::make()
                                ->success()
                                ->title(__('employees::filament/clusters/configurations/resources/skill-type/relation-managers/skills.table.bulk-actions.delete.notification.title'))
                                ->body(__('employees::filament/clusters/configurations/resources/skill-type/relation-managers/skills.table.bulk-actions.delete.notification.body')),
                        ),
                    Tables\Actions\ForceDeleteBulkAction::make()
                        ->successNotification(
                            Notification::make()
                                ->success()
                                ->title(__('employees::filament/clusters/configurations/resources/skill-type/relation-managers/skills.table.bulk-actions.force-delete.notification.title'))
                                ->body(__('employees::filament/clusters/configurations/resources/skill-type/relation-managers/skills.table.bulk-actions.force-delete.notification.body')),
                        ),
                    Tables\Actions\RestoreBulkAction::make()
                        ->successNotification(
                            Notification::make()
                                ->success()
                                ->title(__('employees::filament/clusters/configurations/resources/skill-type/relation-managers/skills.table.bulk-actions.restore.notification.title'))
                                ->body(__('employees::filament/clusters/configurations/resources/skill-type/relation-managers/skills.table.bulk-actions.restore.notification.body')),
                        ),
                ]),
            ]);
    }

    public function infolist(Infolist $infolist): Infolist
    {
        return $infolist
            ->schema([
                Infolists\Components\TextEntry::make('name')
                    ->placeholder('—')
                    ->label(__('employees::filament/clusters/configurations/resources/skill-type/relation-managers/skills.infolist.entries.name')),
            ]);
    }
}
