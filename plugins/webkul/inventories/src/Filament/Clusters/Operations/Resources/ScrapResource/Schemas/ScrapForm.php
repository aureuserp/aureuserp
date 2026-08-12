<?php

namespace Webkul\Inventory\Filament\Clusters\Operations\Resources\ScrapResource\Schemas;

use Filament\Actions\Action;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Group;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Components\Utilities\Set;
use Filament\Schemas\Schema;
use Illuminate\Database\Eloquent\Builder;
use Webkul\Field\Filament\Forms\Components\ProgressStepper as FormProgressStepper;
use Webkul\Inventory\Enums\LocationType;
use Webkul\Inventory\Enums\ProductTracking;
use Webkul\Inventory\Enums\ScrapState;
use Webkul\Inventory\Filament\Clusters\Operations\Resources\ScrapResource;
use Webkul\Inventory\Filament\Clusters\Products\Resources\LotResource;
use Webkul\Inventory\Filament\Clusters\Products\Resources\PackageResource;
use Webkul\Inventory\Filament\Clusters\Products\Resources\ProductResource;
use Webkul\Inventory\Models\Location;
use Webkul\Inventory\Models\Lot;
use Webkul\Inventory\Models\Package;
use Webkul\Inventory\Models\Product;
use Webkul\Partner\Filament\Resources\PartnerResource;
use Webkul\Product\Enums\ProductType;

class ScrapForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                FormProgressStepper::make('state')
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
                                            ->relationship(
                                                'product',
                                                'name',
                                                fn (Builder $query, Get $get) => $query
                                                    ->where('type', ProductType::GOODS)
                                                    ->whereNull('is_configurable')
                                                    ->where(owned_by_company($get('company_id'))),
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
                                            ->minValue(1)
                                            ->maxValue(99999999999)
                                            ->default(1)
                                            ->disabled(fn ($record): bool => $record?->state == ScrapState::DONE),
                                        Select::make('uom_id')
                                            ->label(__('inventories::filament/clusters/operations/resources/scrap.form.sections.general.fields.unit'))
                                            ->relationship(
                                                'uom',
                                                'name',
                                                function (Builder $query, Get $get) {
                                                    $product = Product::find($get('product_id'));
                                                    $categoryId = $product?->uom?->category_id;

                                                    return $query->when($categoryId, fn ($q) => $q->where('category_id', $categoryId))->orderBy('id');
                                                },
                                            )
                                            ->searchable()
                                            ->preload()
                                            ->required()
                                            ->native(false)
                                            ->visible(ScrapResource::getProductSettings()->enable_uom)
                                            ->disabled(fn ($record): bool => $record?->state == ScrapState::DONE),
                                        Select::make('lot_id')
                                            ->label(__('inventories::filament/clusters/operations/resources/scrap.form.sections.general.fields.lot'))
                                            ->searchable()
                                            ->preload()
                                            ->required()
                                            ->relationship(
                                                name: 'lot',
                                                titleAttribute: 'name',
                                                modifyQueryUsing: fn (Builder $query, Get $get) => $query
                                                    ->where('product_id', $get('product_id'))
                                                    ->where(owned_by_company($get('company_id'))),
                                            )
                                            ->disabled(fn ($record): bool => $record?->state == ScrapState::DONE)
                                            ->visible(function (Get $get): bool {
                                                if (! ScrapResource::getTraceabilitySettings()->enable_lots_serial_numbers) {
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
                                            ->relationship(
                                                'package',
                                                'name',
                                                modifyQueryUsing: fn (Builder $query, Get $get) => $query->where(owned_by_company($get('company_id'))),
                                            )
                                            ->searchable()
                                            ->preload()
                                            ->createOptionForm(fn (Schema $schema): Schema => PackageResource::form($schema))
                                            ->visible(ScrapResource::getOperationSettings()->enable_packages)
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
                                            ->relationship(
                                                'sourceLocation',
                                                'full_name',
                                                fn (Builder $query, Get $get) => $query
                                                    ->where('type', LocationType::INTERNAL)
                                                    ->where('is_scrap', false)
                                                    ->where(owned_by_company($get('company_id'))),
                                            )
                                            ->required()
                                            ->searchable()
                                            ->preload()
                                            ->default(fn (Get $get) => Location::where('type', LocationType::INTERNAL)
                                                ->where('is_scrap', false)
                                                ->where(owned_by_company($get('company_id')))
                                                ->value('id'))
                                            ->visible(ScrapResource::getWarehouseSettings()->enable_locations)
                                            ->disabled(fn ($record): bool => $record?->state == ScrapState::DONE),
                                        Select::make('destination_location_id')
                                            ->label(__('inventories::filament/clusters/operations/resources/scrap.form.sections.general.fields.destination-location'))
                                            ->relationship(
                                                'destinationLocation',
                                                'full_name',
                                                fn (Builder $query, Get $get) => $query
                                                    ->where('is_scrap', true)
                                                    ->where(owned_by_company($get('company_id'))),
                                            )
                                            ->required()
                                            ->searchable()
                                            ->preload()
                                            ->default(fn (Get $get) => Location::where('is_scrap', true)
                                                ->where(owned_by_company($get('company_id')))
                                                ->value('id')
                                                ?? Location::where('is_scrap', true)->value('id'))
                                            ->visible(ScrapResource::getWarehouseSettings()->enable_locations)
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
                                            ->default(current_company_id())
                                            ->live()
                                            ->afterStateUpdated(function (Set $set, Get $get, $state, $component): void {
                                                clear_foreign_company_values($set, $get, [
                                                    'product_id' => Product::class,
                                                    'lot_id'     => Lot::class,
                                                    'package_id' => Package::class,
                                                ], $state);

                                                reapply_company_defaults($component, [
                                                    'source_location_id',
                                                    'destination_location_id',
                                                ]);
                                            })
                                            ->disabled(fn ($record): bool => $record?->state == ScrapState::DONE),
                                    ]),
                            ])
                            ->columns(2),
                    ]),
            ])
            ->columns(1);
    }
}
