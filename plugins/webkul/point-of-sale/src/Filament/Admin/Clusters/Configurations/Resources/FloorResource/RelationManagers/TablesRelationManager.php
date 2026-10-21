<?php

namespace Webkul\PointOfSale\Filament\Admin\Clusters\Configurations\Resources\FloorResource\RelationManagers;

use Filament\Actions\CreateAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\ColorPicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\ColorColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Model;
use Webkul\PointOfSale\Enums\TableShape;

class TablesRelationManager extends RelationManager
{
    protected static string $relationship = 'tables';

    public static function getTitle(Model $ownerRecord, string $pageClass): string
    {
        return __('point-of-sale::filament/admin/clusters/configurations/resources/floor/relation-managers/tables.title');
    }

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('table_number')
                    ->label(__('point-of-sale::filament/admin/clusters/configurations/resources/floor/relation-managers/tables.form.fields.table-number'))
                    ->required()
                    ->maxLength(16),

                Select::make('shape')
                    ->label(__('point-of-sale::filament/admin/clusters/configurations/resources/floor/relation-managers/tables.form.fields.shape'))
                    ->options(TableShape::class)
                    ->native(false)
                    ->required(),

                TextInput::make('seats')
                    ->label(__('point-of-sale::filament/admin/clusters/configurations/resources/floor/relation-managers/tables.form.fields.seats'))
                    ->numeric()
                    ->minValue(1)
                    ->required(),

                TextInput::make('position_h')
                    ->label(__('point-of-sale::filament/admin/clusters/configurations/resources/floor/relation-managers/tables.form.fields.position-h'))
                    ->numeric()
                    ->minValue(0)
                    ->helperText(__('point-of-sale::filament/admin/clusters/configurations/resources/floor/relation-managers/tables.form.fields.position-h-helper-text')),

                TextInput::make('position_v')
                    ->label(__('point-of-sale::filament/admin/clusters/configurations/resources/floor/relation-managers/tables.form.fields.position-v'))
                    ->numeric()
                    ->minValue(0)
                    ->helperText(__('point-of-sale::filament/admin/clusters/configurations/resources/floor/relation-managers/tables.form.fields.position-v-helper-text')),

                TextInput::make('width')
                    ->label(__('point-of-sale::filament/admin/clusters/configurations/resources/floor/relation-managers/tables.form.fields.width'))
                    ->numeric()
                    ->minValue(20)
                    ->required(),

                TextInput::make('height')
                    ->label(__('point-of-sale::filament/admin/clusters/configurations/resources/floor/relation-managers/tables.form.fields.height'))
                    ->numeric()
                    ->minValue(20)
                    ->required(),

                ColorPicker::make('color')
                    ->label(__('point-of-sale::filament/admin/clusters/configurations/resources/floor/relation-managers/tables.form.fields.color')),
            ])
            ->columns(2);
    }

    public function table(Table $table): Table
    {
        return $table
            ->defaultSort('table_number')
            ->columns([
                TextColumn::make('table_number')
                    ->label(__('point-of-sale::filament/admin/clusters/configurations/resources/floor/relation-managers/tables.table.columns.table-number'))
                    ->searchable(),
                TextColumn::make('shape')
                    ->label(__('point-of-sale::filament/admin/clusters/configurations/resources/floor/relation-managers/tables.table.columns.shape'))
                    ->badge(),
                TextColumn::make('seats')
                    ->label(__('point-of-sale::filament/admin/clusters/configurations/resources/floor/relation-managers/tables.table.columns.seats'))
                    ->numeric(),
                TextColumn::make('width')
                    ->label(__('point-of-sale::filament/admin/clusters/configurations/resources/floor/relation-managers/tables.table.columns.width'))
                    ->numeric()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('height')
                    ->label(__('point-of-sale::filament/admin/clusters/configurations/resources/floor/relation-managers/tables.table.columns.height'))
                    ->numeric()
                    ->toggleable(isToggledHiddenByDefault: true),
                ColorColumn::make('color')
                    ->label(__('point-of-sale::filament/admin/clusters/configurations/resources/floor/relation-managers/tables.table.columns.color'))
                    ->placeholder('—')
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->headerActions([
                CreateAction::make(),
            ])
            ->recordActions([
                EditAction::make(),
                DeleteAction::make(),
            ]);
    }
}
