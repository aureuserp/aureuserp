<?php

namespace Webkul\Maintenance\Filament\Clusters\Configurations\Resources\EquipmentCategoryResource\Schemas;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Illuminate\Support\Facades\Auth;

class EquipmentCategoryForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make(__('maintenance::filament/clusters/configurations/resources/equipment-category.form.sections.general.title'))
                    ->schema([
                        TextInput::make('name')
                            ->label(__('maintenance::filament/clusters/configurations/resources/equipment-category.form.sections.general.fields.name'))
                            ->required()
                            ->maxLength(255)
                            ->autofocus()
                            ->unique(ignoreRecord: true),

                        Select::make('technician_user_id')
                            ->label(__('maintenance::filament/clusters/configurations/resources/equipment-category.form.sections.general.fields.technician'))
                            ->relationship('technician', 'name')
                            ->searchable()
                            ->preload()
                            ->default(Auth::id()),

                        Select::make('company_id')
                            ->label(__('maintenance::filament/clusters/configurations/resources/equipment-category.form.sections.general.fields.company'))
                            ->relationship('company', 'name')
                            ->searchable()
                            ->preload()
                            ->default(current_company_id()),

                        Textarea::make('note')
                            ->label(__('maintenance::filament/clusters/configurations/resources/equipment-category.form.sections.general.fields.note'))
                            ->rows(4)
                            ->columnSpanFull(),
                    ])
                    ->columns(2)
                    ->columnSpanFull(),
            ]);
    }
}
