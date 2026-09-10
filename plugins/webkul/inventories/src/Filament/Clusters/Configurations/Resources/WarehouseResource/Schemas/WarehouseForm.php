<?php

namespace Webkul\Inventory\Filament\Clusters\Configurations\Resources\WarehouseResource\Schemas;

use Filament\Forms\Components\CheckboxList;
use Filament\Forms\Components\Radio;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Fieldset;
use Filament\Schemas\Components\Group;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Schema;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Validation\Rules\Unique;
use Webkul\Inventory\Enums\DeliveryStep;
use Webkul\Inventory\Enums\ManufactureStep;
use Webkul\Inventory\Enums\ReceptionStep;
use Webkul\Inventory\Filament\Clusters\Configurations\Resources\WarehouseResource;
use Webkul\Inventory\Models\Warehouse;
use Webkul\Partner\Filament\Resources\PartnerResource;
use Webkul\PluginManager\Package;

class WarehouseForm
{
    public static function configure(Schema $schema, array $customFormFields = []): Schema
    {
        return $schema
            ->components([
                Group::make()
                    ->schema([
                        Section::make(__('inventories::filament/clusters/configurations/resources/warehouse.form.sections.general.title'))
                            ->schema([
                                TextInput::make('name')
                                    ->label(__('inventories::filament/clusters/configurations/resources/warehouse.form.sections.general.fields.name'))
                                    ->required()
                                    ->maxLength(255)
                                    ->autofocus()
                                    ->placeholder(__('inventories::filament/clusters/configurations/resources/warehouse.form.sections.general.fields.name-placeholder'))
                                    ->extraInputAttributes(['style' => 'font-size: 1.5rem;height: 3rem;'])
                                    ->unique(ignoreRecord: true, modifyRuleUsing: fn (Unique $rule, Get $get): Unique => $rule->where('company_id', $get('company_id'))),

                                TextInput::make('code')
                                    ->label(__('inventories::filament/clusters/configurations/resources/warehouse.form.sections.general.fields.code'))
                                    ->required()
                                    ->maxLength(255)
                                    ->placeholder(__('inventories::filament/clusters/configurations/resources/warehouse.form.sections.general.fields.code-placeholder'))
                                    ->hintIcon('heroicon-m-question-mark-circle', tooltip: __('inventories::filament/clusters/configurations/resources/warehouse.form.sections.general.fields.code-hint-tooltip'))
                                    ->unique(ignoreRecord: true, modifyRuleUsing: fn (Unique $rule, Get $get): Unique => $rule->where('company_id', $get('company_id'))),

                                Group::make()
                                    ->schema([
                                        Select::make('company_id')
                                            ->label(__('inventories::filament/clusters/configurations/resources/warehouse.form.sections.general.fields.company'))
                                            ->relationship('company', 'name')
                                            ->required()
                                            ->disabled(fn () => current_company_id())
                                            ->default(current_company_id())
                                            ->helperText(fn (?Warehouse $record): ?string => $record || WarehouseResource::getWarehouseSettings()->enable_locations
                                                ? null
                                                : __('inventories::filament/clusters/configurations/resources/warehouse.form.sections.general.fields.multi-warehouse-warning')),
                                        Select::make('partner_address_id')
                                            ->label(__('inventories::filament/clusters/configurations/resources/warehouse.form.sections.general.fields.address'))
                                            ->relationship('partnerAddress', 'name')
                                            ->searchable()
                                            ->preload()
                                            ->createOptionForm(fn (Schema $schema): Schema => PartnerResource::form($schema)),
                                    ])
                                    ->columns(2),
                            ]),

                        Section::make(__('inventories::filament/clusters/configurations/resources/warehouse.form.sections.additional.title'))
                            ->visible(! empty($customFormFields = $customFormFields))
                            ->schema($customFormFields),
                    ])
                    ->columnSpan(['lg' => 2]),

                Group::make()
                    ->schema([
                        Section::make(__('inventories::filament/clusters/configurations/resources/warehouse.form.sections.settings.title'))
                            ->schema([
                                Fieldset::make(__('inventories::filament/clusters/configurations/resources/warehouse.form.sections.settings.fields.shipment-management'))
                                    ->schema([
                                        Radio::make('reception_steps')
                                            ->label(__('inventories::filament/clusters/configurations/resources/warehouse.form.sections.settings.fields.incoming-shipments'))
                                            ->options(ReceptionStep::class)
                                            ->default(ReceptionStep::ONE_STEP)
                                            ->hintIcon('heroicon-m-question-mark-circle', tooltip: __('inventories::filament/clusters/configurations/resources/warehouse.form.sections.settings.fields.incoming-shipments-hint-tooltip')),

                                        Radio::make('delivery_steps')
                                            ->label(__('inventories::filament/clusters/configurations/resources/warehouse.form.sections.settings.fields.outgoing-shipments'))
                                            ->options(DeliveryStep::class)
                                            ->default(DeliveryStep::ONE_STEP)
                                            ->hintIcon('heroicon-m-question-mark-circle', tooltip: __('inventories::filament/clusters/configurations/resources/warehouse.form.sections.settings.fields.outgoing-shipments-hint-tooltip')),
                                    ])
                                    ->columns(1)
                                    ->visible(WarehouseResource::getWarehouseSettings()->enable_multi_steps_routes),

                                Fieldset::make(__('inventories::filament/clusters/configurations/resources/warehouse.form.sections.settings.fields.resupply-management'))
                                    ->schema([
                                        CheckboxList::make('supplierWarehouses')
                                            ->label(__('inventories::filament/clusters/configurations/resources/warehouse.form.sections.settings.fields.resupply-from'))
                                            ->relationship(
                                                'supplierWarehouses',
                                                'name',
                                                modifyQueryUsing: fn (Builder $query, Get $get) => $query->where(owned_by_company($get('company_id'))),
                                            )
                                            ->visible(Warehouse::maxPerCompany() > 1),

                                        Radio::make('manufacture_steps')
                                            ->label(__('inventories::filament/clusters/configurations/resources/warehouse.form.sections.settings.fields.manufacture'))
                                            ->options(ManufactureStep::class)
                                            ->default(ManufactureStep::ONE_STEP)
                                            ->hintIcon('heroicon-m-question-mark-circle', tooltip: __('inventories::filament/clusters/configurations/resources/warehouse.form.sections.settings.fields.manufacture-hint-tooltip'))
                                            ->visible(Package::isPluginInstalled('manufacturing')),
                                    ])
                                    ->columns(1)
                                    ->visible(Warehouse::maxPerCompany() > 1 || Package::isPluginInstalled('manufacturing')),
                            ]),
                    ])
                    ->columnSpan(['lg' => 1])
                    ->visible(WarehouseResource::getWarehouseSettings()->enable_multi_steps_routes),
            ])
            ->columns(3);
    }
}
