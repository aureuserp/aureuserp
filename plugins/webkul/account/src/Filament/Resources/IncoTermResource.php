<?php

namespace Webkul\Account\Filament\Resources;

use Filament\Forms;
use Filament\Forms\Form;
use Filament\Infolists;
use Filament\Infolists\Infolist;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Auth;
use Webkul\Account\Filament\Resources\IncoTermResource\Pages;
use Webkul\Account\Models\Incoterm;

class IncoTermResource extends Resource
{
    protected static ?string $model = Incoterm::class;

    protected static ?string $navigationIcon = 'heroicon-o-globe-alt';

    protected static bool $shouldRegisterNavigation = false;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Hidden::make('creator_id')
                    ->default(Auth::id())
                    ->required(),
                Forms\Components\TextInput::make('code')
                    ->label(__('accounts::filament/resources/incoterm.form.fields.code'))
                    ->maxLength(3)
                    ->required(),
                Forms\Components\TextInput::make('name')
                    ->label(__('accounts::filament/resources/incoterm.form.fields.name'))
                    ->required(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('code')
                    ->label(__('accounts::filament/resources/incoterm.table.columns.code'))
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('name')
                    ->label(__('accounts::filament/resources/incoterm.table.columns.name'))
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('createdBy.name')
                    ->label(__('accounts::filament/resources/incoterm.table.columns.created-by'))
                    ->searchable()
                    ->sortable(),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make()
                    ->successNotification(
                        Notification::make()
                            ->success()
                            ->title(__('accounts::filament/resources/incoterm.table.actions.edit.notification.title'))
                            ->body(__('accounts::filament/resources/incoterm.table.actions.edit.notification.body'))
                    ),
                Tables\Actions\DeleteAction::make()
                    ->successNotification(
                        Notification::make()
                            ->title(__('accounts::filament/resources/incoterm.table.actions.delete.notification.title'))
                            ->body(__('accounts::filament/resources/incoterm.table.actions.delete.notification.body'))
                    ),
                Tables\Actions\RestoreAction::make()
                    ->successNotification(
                        Notification::make()
                            ->title(__('accounts::filament/resources/incoterm.table.actions.restore.notification.title'))
                            ->body(__('accounts::filament/resources/incoterm.table.actions.restore.notification.body'))
                    ),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make()
                        ->successNotification(
                            Notification::make()
                                ->title(__('accounts::filament/resources/incoterm.table.bulk-actions.delete.notification.title'))
                                ->body(__('accounts::filament/resources/incoterm.table.bulk-actions.delete.notification.body'))
                        ),
                    Tables\Actions\ForceDeleteBulkAction::make()
                        ->successNotification(
                            Notification::make()
                                ->title(__('accounts::filament/resources/incoterm.table.bulk-actions.force-delete.notification.title'))
                                ->body(__('accounts::filament/resources/incoterm.table.bulk-actions.force-delete.notification.body'))
                        ),
                    Tables\Actions\RestoreBulkAction::make()
                        ->successNotification(
                            Notification::make()
                                ->title(__('accounts::filament/resources/incoterm.table.bulk-actions.restore.notification.title'))
                                ->body(__('accounts::filament/resources/incoterm.table.bulk-actions.restore.notification.body'))
                        ),
                ]),
            ]);
    }

    public static function infolist(Infolist $infolist): Infolist
    {
        return $infolist
            ->schema([
                Infolists\Components\TextEntry::make('code')
                    ->placeholder(__('accounts::filament/resources/incoterm.infolist.entries.code')),
                Infolists\Components\TextEntry::make('name')
                    ->placeholder(__('accounts::filament/resources/incoterm.infolist.entries.name')),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListIncoTerms::route('/'),
        ];
    }
}
