<?php

namespace Webkul\Sale\Filament\Clusters\Configuration\Resources;

use Filament\Forms;
use Filament\Forms\Form;
use Filament\Infolists;
use Filament\Infolists\Infolist;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Model;
use Webkul\Sale\Filament\Clusters\Configuration;
use Webkul\Sale\Filament\Clusters\Configuration\Resources\TagResource\Pages;
use Webkul\Sale\Models\Tag;

class TagResource extends Resource
{
    protected static ?string $model = Tag::class;

    protected static ?string $navigationIcon = 'heroicon-o-tag';

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

    public static function getGloballySearchableAttributes(): array
    {
        return [
            'name',
        ];
    }

    public static function getGlobalSearchResultDetails(Model $record): array
    {
        return [
            __('sales::filament/clusters/configurations/resources/tag.global-search.name') => $record->name ?? '—',
        ];
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('name')
                    ->label(__('sales::filament/clusters/configurations/resources/tag.form.fields.name'))
                    ->required()
                    ->placeholder(__('Name')),
                Forms\Components\ColorPicker::make('color')
                    ->label(__('sales::filament/clusters/configurations/resources/tag.form.fields.color'))
                    ->hexColor(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->searchable()
                    ->sortable()
                    ->label(__('sales::filament/clusters/configurations/resources/tag.table.columns.name')),
                Tables\Columns\ColorColumn::make('color')
                    ->label(__('sales::filament/clusters/configurations/resources/tag.table.columns.color')),
                Tables\Columns\TextColumn::make('createdBy.name')
                    ->label(__('sales::filament/clusters/configurations/resources/tag.table.columns.created-by')),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make()
                    ->successNotification(
                        Notification::make()
                            ->success()
                            ->title(__('sales::filament/clusters/configurations/resources/tag.table.actions.edit.notification.title'))
                            ->body(__('sales::filament/clusters/configurations/resources/tag.table.actions.edit.notification.body'))
                    ),
                Tables\Actions\DeleteAction::make()
                    ->successNotification(
                        Notification::make()
                            ->success()
                            ->title(__('sales::filament/clusters/configurations/resources/tag.table.actions.delete.notification.title'))
                            ->body(__('sales::filament/clusters/configurations/resources/tag.table.actions.delete.notification.body'))
                    ),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make()
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
            'index' => Pages\ListTags::route('/'),
        ];
    }

    public static function infolist(Infolist $infolist): Infolist
    {
        return $infolist
            ->schema([
                Infolists\Components\TextEntry::make('name')
                    ->label(__('sales::filament/clusters/configurations/resources/tag.infolist.entries.name'))
                    ->placeholder('-'),
                Infolists\Components\ColorEntry::make('color')
                    ->label(__('sales::filament/clusters/configurations/resources/tag.infolist.entries.color')),
            ]);
    }
}
