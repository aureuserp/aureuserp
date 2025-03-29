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
use Filament\Tables\Table;
use Webkul\Product\Enums\AttributeType;
use Webkul\Product\Models\Attribute;

class AttributeResource extends Resource
{
    protected static ?string $model = Attribute::class;

    protected static ?string $navigationIcon = 'heroicon-o-swatch';

    protected static bool $shouldRegisterNavigation = false;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make(__('products::filament/resources/attribute.form.sections.general.title'))
                    ->schema([
                        Forms\Components\TextInput::make('name')
                            ->label(__('products::filament/resources/attribute.form.sections.general.fields.name'))
                            ->required()
                            ->maxLength(255),
                        Forms\Components\Radio::make('type')
                            ->label(__('products::filament/resources/attribute.form.sections.general.fields.type'))
                            ->required()
                            ->options(AttributeType::class)
                            ->default(AttributeType::RADIO->value)
                            ->live(),
                    ]),

                Forms\Components\Section::make(__('products::filament/resources/attribute.form.sections.options.title'))
                    ->schema([
                        Forms\Components\Repeater::make(__('products::filament/resources/attribute.form.sections.options.title'))
                            ->hiddenLabel()
                            ->relationship('options')
                            ->schema([
                                Forms\Components\TextInput::make('name')
                                    ->label(__('products::filament/resources/attribute.form.sections.options.fields.name'))
                                    ->required()
                                    ->maxLength(255),
                                Forms\Components\ColorPicker::make('color')
                                    ->label(__('products::filament/resources/attribute.form.sections.options.fields.color'))
                                    ->hexColor()
                                    ->visible(fn (Forms\Get $get): bool => $get('../../type') === AttributeType::COLOR->value),
                                Forms\Components\TextInput::make('extra_price')
                                    ->label(__('products::filament/resources/attribute.form.sections.options.fields.extra-price'))
                                    ->required()
                                    ->numeric()
                                    ->default(0.0000)
                                    ->minValue(0),
                            ])
                            ->columns(3),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->label(__('products::filament/resources/attribute.table.columns.name'))
                    ->searchable(),
                Tables\Columns\TextColumn::make('type')
                    ->label(__('products::filament/resources/attribute.table.columns.type'))
                    ->sortable(),
                Tables\Columns\TextColumn::make('created_at')
                    ->label(__('products::filament/resources/attribute.table.columns.created-at'))
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('updated_at')
                    ->label(__('products::filament/resources/attribute.table.columns.updated-at'))
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->groups([
                Tables\Grouping\Group::make('type')
                    ->label(__('products::filament/resources/attribute.table.groups.type'))
                    ->collapsible(),
                Tables\Grouping\Group::make('created_at')
                    ->label(__('products::filament/resources/attribute.table.groups.created-at'))
                    ->collapsible(),
                Tables\Grouping\Group::make('updated_at')
                    ->label(__('products::filament/resources/attribute.table.groups.updated-at'))
                    ->date()
                    ->collapsible(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('type')
                    ->label(__('products::filament/resources/attribute.table.filters.type'))
                    ->options(AttributeType::class)
                    ->searchable()
                    ->preload(),
            ])
            ->actions([
                Tables\Actions\ViewAction::make()
                    ->hidden(fn ($record) => $record->trashed()),
                Tables\Actions\EditAction::make()
                    ->hidden(fn ($record) => $record->trashed()),
                Tables\Actions\RestoreAction::make()
                    ->successNotification(
                        Notification::make()
                            ->success()
                            ->title(__('products::filament/resources/attribute.table.actions.restore.notification.title'))
                            ->body(__('products::filament/resources/attribute.table.actions.restore.notification.body')),
                    ),
                Tables\Actions\DeleteAction::make()
                    ->successNotification(
                        Notification::make()
                            ->success()
                            ->title(__('products::filament/resources/attribute.table.actions.delete.notification.title'))
                            ->body(__('products::filament/resources/attribute.table.actions.delete.notification.body')),
                    ),
                Tables\Actions\ForceDeleteAction::make()
                    ->successNotification(
                        Notification::make()
                            ->success()
                            ->title(__('products::filament/resources/attribute.table.actions.force-delete.notification.title'))
                            ->body(__('products::filament/resources/attribute.table.actions.force-delete.notification.body')),
                    ),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\RestoreBulkAction::make()
                        ->successNotification(
                            Notification::make()
                                ->success()
                                ->title(__('products::filament/resources/attribute.table.bulk-actions.restore.notification.title'))
                                ->body(__('products::filament/resources/attribute.table.bulk-actions.restore.notification.body')),
                        ),
                    Tables\Actions\DeleteBulkAction::make()
                        ->successNotification(
                            Notification::make()
                                ->success()
                                ->title(__('products::filament/resources/attribute.table.bulk-actions.delete.notification.title'))
                                ->body(__('products::filament/resources/attribute.table.bulk-actions.delete.notification.body')),
                        ),
                    Tables\Actions\ForceDeleteBulkAction::make()
                        ->successNotification(
                            Notification::make()
                                ->success()
                                ->title(__('products::filament/resources/attribute.table.bulk-actions.force-delete.notification.title'))
                                ->body(__('products::filament/resources/attribute.table.bulk-actions.force-delete.notification.body')),
                        ),
                ]),
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
                        Infolists\Components\Section::make(__('products::filament/resources/attribute.infolist.sections.general.title'))
                            ->schema([
                                Infolists\Components\TextEntry::make('name')
                                    ->label(__('products::filament/resources/attribute.infolist.sections.general.entries.name'))
                                    ->size(Infolists\Components\TextEntry\TextEntrySize::Large)
                                    ->weight(FontWeight::Bold),

                                Infolists\Components\TextEntry::make('type')
                                    ->label(__('products::filament/resources/attribute.infolist.sections.general.entries.type'))
                                    ->placeholder('—'),
                            ]),
                    ])
                    ->columnSpan(['lg' => 2]),

                Infolists\Components\Group::make()
                    ->schema([
                        Infolists\Components\Section::make(__('products::filament/resources/attribute.infolist.sections.record-information.title'))
                            ->schema([
                                Infolists\Components\TextEntry::make('creator.name')
                                    ->label(__('products::filament/resources/attribute.infolist.sections.record-information.entries.creator'))
                                    ->icon('heroicon-o-user')
                                    ->placeholder('—'),

                                Infolists\Components\TextEntry::make('created_at')
                                    ->label(__('products::filament/resources/attribute.infolist.sections.record-information.entries.created_at'))
                                    ->dateTime()
                                    ->icon('heroicon-o-calendar')
                                    ->placeholder('—'),

                                Infolists\Components\TextEntry::make('updated_at')
                                    ->label(__('products::filament/resources/attribute.infolist.sections.record-information.entries.updated_at'))
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
