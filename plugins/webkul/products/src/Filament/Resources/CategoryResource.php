<?php

namespace Webkul\Product\Filament\Resources;

use Filament\Forms;
use Filament\Forms\Form;
use Filament\Infolists;
use Filament\Infolists\Infolist;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Support\Enums\FontWeight;
use Filament\Tables;
use Illuminate\Database\QueryException;
use Illuminate\Database\Eloquent\Collection;
use Filament\Tables\Table;
use Webkul\Product\Models\Category;
use Illuminate\Database\Eloquent\Model;

class CategoryResource extends Resource
{
    protected static ?string $model = Category::class;

    protected static ?string $navigationIcon = 'heroicon-o-folder';

    protected static bool $shouldRegisterNavigation = false;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Group::make()
                    ->schema([
                        Forms\Components\Section::make(__('products::filament/resources/category.form.sections.general.title'))
                            ->schema([
                                Forms\Components\TextInput::make('name')
                                    ->label(__('products::filament/resources/category.form.sections.general.fields.name'))
                                    ->required()
                                    ->maxLength(255)
                                    ->autofocus()
                                    ->placeholder(__('products::filament/resources/category.form.sections.general.fields.name-placeholder'))
                                    ->extraInputAttributes(['style' => 'font-size: 1.5rem;height: 3rem;'])
                                    ->unique(ignoreRecord: true),
                                Forms\Components\Select::make('parent_id')
                                    ->label(__('products::filament/resources/category.form.sections.general.fields.parent'))
                                    ->relationship('parent', 'full_name')
                                    ->searchable()
                                    ->preload(),
                            ]),
                    ])
                    ->columnSpan(['lg' => 2]),

                Forms\Components\Group::make()
                    ->schema([])
                    ->columnSpan(['lg' => 1]),
            ])
            ->columns(3);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->label(__('products::filament/resources/category.table.columns.name'))
                    ->searchable(),
                Tables\Columns\TextColumn::make('full_name')
                    ->label(__('products::filament/resources/category.table.columns.full-name'))
                    ->searchable(),
                Tables\Columns\TextColumn::make('parent_path')
                    ->label(__('products::filament/resources/category.table.columns.parent-path'))
                    ->placeholder('—')
                    ->searchable(),
                Tables\Columns\TextColumn::make('parent.name')
                    ->label(__('products::filament/resources/category.table.columns.parent'))
                    ->placeholder('—')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('creator.name')
                    ->label(__('products::filament/resources/category.table.columns.creator'))
                    ->placeholder('—')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('created_at')
                    ->label(__('products::filament/resources/category.table.columns.created-at'))
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('updated_at')
                    ->label(__('products::filament/resources/category.table.columns.updated-at'))
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->groups([
                Tables\Grouping\Group::make('parent.full_name')
                    ->label(__('products::filament/resources/category.table.groups.parent'))
                    ->collapsible(),
                Tables\Grouping\Group::make('creator.name')
                    ->label(__('products::filament/resources/category.table.groups.creator'))
                    ->collapsible(),
                Tables\Grouping\Group::make('created_at')
                    ->label(__('products::filament/resources/category.table.groups.created-at'))
                    ->collapsible(),
                Tables\Grouping\Group::make('updated_at')
                    ->label(__('products::filament/resources/category.table.groups.updated-at'))
                    ->date()
                    ->collapsible(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('parent_id')
                    ->label(__('products::filament/resources/category.table.filters.parent'))
                    ->relationship('parent', 'full_name')
                    ->searchable()
                    ->preload(),
                Tables\Filters\SelectFilter::make('creator_id')
                    ->label(__('products::filament/resources/category.table.filters.creator'))
                    ->relationship('creator', 'name')
                    ->searchable()
                    ->preload(),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make()
                    ->action(function (Category $record) {
                        try {
                            $record->delete();
                        } catch (QueryException $e) {
                            Notification::make()
                                ->danger()
                                ->title(__('products::filament/resources/category.table.actions.delete.notification.error.title'))
                                ->body(__('products::filament/resources/category.table.actions.delete.notification.error.body'))
                                ->send();
                        }
                    })
                    ->successNotification(
                        Notification::make()
                            ->success()
                            ->title(__('products::filament/resources/category.table.actions.delete.notification.success.title'))
                            ->body(__('products::filament/resources/category.table.actions.delete.notification.success.body')),
                    ),
            ])
            ->bulkActions([
                Tables\Actions\DeleteBulkAction::make()
                    ->action(function (Collection $records) {
                        try {
                            $records->each(fn (Model $record) => $record->delete());
                        } catch (QueryException $e) {
                            Notification::make()
                                ->danger()
                                ->title(__('products::filament/resources/category.table.bulk-actions.delete.notification.error.title'))
                                ->body(__('products::filament/resources/category.table.bulk-actions.delete.notification.error.body'))
                                ->send();
                        }
                    })
                    ->successNotification(
                        Notification::make()
                            ->success()
                            ->title(__('products::filament/resources/category.table.bulk-actions.delete.notification.success.title'))
                            ->body(__('products::filament/resources/category.table.bulk-actions.delete.notification.success.body')),
                    ),
            ])
            ->emptyStateActions([
                Tables\Actions\CreateAction::make()
                    ->icon('heroicon-o-plus-circle'),
            ]);
    }

    public static function infolist(Infolist $infolist): Infolist
    {
        return $infolist
            ->schema([
                Infolists\Components\Group::make()
                    ->schema([
                        Infolists\Components\Section::make(__('products::filament/resources/category.infolist.sections.general.title'))
                            ->schema([
                                Infolists\Components\TextEntry::make('name')
                                    ->label(__('products::filament/resources/category.infolist.sections.general.entries.name'))
                                    ->weight(FontWeight::Bold)
                                    ->size(Infolists\Components\TextEntry\TextEntrySize::Large)
                                    ->icon('heroicon-o-document-text'),

                                Infolists\Components\TextEntry::make('parent.name')
                                    ->label(__('products::filament/resources/category.infolist.sections.general.entries.parent'))
                                    ->icon('heroicon-o-folder')
                                    ->placeholder('—'),

                                Infolists\Components\TextEntry::make('full_name')
                                    ->label(__('products::filament/resources/category.infolist.sections.general.entries.full_name'))
                                    ->icon('heroicon-o-folder-open')
                                    ->placeholder('—'),

                                Infolists\Components\TextEntry::make('parent_path')
                                    ->label(__('products::filament/resources/category.infolist.sections.general.entries.parent_path'))
                                    ->icon('heroicon-o-arrows-right-left')
                                    ->placeholder('—'),
                            ]),
                    ])
                    ->columnSpan(['lg' => 2]),

                Infolists\Components\Group::make()
                    ->schema([
                        Infolists\Components\Section::make(__('products::filament/resources/category.infolist.sections.record-information.title'))
                            ->schema([
                                Infolists\Components\TextEntry::make('creator.name')
                                    ->label(__('products::filament/resources/category.infolist.sections.record-information.entries.creator'))
                                    ->icon('heroicon-o-user')
                                    ->placeholder('—'),

                                Infolists\Components\TextEntry::make('created_at')
                                    ->label(__('products::filament/resources/category.infolist.sections.record-information.entries.created_at'))
                                    ->dateTime()
                                    ->icon('heroicon-o-calendar')
                                    ->placeholder('—'),

                                Infolists\Components\TextEntry::make('updated_at')
                                    ->label(__('products::filament/resources/category.infolist.sections.record-information.entries.updated_at'))
                                    ->dateTime()
                                    ->icon('heroicon-o-clock')
                                    ->placeholder('—'),
                            ])
                            ->icon('heroicon-o-information-circle')
                            ->collapsible(),
                    ])
                    ->columnSpan(['lg' => 1]),
            ])
            ->columns(3);
    }
}
