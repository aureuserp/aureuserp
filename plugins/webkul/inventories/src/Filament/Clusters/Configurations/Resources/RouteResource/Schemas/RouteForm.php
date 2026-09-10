<?php

namespace Webkul\Inventory\Filament\Clusters\Configurations\Resources\RouteResource\Schemas;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Group;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Components\Utilities\Set;
use Filament\Schemas\Schema;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Webkul\Inventory\Filament\Clusters\Configurations\Resources\WarehouseResource\Pages\ManageRoutes;
use Webkul\Inventory\Models\Warehouse;
use Webkul\Product\Settings\ProductSettings;

class RouteForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make(__('inventories::filament/clusters/configurations/resources/route.form.sections.general.title'))
                    ->schema([
                        TextInput::make('name')
                            ->label(__('inventories::filament/clusters/configurations/resources/route.form.sections.general.fields.route'))
                            ->required()
                            ->maxLength(255)
                            ->autofocus()
                            ->placeholder(__('inventories::filament/clusters/configurations/resources/route.form.sections.general.fields.route-placeholder'))
                            ->extraInputAttributes(['style' => 'font-size: 1.5rem;height: 3rem;']),
                        Select::make('company_id')
                            ->label(__('inventories::filament/clusters/configurations/resources/route.form.sections.general.fields.company'))
                            ->relationship(
                                name: 'company',
                                titleAttribute: 'name',
                                modifyQueryUsing: fn (Builder $query) => $query->withTrashed(),
                            )
                            ->getOptionLabelFromRecordUsing(
                                fn (Model $record): string => $record->name.($record->trashed() ? ' (Deleted)' : ''),
                            )
                            ->disableOptionWhen(
                                fn (string $label): bool => str_contains($label, ' (Deleted)'),
                            )
                            ->live()
                            ->afterStateUpdated(fn (Set $set, Get $get, $state) => clear_foreign_company_values($set, $get, [
                                'warehouses' => Warehouse::class,
                            ], $state))
                            ->searchable()
                            ->preload()
                            ->default(current_company_id()),
                    ]),

                Section::make(__('inventories::filament/clusters/configurations/resources/route.form.sections.applicable-on.title'))
                    ->description(__('inventories::filament/clusters/configurations/resources/route.form.sections.applicable-on.description'))
                    ->schema([
                        Group::make()
                            ->schema([
                                Toggle::make('product_category_selectable')
                                    ->label(__('inventories::filament/clusters/configurations/resources/route.form.sections.applicable-on.fields.product-categories'))
                                    ->inline(false)
                                    ->hintIcon('heroicon-m-question-mark-circle', tooltip: __('inventories::filament/clusters/configurations/resources/route.form.sections.applicable-on.fields.product-categories-hint-tooltip')),
                                Toggle::make('product_selectable')
                                    ->label(__('inventories::filament/clusters/configurations/resources/route.form.sections.applicable-on.fields.products'))
                                    ->inline(false)
                                    ->hintIcon('heroicon-m-question-mark-circle', tooltip: __('inventories::filament/clusters/configurations/resources/route.form.sections.applicable-on.fields.products-hint-tooltip')),
                                Toggle::make('packaging_selectable')
                                    ->label(__('inventories::filament/clusters/configurations/resources/route.form.sections.applicable-on.fields.packaging'))
                                    ->inline(false)
                                    ->hintIcon('heroicon-m-question-mark-circle', tooltip: __('inventories::filament/clusters/configurations/resources/route.form.sections.applicable-on.fields.packaging-hint-tooltip'))
                                    ->visible(fn (ProductSettings $settings): bool => $settings->enable_packagings),
                            ]),
                        Group::make()
                            ->schema([
                                Toggle::make('warehouse_selectable')
                                    ->label(__('inventories::filament/clusters/configurations/resources/route.form.sections.applicable-on.fields.warehouses'))
                                    ->inline(false)
                                    ->hintIcon('heroicon-m-question-mark-circle', tooltip: __('inventories::filament/clusters/configurations/resources/route.form.sections.applicable-on.fields.warehouses-hint-tooltip'))
                                    ->live(),
                                Select::make('warehouses')
                                    ->hiddenLabel()
                                    ->relationship(
                                        'warehouses',
                                        'name',
                                        modifyQueryUsing: fn (Builder $query, Get $get) => $query->where(owned_by_company($get('company_id'))),
                                    )
                                    ->searchable()
                                    ->preload()
                                    ->multiple()
                                    ->visible(fn (Get $get) => $get('warehouse_selectable')),
                            ])
                            ->hiddenOn(ManageRoutes::class),
                    ])
                    ->columns(2),
            ]);
    }
}
