<?php

namespace Webkul\PointOfSale\Filament\Admin\Clusters\Configurations\Resources\CategoryResource\Schemas;

use Filament\Forms\Components\ColorPicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class CategoryForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make(__('point-of-sale::filament/admin/clusters/configurations/resources/category.form.sections.general.title'))
                    ->schema([
                        TextInput::make('name')
                            ->label(__('point-of-sale::filament/admin/clusters/configurations/resources/category.form.sections.general.fields.name'))
                            ->required()
                            ->maxLength(255)
                            ->autofocus(),

                        Select::make('parent_id')
                            ->label(__('point-of-sale::filament/admin/clusters/configurations/resources/category.form.sections.general.fields.parent'))
                            ->relationship('parent', 'name')
                            ->searchable()
                            ->preload()
                            ->native(false),

                        ColorPicker::make('color')
                            ->label(__('point-of-sale::filament/admin/clusters/configurations/resources/category.form.sections.general.fields.color'))
                            ->hexColor(),

                        FileUpload::make('image')
                            ->label(__('point-of-sale::filament/admin/clusters/configurations/resources/category.form.sections.general.fields.image'))
                            ->image()
                            ->directory('point-of-sale/categories')
                            ->columnSpanFull(),
                    ])
                    ->columns(2),
            ]);
    }
}
