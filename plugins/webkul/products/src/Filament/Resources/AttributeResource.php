<?php

namespace Webkul\Product\Filament\Resources;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\CreateAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ForceDeleteAction;
use Filament\Actions\ForceDeleteBulkAction;
use Filament\Actions\RestoreAction;
use Filament\Actions\RestoreBulkAction;
use Filament\Actions\ViewAction;
use Filament\Forms\Components\ColorPicker;
use Filament\Forms\Components\Radio;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\TextInput;
use Filament\Infolists\Components\TextEntry;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Schema;
use Filament\Support\Enums\FontWeight;
use Filament\Support\Enums\TextSize;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Grouping\Group;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\QueryException;
use Webkul\Product\Enums\AttributeType;
use Webkul\Product\Models\Attribute;

class AttributeResource extends Resource
{
    protected static ?string $model = Attribute::class;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-swatch';

    protected static bool $shouldRegisterNavigation = false;

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make(__('products::filament/resources/attribute.form.sections.general.title'))
                    ->schema([
                        TextInput::make('name')
                            ->label(__('products::filament/resources/attribute.form.sections.general.fields.name'))
                            ->required()
                            ->maxLength(255),
                        Radio::make('type')
                            ->label(__('products::filament/resources/attribute.form.sections.general.fields.type'))
                            ->required()
                            ->options(AttributeType::class)
                            ->default(AttributeType::RADIO->value)
                            ->live(),
                    ]),

                Section::make(__('products::filament/resources/attribute.form.sections.options.title'))
                    ->schema([
                        Repeater::make(__('products::filament/resources/attribute.form.sections.options.title'))
                            ->hiddenLabel()
                            ->compact()
                            ->relationship('options')
                            ->schema([
                                TextInput::make('name')
                                    ->label(__('products::filament/resources/attribute.form.sections.options.fields.name'))
                                    ->required()
                                    ->maxLength(255),
                                ColorPicker::make('color')
                                    ->label(__('products::filament/resources/attribute.form.sections.options.fields.color'))
                                    ->hexColor()
                                    ->visible(fn (Get $get): bool => $get('../../type') === AttributeType::COLOR),
                                TextInput::make('extra_price')
                                    ->label(__('products::filament/resources/attribute.form.sections.options.fields.extra-price'))
                                    ->required()
                                    ->numeric()
                                    ->default(0.0000)
                                    ->minValue(0)
                                    ->maxValue(99999999999),
                            ])
                            ->columns(3),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')
                    ->label(__('products::filament/resources/attribute.table.columns.name'))
                    ->searchable(),
                TextColumn::make('type')
                    ->label(__('products::filament/resources/attribute.table.columns.type'))
                    ->sortable(),
                TextColumn::make('created_at')
                    ->label(__('products::filament/resources/attribute.table.columns.created-at'))
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('updated_at')
                    ->label(__('products::filament/resources/attribute.table.columns.updated-at'))
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->groups([
                Group::make('type')
                    ->label(__('products::filament/resources/attribute.table.groups.type'))
                    ->collapsible(),
                Group::make('created_at')
                    ->label(__('products::filament/resources/attribute.table.groups.created-at'))
                    ->collapsible(),
                Group::make('updated_at')
                    ->label(__('products::filament/resources/attribute.table.groups.updated-at'))
                    ->date()
                    ->collapsible(),
            ])
            ->filters([
                SelectFilter::make('type')
                    ->label(__('products::filament/resources/attribute.table.filters.type'))
                    ->options(AttributeType::class)
                    ->searchable()
                    ->preload(),
            ])
            ->recordActions([
                ViewAction::make()
                    ->hidden(fn ($record) => $record->trashed()),
                EditAction::make()
                    ->hidden(fn ($record) => $record->trashed()),
                RestoreAction::make()
                    ->successNotification(
                        Notification::make()
                            ->success()
                            ->title(__('products::filament/resources/attribute.table.actions.restore.notification.title'))
                            ->body(__('products::filament/resources/attribute.table.actions.restore.notification.body')),
                    ),
                DeleteAction::make()
                    ->successNotification(
                        Notification::make()
                            ->success()
                            ->title(__('products::filament/resources/attribute.table.actions.delete.notification.title'))
                            ->body(__('products::filament/resources/attribute.table.actions.delete.notification.body')),
                    ),
                ForceDeleteAction::make()
                    ->action(function (Attribute $record) {
                        try {
                            $record->forceDelete();
                        } catch (QueryException $e) {
                            Notification::make()
                                ->danger()
                                ->title(__('products::filament/resources/attribute.table.actions.force-delete.notification.error.title'))
                                ->body(__('products::filament/resources/attribute.table.actions.force-delete.notification.error.body'))
                                ->send();
                        }
                    })
                    ->successNotification(
                        Notification::make()
                            ->success()
                            ->title(__('products::filament/resources/attribute.table.actions.force-delete.notification.success.title'))
                            ->body(__('products::filament/resources/attribute.table.actions.force-delete.notification.success.body')),
                    ),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    RestoreBulkAction::make()
                        ->successNotification(
                            Notification::make()
                                ->success()
                                ->title(__('products::filament/resources/attribute.table.bulk-actions.restore.notification.title'))
                                ->body(__('products::filament/resources/attribute.table.bulk-actions.restore.notification.body')),
                        ),
                    DeleteBulkAction::make()
                        ->successNotification(
                            Notification::make()
                                ->success()
                                ->title(__('products::filament/resources/attribute.table.bulk-actions.delete.notification.title'))
                                ->body(__('products::filament/resources/attribute.table.bulk-actions.delete.notification.body')),
                        ),
                    ForceDeleteBulkAction::make()
                        ->action(function (Collection $records) {
                            try {
                                $records->each(fn (Model $record) => $record->forceDelete());
                            } catch (QueryException $e) {
                                Notification::make()
                                    ->danger()
                                    ->title(__('products::filament/resources/attribute.table.bulk-actions.force-delete.notification.error.title'))
                                    ->body(__('products::filament/resources/attribute.table.bulk-actions.force-delete.notification.error.body'))
                                    ->send();
                            }
                        })
                        ->successNotification(
                            Notification::make()
                                ->success()
                                ->title(__('products::filament/resources/attribute.table.bulk-actions.force-delete.notification.success.title'))
                                ->body(__('products::filament/resources/attribute.table.bulk-actions.force-delete.notification.success.body')),
                        ),
                ]),
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
                \Filament\Schemas\Components\Group::make()
                    ->schema([
                        Section::make(__('products::filament/resources/attribute.infolist.sections.general.title'))
                            ->schema([
                                TextEntry::make('name')
                                    ->label(__('products::filament/resources/attribute.infolist.sections.general.entries.name'))
                                    ->size(TextSize::Large)
                                    ->weight(FontWeight::Bold),

                                TextEntry::make('type')
                                    ->label(__('products::filament/resources/attribute.infolist.sections.general.entries.type'))
                                    ->placeholder('—'),
                            ]),
                    ])
                    ->columnSpan(['lg' => 2]),

                \Filament\Schemas\Components\Group::make()
                    ->schema([
                        Section::make(__('products::filament/resources/attribute.infolist.sections.record-information.title'))
                            ->schema([
                                TextEntry::make('creator.name')
                                    ->label(__('products::filament/resources/attribute.infolist.sections.record-information.entries.creator'))
                                    ->icon('heroicon-o-user')
                                    ->placeholder('—'),

                                TextEntry::make('created_at')
                                    ->label(__('products::filament/resources/attribute.infolist.sections.record-information.entries.created_at'))
                                    ->dateTime()
                                    ->icon('heroicon-o-calendar')
                                    ->placeholder('—'),

                                TextEntry::make('updated_at')
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
