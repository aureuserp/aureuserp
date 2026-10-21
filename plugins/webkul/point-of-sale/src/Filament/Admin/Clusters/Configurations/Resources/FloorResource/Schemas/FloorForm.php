<?php

namespace Webkul\PointOfSale\Filament\Admin\Clusters\Configurations\Resources\FloorResource\Schemas;

use Filament\Forms\Components\ColorPicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Illuminate\Database\Eloquent\Builder;

class FloorForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make(__('point-of-sale::filament/admin/clusters/configurations/resources/floor.form.sections.general.title'))
                    ->schema([
                        TextInput::make('name')
                            ->label(__('point-of-sale::filament/admin/clusters/configurations/resources/floor.form.sections.general.fields.name'))
                            ->required()
                            ->maxLength(255)
                            ->autofocus(),

                        Select::make('configs')
                            ->label(__('point-of-sale::filament/admin/clusters/configurations/resources/floor.form.sections.general.fields.configs'))
                            ->relationship(
                                'configs',
                                'name',
                                modifyQueryUsing: fn (Builder $query): Builder => $query->where('is_restaurant', true),
                            )
                            ->multiple()
                            ->searchable()
                            ->preload()
                            ->native(false)
                            ->helperText(__('point-of-sale::filament/admin/clusters/configurations/resources/floor.form.sections.general.fields.configs-helper-text')),

                        ColorPicker::make('background_color')
                            ->label(__('point-of-sale::filament/admin/clusters/configurations/resources/floor.form.sections.general.fields.background-color'))
                            ->hexColor(),

                        FileUpload::make('background_image')
                            ->label(__('point-of-sale::filament/admin/clusters/configurations/resources/floor.form.sections.general.fields.background-image'))
                            ->image()
                            ->directory('point-of-sale/floors'),
                    ])
                    ->columns(2),
            ]);
    }
}
