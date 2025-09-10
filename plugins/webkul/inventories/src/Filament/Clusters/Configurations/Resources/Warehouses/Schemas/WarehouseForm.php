<?php

namespace Webkul\Inventory\Filament\Clusters\Configurations\Resources\Warehouses\Schemas;

use Filament\Forms\Components\CheckboxList;
use Filament\Forms\Components\Radio;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Fieldset;
use Filament\Schemas\Components\Group;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Illuminate\Support\Facades\Auth;
use Webkul\Inventory\Enums\DeliveryStep;
use Webkul\Inventory\Enums\ReceptionStep;
use Webkul\Inventory\Filament\Clusters\Configurations\Resources\Warehouses\WarehouseResource;
use Webkul\Inventory\Models\Warehouse;
use Webkul\Inventory\Settings\WarehouseSettings;
use Webkul\Partner\Filament\Resources\Partners\PartnerResource;

class WarehouseForm
{
    public static function configure(Schema $schema): Schema
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
                                    ->unique(ignoreRecord: true),

                                TextInput::make('code')
                                    ->label(__('inventories::filament/clusters/configurations/resources/warehouse.form.sections.general.fields.code'))
                                    ->required()
                                    ->maxLength(255)
                                    ->placeholder(__('inventories::filament/clusters/configurations/resources/warehouse.form.sections.general.fields.code-placeholder'))
                                    ->hintIcon('heroicon-m-question-mark-circle', tooltip: __('inventories::filament/clusters/configurations/resources/warehouse.form.sections.general.fields.code-hint-tooltip'))
                                    ->unique(ignoreRecord: true),

                                Group::make()
                                    ->schema([
                                        Select::make('company_id')
                                            ->label(__('inventories::filament/clusters/configurations/resources/warehouse.form.sections.general.fields.company'))
                                            ->relationship('company', 'name')
                                            ->required()
                                            ->disabled(fn () => Auth::user()->default_company_id)
                                            ->default(Auth::user()->default_company_id),
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
                            ->visible(! empty($customFormFields = WarehouseResource::getCustomFormFields()))
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
                                    ->visible(fn (WarehouseSettings $settings): bool => $settings->enable_multi_steps_routes),

                                Fieldset::make(__('inventories::filament/clusters/configurations/resources/warehouse.form.sections.settings.fields.resupply-management'))
                                    ->schema([
                                        CheckboxList::make('supplierWarehouses')
                                            ->label(__('inventories::filament/clusters/configurations/resources/warehouse.form.sections.settings.fields.resupply-from'))
                                            ->relationship('supplierWarehouses', 'name'),
                                    ])
                                    ->visible(Warehouse::count() > 1),
                            ]),
                    ])
                    ->columnSpan(['lg' => 1])
                    ->visible(fn (WarehouseSettings $settings): bool => $settings->enable_multi_steps_routes),
            ])
            ->columns(3);
    }
}
