<?php

namespace Webkul\Partner\Filament\Resources;

use Filament\Forms;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Webkul\Partner\Models\Tag;

class TagResource extends Resource
{
    protected static ?string $model = Tag::class;

    protected static bool $shouldRegisterNavigation = false;

    public static function getNavigationLabel(): string
    {
        return __('partners::filament/resources/tag.navigation.title');
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('name')
                    ->label(__('partners::filament/resources/tag.form.name'))
                    ->required()
                    ->maxLength(255)
                    ->unique(ignoreRecord: true),
                Forms\Components\ColorPicker::make('color')
                    ->label(__('partners::filament/resources/tag.form.color'))
                    ->hexColor(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->label(__('partners::filament/resources/tag.table.columns.name'))
                    ->searchable(),
                Tables\Columns\ColorColumn::make('color')
                    ->label(__('partners::filament/resources/tag.table.columns.color')),
            ])
            ->actions([
                Tables\Actions\EditAction::make()
                    ->hidden(fn ($record) => $record->trashed())
                    ->successNotification(
                        Notification::make()
                            ->success()
                            ->title(__('partners::filament/resources/tag.table.actions.edit.notification.title'))
                            ->body(__('partners::filament/resources/tag.table.actions.edit.notification.body')),
                    ),
                Tables\Actions\RestoreAction::make()
                    ->successNotification(
                        Notification::make()
                            ->success()
                            ->title(__('partners::filament/resources/tag.table.actions.restore.notification.title'))
                            ->body(__('partners::filament/resources/tag.table.actions.restore.notification.body')),
                    ),
                Tables\Actions\DeleteAction::make()
                    ->successNotification(
                        Notification::make()
                            ->success()
                            ->title(__('partners::filament/resources/tag.table.actions.delete.notification.title'))
                            ->body(__('partners::filament/resources/tag.table.actions.delete.notification.body')),
                    ),
                Tables\Actions\ForceDeleteAction::make()
                    ->successNotification(
                        Notification::make()
                            ->success()
                            ->title(__('partners::filament/resources/tag.table.actions.force-delete.notification.title'))
                            ->body(__('partners::filament/resources/tag.table.actions.force-delete.notification.body')),
                    ),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\RestoreBulkAction::make()
                        ->successNotification(
                            Notification::make()
                                ->success()
                                ->title(__('partners::filament/resources/tag.table.bulk-actions.restore.notification.title'))
                                ->body(__('partners::filament/resources/tag.table.bulk-actions.restore.notification.body')),
                        ),
                    Tables\Actions\DeleteBulkAction::make()
                        ->successNotification(
                            Notification::make()
                                ->success()
                                ->title(__('partners::filament/resources/tag.table.bulk-actions.delete.notification.title'))
                                ->body(__('partners::filament/resources/tag.table.bulk-actions.delete.notification.body')),
                        ),
                    Tables\Actions\ForceDeleteBulkAction::make()
                        ->successNotification(
                            Notification::make()
                                ->success()
                                ->title(__('partners::filament/resources/tag.table.bulk-actions.force-delete.notification.title'))
                                ->body(__('partners::filament/resources/tag.table.bulk-actions.force-delete.notification.body')),
                        ),
                ]),
            ]);
    }
}
