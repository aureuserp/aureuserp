<?php

namespace Webkul\Security\Filament\Resources;

use Filament\Forms;
use Filament\Forms\Form;
use Filament\Infolists;
use Filament\Infolists\Infolist;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Webkul\Security\Filament\Resources\TeamResource\Pages;
use Webkul\Security\Models\Team;
use Webkul\Security\Traits\HasResourcePermissionQuery;

class TeamResource extends Resource
{
    use HasResourcePermissionQuery;

    protected static ?string $model = Team::class;

    protected static ?string $navigationIcon = 'heroicon-o-users';

    protected static ?int $navigationSort = 3;

    public static function getNavigationLabel(): string
    {
        return __('security::filament/resources/team.navigation.title');
    }

    public static function getNavigationGroup(): string
    {
        return __('security::filament/resources/team.navigation.group');
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('name')
                    ->label(__('security::filament/resources/team.form.fields.name'))
                    ->required()
                    ->maxLength(255),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->label(__('security::filament/resources/team.table.columns.name'))
                    ->searchable()
                    ->limit(50)
                    ->sortable(),
                Tables\Columns\TextColumn::make('createdBy.name')
                    ->label(__('security::filament/resources/team.table.columns.created-by'))
                    ->searchable()
                    ->limit(50)
                    ->sortable(),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make()
                    ->successNotification(
                        Notification::make()
                            ->success()
                            ->title(__('security::filament/resources/team.table.actions.edit.notification.title'))
                            ->body(__('security::filament/resources/team.table.actions.edit.notification.body'))
                    ),
                Tables\Actions\DeleteAction::make()
                    ->hidden(fn (Team $record): bool => $record->users?->isNotEmpty())
                    ->successNotification(
                        Notification::make()
                            ->success()
                            ->title(__('security::filament/resources/team.table.actions.delete.notification.title'))
                            ->body(__('security::filament/resources/team.table.actions.delete.notification.body'))
                    ),
            ])
            ->emptyStateActions([
                Tables\Actions\CreateAction::make()
                    ->icon('heroicon-o-plus-circle')
                    ->successNotification(
                        Notification::make()
                            ->success()
                            ->title(__('security::filament/resources/team.table.empty-state-actions.create.notification.title'))
                            ->body(__('security::filament/resources/team.table.empty-state-actions.create.notification.body'))
                    ),
            ]);
    }

    public static function infolist(Infolist $infolist): Infolist
    {
        return $infolist
            ->schema([
                Infolists\Components\TextEntry::make('name')
                    ->icon('heroicon-o-user')
                    ->placeholder('—')
                    ->label(__('security::filament/resources/team.infolist.entries.name')),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ManageTeams::route('/'),
        ];
    }
}
