<?php

namespace Webkul\Support\Filament\Resources;

use BackedEnum;
use Closure;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Components\Utilities\Set;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Grouping\Group;
use Filament\Tables\Table;
use Webkul\Support\Enums\UOMType;
use Webkul\Support\Filament\Forms\Components\Repeater;
use Webkul\Support\Filament\Forms\Components\Repeater\TableColumn;
use Webkul\Support\Models\UOMCategory;
use Webkul\Support\Filament\Resources\UOMCategoryResource\Pages\CreateUOMCategory;
use Webkul\Support\Filament\Resources\UOMCategoryResource\Pages\EditUOMCategory;
use Webkul\Support\Filament\Resources\UOMCategoryResource\Pages\ListUOMCategories;
use Webkul\Support\Filament\Resources\UOMCategoryResource\Pages\ViewUOMCategory;
use Webkul\Support\Enums\NavigationGroup;

class UOMCategoryResource extends Resource
{
    protected static ?string $model = UOMCategory::class;

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-scale';

    protected static bool $shouldRegisterNavigation = false;

    public static function getNavigationGroup(): string | \UnitEnum
    {
        return NavigationGroup::Setting;
    }

    public static function getNavigationLabel(): string
    {
        return __('support::filament/resources/uom-category.navigation.title');
    }

    /**
     * The reference unit of a category is highlighted, the same way Odoo emphasises it.
     *
     * An inline style is used instead of a utility class because the compiled theme only
     * ships the classes present at build time.
     */
    public static function getReferenceRowAttributes(Get $get): array
    {
        return static::isReferenceType($get('type'))
            ? ['style' => 'font-weight: 700;']
            : [];
    }

    /**
     * The state is an enum instance once hydrated, but a plain string when it comes back
     * from the browser, so both shapes have to be accepted.
     */
    public static function isReferenceType(mixed $type): bool
    {
        if (! $type instanceof UOMType) {
            $type = UOMType::tryFrom((string) $type);
        }

        return $type === UOMType::REFERENCE;
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
                            ->hiddenLabel()
                            ->relationship('uoms')
                            ->compact()
                            ->table([
                                TableColumn::make('name')
                                    ->label(__('support::filament/resources/uom-category.form.sections.uoms.fields.name'))
                                    ->resizable(),
                                TableColumn::make('type')
                                    ->label(__('support::filament/resources/uom-category.form.sections.uoms.fields.type'))
                                    ->resizable(),
                                TableColumn::make('ratio')
                                    ->label(__('support::filament/resources/uom-category.form.sections.uoms.fields.ratio'))
                                    ->resizable(),
                                TableColumn::make('rounding')
                                    ->label(__('support::filament/resources/uom-category.form.sections.uoms.fields.rounding'))
                                    ->resizable(),
                            ])
                            ->schema([
                                TextInput::make('name')
                                    ->label(__('support::filament/resources/uom-category.form.sections.uoms.fields.name'))
                                    ->maxLength(255)
                                    ->required()
                                    ->extraInputAttributes(static::getReferenceRowAttributes(...)),
                                Select::make('type')
                                    ->label(__('support::filament/resources/uom-category.form.sections.uoms.fields.type'))
                                    ->options(UOMType::class)
                                    ->default(UOMType::REFERENCE->value)
                                    ->required()
                                    ->native(false)
                                    ->live()
                                    ->afterStateUpdated(function ($state, Set $set): void {
                                        if (static::isReferenceType($state)) {
                                            $set('ratio', 1);
                                        }
                                    })
                                    ->extraAttributes(static::getReferenceRowAttributes(...)),
                                TextInput::make('ratio')
                                    ->label(__('support::filament/resources/uom-category.form.sections.uoms.fields.ratio'))
                                    ->numeric()
                                    ->default(1)
                                    ->required()
                                    ->step(0.000001)
                                    ->disabled(fn (Get $get): bool => static::isReferenceType($get('type')))
                                    ->dehydrated()
                                    ->rule('gt:0')
                                    ->validationMessages([
                                        'gt' => __('support::filament/resources/uom-category.form.sections.uoms.validations.ratio-greater-than-zero'),
                                    ])
                                    ->extraInputAttributes(static::getReferenceRowAttributes(...)),
                                TextInput::make('rounding')
                                    ->label(__('support::filament/resources/uom-category.form.sections.uoms.fields.rounding'))
                                    ->numeric()
                                    ->default(0.01)
                                    ->required()
                                    ->step(0.0001)
                                    ->rule('gt:0')
                                    ->validationMessages([
                                        'gt' => __('support::filament/resources/uom-category.form.sections.uoms.validations.rounding-greater-than-zero'),
                                    ])
                                    ->extraInputAttributes(static::getReferenceRowAttributes(...)),
                            ])
                            ->defaultItems(1)
                            ->minItems(1)
                            ->addActionLabel(__('support::filament/resources/uom-category.form.sections.uoms.actions.add'))
                            ->reorderable(false)
                            ->rules([
                                fn (): Closure => function (string $attribute, $value, Closure $fail): void {
                                    $references = collect($value)
                                        ->filter(fn ($uom): bool => static::isReferenceType($uom['type'] ?? null))
                                        ->count();

                                    if ($references === 0) {
                                        $fail(__('support::filament/resources/uom-category.form.sections.uoms.validations.missing-reference'));
                                    }

                                    if ($references > 1) {
                                        $fail(__('support::filament/resources/uom-category.form.sections.uoms.validations.multiple-references'));
                                    }
                                },
                            ]),
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
                TextColumn::make('uoms.name')
                    ->label(__('support::filament/resources/uom-category.table.columns.uoms'))
                    ->badge()
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

    public static function getPages(): array
    {
        return [
            'index'  => ListUOMCategories::route('/'),
            'create' => CreateUOMCategory::route('/create'),
            'view'   => ViewUOMCategory::route('/{record}'),
            'edit'   => EditUOMCategory::route('/{record}/edit'),
        ];
    }
}
