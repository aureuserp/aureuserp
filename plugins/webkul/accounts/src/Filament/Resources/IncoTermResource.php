<?php

namespace Webkul\Account\Filament\Resources;

use Filament\Actions\BulkActionGroup;
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
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Auth;
use Webkul\Account\Filament\Resources\IncoTermResource\Pages\ListIncoTerms;
use Webkul\Account\Models\Incoterm;

class IncoTermResource extends Resource
{
    protected static ?string $model = Incoterm::class;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-globe-alt';

    protected static bool $shouldRegisterNavigation = false;

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Hidden::make('creator_id')
                    ->default(Auth::id())
                    ->required(),
                TextInput::make('code')
                    ->label(__('accounts::filament/resources/incoterm.form.fields.code'))
                    ->maxLength(3)
                    ->required(),
                TextInput::make('name')
                    ->label(__('accounts::filament/resources/incoterm.form.fields.name'))
                    ->required(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('code')
                    ->label(__('accounts::filament/resources/incoterm.table.columns.code'))
                    ->searchable()
                    ->sortable(),
                TextColumn::make('name')
                    ->label(__('accounts::filament/resources/incoterm.table.columns.name'))
                    ->searchable()
                    ->sortable(),
                TextColumn::make('createdBy.name')
                    ->label(__('accounts::filament/resources/incoterm.table.columns.created-by'))
                    ->searchable()
                    ->sortable(),
            ])
            ->recordActions([
                ViewAction::make(),
                EditAction::make()
                    ->successNotification(
                        Notification::make()
                            ->success()
                            ->title(__('accounts::filament/resources/incoterm.table.actions.edit.notification.title'))
                            ->body(__('accounts::filament/resources/incoterm.table.actions.edit.notification.body'))
                    ),
                DeleteAction::make()
                    ->successNotification(
                        Notification::make()
                            ->title(__('accounts::filament/resources/incoterm.table.actions.delete.notification.title'))
                            ->body(__('accounts::filament/resources/incoterm.table.actions.delete.notification.body'))
                    ),
                RestoreAction::make()
                    ->successNotification(
                        Notification::make()
                            ->title(__('accounts::filament/resources/incoterm.table.actions.restore.notification.title'))
                            ->body(__('accounts::filament/resources/incoterm.table.actions.restore.notification.body'))
                    ),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make()
                        ->successNotification(
                            Notification::make()
                                ->title(__('accounts::filament/resources/incoterm.table.bulk-actions.delete.notification.title'))
                                ->body(__('accounts::filament/resources/incoterm.table.bulk-actions.delete.notification.body'))
                        ),
                    ForceDeleteBulkAction::make()
                        ->successNotification(
                            Notification::make()
                                ->title(__('accounts::filament/resources/incoterm.table.bulk-actions.force-delete.notification.title'))
                                ->body(__('accounts::filament/resources/incoterm.table.bulk-actions.force-delete.notification.body'))
                        ),
                    RestoreBulkAction::make()
                        ->successNotification(
                            Notification::make()
                                ->title(__('accounts::filament/resources/incoterm.table.bulk-actions.restore.notification.title'))
                                ->body(__('accounts::filament/resources/incoterm.table.bulk-actions.restore.notification.body'))
                        ),
                ]),
            ]);
    }

    public static function infolist(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextEntry::make('code')
                    ->placeholder(__('accounts::filament/resources/incoterm.infolist.entries.code')),
                TextEntry::make('name')
                    ->placeholder(__('accounts::filament/resources/incoterm.infolist.entries.name')),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListIncoTerms::route('/'),
        ];
    }
}
