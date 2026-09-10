<?php

namespace Webkul\Support\Filament\Resources\UOMCategoryResource\Schemas;

use Closure;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Components\Utilities\Set;
use Filament\Schemas\Schema;
use Webkul\Support\Enums\UOMType;
use Webkul\Support\Filament\Forms\Components\Repeater;
use Webkul\Support\Filament\Forms\Components\Repeater\TableColumn;

class UOMCategoryForm
{
    public static function configure(Schema $schema): Schema
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

    public static function getReferenceRowAttributes(Get $get): array
    {
        return static::isReferenceType($get('type'))
            ? ['style' => 'font-weight: 700;']
            : [];
    }

    public static function isReferenceType(mixed $type): bool
    {
        if (! $type instanceof UOMType) {
            $type = UOMType::tryFrom((string) $type);
        }

        return $type === UOMType::REFERENCE;
    }
}
