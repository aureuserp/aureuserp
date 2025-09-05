<?php

namespace Webkul\Inventory\Filament\Clusters\Operations\Resources\Scraps\Schemas;

use Filament\Actions\Action;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Group;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Components\Utilities\Set;
use Filament\Schemas\Schema;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;
use Webkul\Field\Filament\Forms\Components\ProgressStepper;
use Webkul\Inventory\Enums\LocationType;
use Webkul\Inventory\Enums\ProductTracking;
use Webkul\Inventory\Enums\ScrapState;
use Webkul\Inventory\Filament\Clusters\Products\Resources\Lots\LotResource;
use Webkul\Inventory\Filament\Clusters\Products\Resources\Packages\PackageResource;
use Webkul\Inventory\Filament\Clusters\Products\Resources\Products\ProductResource;
use Webkul\Inventory\Models\Location;
use Webkul\Inventory\Models\Product;
use Webkul\Inventory\Settings\OperationSettings;
use Webkul\Inventory\Settings\ProductSettings;
use Webkul\Inventory\Settings\TraceabilitySettings;
use Webkul\Inventory\Settings\WarehouseSettings;
use Webkul\Partner\Filament\Resources\Partners\PartnerResource;
use Webkul\Product\Enums\ProductType;

class ScrapForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                ProgressStepper::make('state')
                    ->hiddenLabel()
                    ->inline()
                    ->options(ScrapState::options())
                    ->default(ScrapState::DRAFT)
                    ->disabled(),
                Section::make(__('inventories::filament/clusters/operations/resources/scrap.form.sections.general.title'))
                    ->schema([
                        Group::make()
                            ->schema([
                                Group::make()
                                    ->schema([
                                        Select::make('product_id')
                                            ->label(__('inventories::filament/clusters/operations/resources/scrap.form.sections.general.fields.product'))
                                            ->relationship(name: 'product', titleAttribute: 'name')
                                            ->relationship(
                                                'product',
                                                'name',
                                                fn ($query) => $query->where('type', ProductType::GOODS)->whereNull('is_configurable'),
                                            )
                                            ->getOptionLabelFromRecordUsing(function ($record): string {
                                                return $record->name.($record->trashed() ? ' (Deleted)' : '');
                                            })
                                            ->disableOptionWhen(function ($label) {
                                                return str_contains($label, ' (Deleted)');
                                            })
                                            ->searchable()
                                            ->preload()
                                            ->live()
                                            ->afterStateUpdated(function (Set $set, Get $get) {
                                                $set('lot_id', null);

                                                if ($product = Product::find($get('product_id'))) {
                                                    $set('uom_id', $product->uom_id);
                                                }
                                            })
                                            ->createOptionForm(fn (Schema $schema): Schema => ProductResource::form($schema))
                                            ->createOptionAction(fn ($action) => $action->modalWidth('6xl'))
                                            ->disabled(fn ($record): bool => $record?->state == ScrapState::DONE),
                                        TextInput::make('qty')
                                            ->label(__('inventories::filament/clusters/operations/resources/scrap.form.sections.general.fields.quantity'))
                                            ->required()
                                            ->numeric()
                                            ->minValue(0)
                                            ->maxValue(99999999999)
                                            ->default(0)
                                            ->disabled(fn ($record): bool => $record?->state == ScrapState::DONE),
                                        Select::make('uom_id')
                                            ->label(__('inventories::filament/clusters/operations/resources/scrap.form.sections.general.fields.unit'))
                                            ->relationship(
                                                'uom',
                                                'name',
                                                fn ($query) => $query->where('category_id', 1),
                                            )
                                            ->searchable()
                                            ->preload()
                                            ->required()
                                            ->visible(fn (ProductSettings $settings) => $settings->enable_uom)
                                            ->disabled(fn ($record): bool => $record?->state == ScrapState::DONE),
                                        Select::make('lot_id')
                                            ->label(__('inventories::filament/clusters/operations/resources/scrap.form.sections.general.fields.lot'))
                                            ->searchable()
                                            ->preload()
                                            ->required()
                                            ->relationship(
                                                name: 'lot',
                                                titleAttribute: 'name',
                                                modifyQueryUsing: fn (Builder $query, Get $get) => $query->where('product_id', $get('product_id')),
                                            )
                                            ->disabled(fn ($record): bool => $record?->state == ScrapState::DONE)
                                            ->visible(function (TraceabilitySettings $settings, Get $get): bool {
                                                if (! $settings->enable_lots_serial_numbers) {
                                                    return false;
                                                }

                                                $product = Product::find($get('product_id'));

                                                if (! $product) {
                                                    return false;
                                                }

                                                return $product->tracking === ProductTracking::LOT;
                                            })
                                            ->createOptionForm(fn (Schema $schema): Schema => LotResource::form($schema))
                                            ->createOptionAction(function (Action $action, Get $get) {
                                                $action
                                                    ->mutateDataUsing(function (array $data) use ($get): array {
                                                        $data['product_id'] = $get('product_id');

                                                        return $data;
                                                    });
                                            }),
                                        Select::make('tags')
                                            ->label(__('inventories::filament/clusters/operations/resources/scrap.form.sections.general.fields.tags'))
                                            ->relationship(name: 'tags', titleAttribute: 'name')
                                            ->multiple()
                                            ->searchable()
                                            ->preload()
                                            ->createOptionForm([
                                                TextInput::make('name')
                                                    ->label(__('inventories::filament/clusters/operations/resources/scrap.form.sections.general.fields.name'))
                                                    ->required()
                                                    ->maxLength(255)
                                                    ->unique('inventories_tags'),
                                            ]),
                                    ]),

                                Group::make()
                                    ->schema([
                                        Select::make('package_id')
                                            ->label(__('inventories::filament/clusters/operations/resources/scrap.form.sections.general.fields.package'))
                                            ->relationship('package', 'name')
                                            ->searchable()
                                            ->preload()
                                            ->createOptionForm(fn (Schema $schema): Schema => PackageResource::form($schema))
                                            ->visible(fn (OperationSettings $settings) => $settings->enable_packages)
                                            ->disabled(fn ($record): bool => $record?->state == ScrapState::DONE),
                                        Select::make('partner_id')
                                            ->label(__('inventories::filament/clusters/operations/resources/scrap.form.sections.general.fields.owner'))
                                            ->relationship('partner', 'name')
                                            ->searchable()
                                            ->preload()
                                            ->createOptionForm(fn (Schema $schema): Schema => PartnerResource::form($schema))
                                            ->disabled(fn ($record): bool => $record?->state == ScrapState::DONE),
                                        Select::make('source_location_id')
                                            ->label(__('inventories::filament/clusters/operations/resources/scrap.form.sections.general.fields.source-location'))
                                            ->relationship('sourceLocation', 'full_name')
                                            ->relationship(
                                                'sourceLocation',
                                                'full_name',
                                                fn ($query) => $query->where('type', LocationType::INTERNAL)->where('is_scrap', false),
                                            )
                                            ->required()
                                            ->searchable()
                                            ->preload()
                                            ->default(function () {
                                                $scrapLocation = Location::where('type', LocationType::INTERNAL)
                                                    ->where('is_scrap', false)
                                                    ->first();

                                                return $scrapLocation?->id;
                                            })
                                            ->visible(fn (WarehouseSettings $settings): bool => $settings->enable_locations)
                                            ->disabled(fn ($record): bool => $record?->state == ScrapState::DONE),
                                        Select::make('destination_location_id')
                                            ->label(__('inventories::filament/clusters/operations/resources/scrap.form.sections.general.fields.destination-location'))
                                            ->relationship('destinationLocation', 'full_name')
                                            ->relationship(
                                                'destinationLocation',
                                                'full_name',
                                                fn ($query) => $query->where('is_scrap', true),
                                            )
                                            ->required()
                                            ->searchable()
                                            ->preload()
                                            ->default(function () {
                                                $scrapLocation = Location::where('is_scrap', true)
                                                    ->first();

                                                return $scrapLocation?->id;
                                            })
                                            ->visible(fn (WarehouseSettings $settings): bool => $settings->enable_locations)
                                            ->disabled(fn ($record): bool => $record?->state == ScrapState::DONE),
                                        TextInput::make('origin')
                                            ->label(__('inventories::filament/clusters/operations/resources/scrap.form.sections.general.fields.source-document'))
                                            ->maxLength(255),
                                        Select::make('company_id')
                                            ->label(__('inventories::filament/clusters/operations/resources/scrap.form.sections.general.fields.company'))
                                            ->relationship('company', 'name')
                                            ->required()
                                            ->searchable()
                                            ->preload()
                                            ->default(Auth::user()->default_company_id)
                                            ->disabled(fn ($record): bool => $record?->state == ScrapState::DONE),
                                    ]),
                            ])
                            ->columns(2),
                    ]),
            ])
            ->columns(1);
    }
}
