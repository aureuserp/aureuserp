<?php

namespace Webkul\Partner\Filament\Resources;

use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Webkul\Partner\Models\Title;

class TitleResource extends Resource
{
    protected static ?string $model = Title::class;

    protected static bool $shouldRegisterNavigation = false;

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('name')
                    ->label(__('partners::filament/resources/title.form.name'))
                    ->required()
                    ->maxLength(255),
                TextInput::make('short_name')
                    ->label(__('partners::filament/resources/title.form.short-name'))
                    ->required()
                    ->maxLength(255),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')
                    ->label(__('partners::filament/resources/title.table.columns.name'))
                    ->searchable(),
                TextColumn::make('short_name')
                    ->label(__('partners::filament/resources/title.table.columns.short-name'))
                    ->searchable(),
                TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->recordActions([
                EditAction::make()
                    ->successNotification(
                        Notification::make()
                            ->success()
                            ->title(__('partners::filament/resources/title.table.actions.edit.notification.title'))
                            ->body(__('partners::filament/resources/title.table.actions.edit.notification.body')),
                    ),
                DeleteAction::make()
                    ->successNotification(
                        Notification::make()
                            ->success()
                            ->title(__('partners::filament/resources/title.table.actions.delete.notification.title'))
                            ->body(__('partners::filament/resources/title.table.actions.delete.notification.body')),
                    ),
            ])
            ->toolbarActions([
                DeleteBulkAction::make()
                    ->successNotification(
                        Notification::make()
                            ->success()
                            ->title(__('partners::filament/resources/title.table.bulk-actions.delete.notification.title'))
                            ->body(__('partners::filament/resources/title.table.bulk-actions.delete.notification.body')),
                    ),
            ]);
    }
}
