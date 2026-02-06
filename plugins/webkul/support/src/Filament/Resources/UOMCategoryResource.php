<?php

namespace Webkul\Support\Filament\Resources;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Grouping\Group;
use Filament\Tables\Table;
use Webkul\Support\Enums\UOMType;
use Webkul\Support\Filament\Forms\Components\Repeater;
use Webkul\Support\Models\UOMCategory;

class UOMCategoryResource extends Resource
{
    protected static ?string $model = UOMCategory::class;

    protected static bool $shouldRegisterNavigation = false;

    public static function getNavigationGroup(): string
    {
        return __('support::filament/resources/uom-category.navigation.group');
    }

    public static function getNavigationLabel(): string
    {
        return __('support::filament/resources/uom-category.navigation.title');
    }

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make(__('support::filament/resources/uom-category.form.sections.general.title'))
                    ->schema([
                        TextInput::make('name')
                            ->label(__('support::filament/resources/uom-category.form.sections.general.fields.name'))
                            ->maxLength(255)
                            ->required(),
                    ])
                    ->columns(1),
                Section::make(__('support::filament/resources/uom-category.form.sections.uoms.title'))
                    ->schema([
                        Repeater::make('uoms')
                            ->label(__('support::filament/resources/uom-category.form.sections.uoms.fields.uoms'))
                            ->relationship('uoms')
                            ->schema([
                                Select::make('type')
                                    ->label(__('support::filament/resources/uom-category.form.sections.uoms.fields.type'))
                                    ->options(UOMType::class)
                                    ->required()
                                    ->native(false),
                                TextInput::make('name')
                                    ->label(__('support::filament/resources/uom-category.form.sections.uoms.fields.name'))
                                    ->maxLength(255)
                                    ->required(),
                                TextInput::make('factor')
                                    ->label(__('support::filament/resources/uom-category.form.sections.uoms.fields.factor'))
                                    ->numeric()
                                    ->default(1)
                                    ->required()
                                    ->step(0.0001)
                                    ->minValue(0),
                                TextInput::make('rounding')
                                    ->label(__('support::filament/resources/uom-category.form.sections.uoms.fields.rounding'))
                                    ->numeric()
                                    ->default(0.01)
                                    ->required()
                                    ->step(0.0001)
                                    ->minValue(0),
                            ])
                            ->columns(4)
                            ->defaultItems(1)
                            ->addActionLabel(__('support::filament/resources/uom-category.form.sections.uoms.actions.add'))
                            ->reorderable(false)
                            ->collapsible()
                            ->itemLabel(fn (array $state): ?string => $state['name'] ?? null),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')
                    ->label(__('support::filament/resources/uom-category.table.columns.name'))
                    ->searchable()
                    ->sortable(),
                TextColumn::make('uoms_count')
                    ->label(__('support::filament/resources/uom-category.table.columns.uoms-count'))
                    ->counts('uoms')
                    ->sortable(),
                TextColumn::make('created_at')
                    ->label(__('support::filament/resources/uom-category.table.columns.created-at'))
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('updated_at')
                    ->label(__('support::filament/resources/uom-category.table.columns.updated-at'))
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->groups([
                Group::make('created_at')
                    ->label(__('support::filament/resources/uom-category.table.groups.created-at'))
                    ->date(),
            ])
            ->recordActions([
                EditAction::make()
                    ->successNotification(
                        Notification::make()
                            ->success()
                            ->title(__('support::filament/resources/uom-category.table.actions.edit.notification.title'))
                            ->body(__('support::filament/resources/uom-category.table.actions.edit.notification.body')),
                    ),
                DeleteAction::make()
                    ->successNotification(
                        Notification::make()
                            ->success()
                            ->title(__('support::filament/resources/uom-category.table.actions.delete.notification.title'))
                            ->body(__('support::filament/resources/uom-category.table.actions.delete.notification.body')),
                    ),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make()
                        ->successNotification(
                            Notification::make()
                                ->success()
                                ->title(__('support::filament/resources/uom-category.table.bulk-actions.delete.notification.title'))
                                ->body(__('support::filament/resources/uom-category.table.bulk-actions.delete.notification.body')),
                        ),
                ]),
            ]);
    }
}
