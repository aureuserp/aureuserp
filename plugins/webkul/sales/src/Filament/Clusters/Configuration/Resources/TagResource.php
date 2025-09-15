<?php

namespace Webkul\Sale\Filament\Clusters\Configuration\Resources;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Forms\Components\ColorPicker;
use Filament\Forms\Components\TextInput;
use Filament\Infolists\Components\ColorEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\ColorColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Webkul\Sale\Filament\Clusters\Configuration;
use Webkul\Sale\Filament\Clusters\Configuration\Resources\TagResource\Pages\ListTags;
use Webkul\Sale\Models\Tag;

class TagResource extends Resource
{
    protected static ?string $model = Tag::class;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-tag';

    protected static ?string $cluster = Configuration::class;

    public static function getModelLabel(): string
    {
        return __('sales::filament/clusters/configurations/resources/tag.title');
    }

    public static function getNavigationLabel(): string
    {
        return __('sales::filament/clusters/configurations/resources/tag.navigation.title');
    }

    public static function getNavigationGroup(): ?string
    {
        return __('sales::filament/clusters/configurations/resources/tag.navigation.group');
    }

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('name')
                    ->label(__('sales::filament/clusters/configurations/resources/tag.form.fields.name'))
                    ->required()
                    ->placeholder(__('Name')),
                ColorPicker::make('color')
                    ->label(__('sales::filament/clusters/configurations/resources/tag.form.fields.color'))
                    ->hexColor(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')
                    ->searchable()
                    ->sortable()
                    ->label(__('sales::filament/clusters/configurations/resources/tag.table.columns.name')),
                ColorColumn::make('color')
                    ->label(__('sales::filament/clusters/configurations/resources/tag.table.columns.color')),
                TextColumn::make('createdBy.name')
                    ->label(__('sales::filament/clusters/configurations/resources/tag.table.columns.created-by')),
            ])
            ->recordActions([
                ViewAction::make(),
                EditAction::make()
                    ->successNotification(
                        Notification::make()
                            ->success()
                            ->title(__('sales::filament/clusters/configurations/resources/tag.table.actions.edit.notification.title'))
                            ->body(__('sales::filament/clusters/configurations/resources/tag.table.actions.edit.notification.body'))
                    ),
                DeleteAction::make()
                    ->successNotification(
                        Notification::make()
                            ->success()
                            ->title(__('sales::filament/clusters/configurations/resources/tag.table.actions.delete.notification.title'))
                            ->body(__('sales::filament/clusters/configurations/resources/tag.table.actions.delete.notification.body'))
                    ),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make()
                        ->successNotification(
                            Notification::make()
                                ->success()
                                ->title(__('sales::filament/clusters/configurations/resources/tag.table.bulk-actions.delete.notification.title'))
                                ->body(__('sales::filament/clusters/configurations/resources/tag.table.bulk-actions.delete.notification.body'))
                        ),
                ]),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListTags::route('/'),
        ];
    }

    public static function infolist(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextEntry::make('name')
                    ->label(__('sales::filament/clusters/configurations/resources/tag.infolist.entries.name'))
                    ->placeholder('-'),
                ColorEntry::make('color')
                    ->label(__('sales::filament/clusters/configurations/resources/tag.infolist.entries.color')),
            ]);
    }
}
