<?php

namespace Webkul\Product\Filament\Resources\Categories\Schemas;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Group;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class CategoryForm
{
    public static function configure(Schema $schema): Schema
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
            ->columns(3);
    }
}
