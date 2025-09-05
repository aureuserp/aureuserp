<?php

namespace Webkul\Inventory\Filament\Clusters\Products\Resources\Packages\Schemas;

use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Group;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Webkul\Inventory\Filament\Clusters\Configurations\Resources\PackageTypes\PackageTypeResource;

class PackageForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make(__('inventories::filament/clusters/products/resources/package.form.sections.general.title'))
                    ->schema([
                        TextInput::make('name')
                            ->label(__('inventories::filament/clusters/products/resources/package.form.sections.general.fields.name'))
                            ->required()
                            ->maxLength(255)
                            ->autofocus()
                            ->placeholder(__('inventories::filament/clusters/products/resources/package.form.sections.general.fields.name-placeholder'))
                            ->extraInputAttributes(['style' => 'font-size: 1.5rem;height: 3rem;']),
                        Group::make()
                            ->schema([
                                Select::make('package_type_id')
                                    ->label(__('inventories::filament/clusters/products/resources/package.form.sections.general.fields.package-type'))
                                    ->relationship('packageType', 'name')
                                    ->searchable()
                                    ->preload()
                                    ->createOptionForm(fn (Schema $schema): Schema => PackageTypeResource::form($schema)),
                                DatePicker::make('pack_date')
                                    ->label(__('inventories::filament/clusters/products/resources/package.form.sections.general.fields.pack-date'))
                                    ->native(false)
                                    ->suffixIcon('heroicon-o-calendar')
                                    ->default(today()),
                                Select::make('location_id')
                                    ->label(__('inventories::filament/clusters/products/resources/package.form.sections.general.fields.location'))
                                    ->relationship('location', 'full_name')
                                    ->searchable()
                                    ->preload(),
                            ])
                            ->columns(2),
                    ])->columnSpanFull(),
            ]);
    }
}
