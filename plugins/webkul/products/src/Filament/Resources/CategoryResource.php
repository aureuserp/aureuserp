<?php

namespace Webkul\Product\Filament\Resources;

use Filament\Actions\CreateAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Infolists\Components\TextEntry;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Group;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Support\Enums\FontWeight;
use Filament\Support\Enums\TextSize;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\QueryException;
use Webkul\Product\Models\Category;

class CategoryResource extends Resource
{
    protected static ?string $model = Category::class;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-folder';

    protected static bool $shouldRegisterNavigation = false;

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Group::make()
                    ->schema([
                        Section::make(__('products::filament/resources/category.form.sections.general.title'))
                            ->schema([
                                TextInput::make('name')
                                    ->label(__('products::filament/resources/category.form.sections.general.fields.name'))
                                    ->required()
                                    ->maxLength(255)
                                    ->autofocus()
                                    ->placeholder(__('products::filament/resources/category.form.sections.general.fields.name-placeholder'))
                                    ->extraInputAttributes(['style' => 'font-size: 1.5rem;height: 3rem;'])
                                    ->unique(ignoreRecord: true),
                                Select::make('parent_id')
                                    ->label(__('products::filament/resources/category.form.sections.general.fields.parent'))
                                    ->relationship('parent', 'full_name')
                                    ->searchable()
                                    ->preload(),
                            ]),
                    ])
                    ->columnSpan(['lg' => 2]),

                Group::make()
                    ->schema([])
                    ->columnSpan(['lg' => 1]),
            ])
            ->columns(1);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->reorderableColumns()
            ->columns([
                TextColumn::make('name')
                    ->label(__('products::filament/resources/category.table.columns.name'))
                    ->searchable(),
                TextColumn::make('full_name')
                    ->label(__('products::filament/resources/category.table.columns.full-name'))
                    ->searchable(),
                TextColumn::make('parent_path')
                    ->label(__('products::filament/resources/category.table.columns.parent-path'))
                    ->placeholder('—')
                    ->searchable(),
                TextColumn::make('parent.name')
                    ->label(__('products::filament/resources/category.table.columns.parent'))
                    ->placeholder('—')
                    ->numeric()
                    ->sortable(),
                TextColumn::make('creator.name')
                    ->label(__('products::filament/resources/category.table.columns.creator'))
                    ->placeholder('—')
                    ->numeric()
                    ->sortable(),
                TextColumn::make('created_at')
                    ->label(__('products::filament/resources/category.table.columns.created-at'))
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('updated_at')
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
                SelectFilter::make('parent_id')
                    ->label(__('products::filament/resources/category.table.filters.parent'))
                    ->relationship('parent', 'full_name')
                    ->searchable()
                    ->preload(),
                SelectFilter::make('creator_id')
                    ->label(__('products::filament/resources/category.table.filters.creator'))
                    ->relationship('creator', 'name')
                    ->searchable()
                    ->preload(),
            ])
            ->recordActions([
                ViewAction::make(),
                EditAction::make(),
                DeleteAction::make()
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
            ->toolbarActions([
                DeleteBulkAction::make()
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
                CreateAction::make()
                    ->icon('heroicon-o-plus-circle'),
            ]);
    }

    public static function infolist(Schema $schema): Schema
    {
        return $schema
            ->components([
                Group::make()
                    ->schema([
                        Section::make(__('products::filament/resources/category.infolist.sections.general.title'))
                            ->schema([
                                TextEntry::make('name')
                                    ->label(__('products::filament/resources/category.infolist.sections.general.entries.name'))
                                    ->weight(FontWeight::Bold)
                                    ->size(TextSize::Large)
                                    ->icon('heroicon-o-document-text'),

                                TextEntry::make('parent.name')
                                    ->label(__('products::filament/resources/category.infolist.sections.general.entries.parent'))
                                    ->icon('heroicon-o-folder')
                                    ->placeholder('—'),

                                TextEntry::make('full_name')
                                    ->label(__('products::filament/resources/category.infolist.sections.general.entries.full_name'))
                                    ->icon('heroicon-o-folder-open')
                                    ->placeholder('—'),

                                TextEntry::make('parent_path')
                                    ->label(__('products::filament/resources/category.infolist.sections.general.entries.parent_path'))
                                    ->icon('heroicon-o-arrows-right-left')
                                    ->placeholder('—'),
                            ]),
                    ])
                    ->columnSpan(['lg' => 2]),

                Group::make()
                    ->schema([
                        Section::make(__('products::filament/resources/category.infolist.sections.record-information.title'))
                            ->schema([
                                TextEntry::make('creator.name')
                                    ->label(__('products::filament/resources/category.infolist.sections.record-information.entries.creator'))
                                    ->icon('heroicon-o-user')
                                    ->placeholder('—'),

                                TextEntry::make('created_at')
                                    ->label(__('products::filament/resources/category.infolist.sections.record-information.entries.created_at'))
                                    ->dateTime()
                                    ->icon('heroicon-o-calendar')
                                    ->placeholder('—'),

                                TextEntry::make('updated_at')
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
