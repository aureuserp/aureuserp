<?php

namespace Webkul\Field\Filament\Resources\Fields\Tables;

use Filament\Actions\ActionGroup;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ForceDeleteAction;
use Filament\Actions\ForceDeleteBulkAction;
use Filament\Actions\RestoreAction;
use Filament\Actions\RestoreBulkAction;
use Filament\Actions\ViewAction;
use Filament\Facades\Filament;
use Filament\Notifications\Notification;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Webkul\Field\FieldsColumnManager;
use Webkul\Field\Models\Field;

class FieldsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('code')
                    ->label(__('fields::filament/resources/field.table.columns.code'))
                    ->searchable()
                    ->sortable(),
                TextColumn::make('name')
                    ->label(__('fields::filament/resources/field.table.columns.name'))
                    ->searchable()
                    ->sortable(),
                TextColumn::make('type')
                    ->label(__('fields::filament/resources/field.table.columns.type'))
                    ->sortable(),
                TextColumn::make('customizable_type')
                    ->label(__('fields::filament/resources/field.table.columns.resource'))
                    ->description(fn (Field $record): string => str($record->customizable_type)->afterLast('\\')->toString().'Resource')
                    ->sortable(),
                TextColumn::make('created_at')
                    ->label(__('fields::filament/resources/field.table.columns.created-at'))
                    ->sortable(),
            ])
            ->filters([
                SelectFilter::make('type')
                    ->label(__('fields::filament/resources/field.table.filters.type'))
                    ->options([
                        'text'          => __('fields::filament/resources/field.table.filters.type-options.text'),
                        'textarea'      => __('fields::filament/resources/field.table.filters.type-options.textarea'),
                        'select'        => __('fields::filament/resources/field.table.filters.type-options.select'),
                        'checkbox'      => __('fields::filament/resources/field.table.filters.type-options.checkbox'),
                        'radio'         => __('fields::filament/resources/field.table.filters.type-options.radio'),
                        'toggle'        => __('fields::filament/resources/field.table.filters.type-options.toggle'),
                        'checkbox_list' => __('fields::filament/resources/field.table.filters.type-options.checkbox-list'),
                        'datetime'      => __('fields::filament/resources/field.table.filters.type-options.datetime'),
                        'editor'        => __('fields::filament/resources/field.table.filters.type-options.editor'),
                        'markdown'      => __('fields::filament/resources/field.table.filters.type-options.markdown'),
                        'color'         => __('fields::filament/resources/field.table.filters.type-options.color'),
                    ]),
                SelectFilter::make('customizable_type')
                    ->label(__('fields::filament/resources/field.table.filters.resource'))
                    ->options(fn () => collect(Filament::getResources())->filter(fn ($resource) => in_array('Webkul\Field\Filament\Traits\HasCustomFields', class_uses($resource)))->mapWithKeys(fn ($resource) => [
                        $resource::getModel() => str($resource)->afterLast('\\')->toString(),
                    ])),
            ])
            ->recordActions([
                ActionGroup::make([
                    EditAction::make()
                        ->hidden(fn ($record) => $record->trashed()),
                    ViewAction::make(),
                    RestoreAction::make()
                        ->successNotification(
                            Notification::make()
                                ->success()
                                ->title(__('fields::filament/resources/field.table.actions.restore.notification.title'))
                                ->body(__('fields::filament/resources/field.table.actions.restore.notification.body')),
                        ),
                    DeleteAction::make()
                        ->successNotification(
                            Notification::make()
                                ->success()
                                ->title(__('fields::filament/resources/field.table.actions.delete.notification.title'))
                                ->body(__('fields::filament/resources/field.table.actions.delete.notification.body')),
                        ),
                    ForceDeleteAction::make()
                        ->before(function ($record) {
                            FieldsColumnManager::deleteColumn($record);
                        })
                        ->successNotification(
                            Notification::make()
                                ->success()
                                ->title(__('fields::filament/resources/field.table.actions.force-delete.notification.title'))
                                ->body(__('fields::filament/resources/field.table.actions.force-delete.notification.body')),
                        ),
                ]),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    RestoreBulkAction::make()
                        ->successNotification(
                            Notification::make()
                                ->success()
                                ->title(__('fields::filament/resources/field.table.bulk-actions.restore.notification.title'))
                                ->body(__('fields::filament/resources/field.table.bulk-actions.restore.notification.body')),
                        ),
                    DeleteBulkAction::make()
                        ->successNotification(
                            Notification::make()
                                ->success()
                                ->title(__('fields::filament/resources/field.table.bulk-actions.delete.notification.title'))
                                ->body(__('fields::filament/resources/field.table.bulk-actions.delete.notification.body')),
                        ),
                    ForceDeleteBulkAction::make()
                        ->before(function ($records) {
                            foreach ($records as $record) {
                                FieldsColumnManager::deleteColumn($record);
                            }
                        })
                        ->successNotification(
                            Notification::make()
                                ->success()
                                ->title(__('fields::filament/resources/field.table.bulk-actions.force-delete.notification.title'))
                                ->body(__('fields::filament/resources/field.table.bulk-actions.force-delete.notification.body')),
                        ),
                ]),
            ])
            ->defaultSort('created_at', 'desc');
    }
}
