<?php

namespace Webkul\Inventory\Filament\Clusters\Products\Resources;

use Filament\Forms\Components\CheckboxList;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Infolists\Components\IconEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Resources\Pages\Page;
use Filament\Schemas\Components\Fieldset;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Components\Utilities\Set;
use Filament\Schemas\Schema;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Auth;
use Webkul\Field\Filament\Traits\HasCustomFields;
use Webkul\Inventory\Enums\MoveState;
use Webkul\Inventory\Enums\ProductTracking;
use Webkul\Inventory\Filament\Clusters\Products;
use Webkul\Inventory\Filament\Clusters\Products\Resources\ProductResource\Pages\CreateProduct;
use Webkul\Inventory\Filament\Clusters\Products\Resources\ProductResource\Pages\EditProduct;
use Webkul\Inventory\Filament\Clusters\Products\Resources\ProductResource\Pages\ListProducts;
use Webkul\Inventory\Filament\Clusters\Products\Resources\ProductResource\Pages\ManageAttributes;
use Webkul\Inventory\Filament\Clusters\Products\Resources\ProductResource\Pages\ManageMoves;
use Webkul\Inventory\Filament\Clusters\Products\Resources\ProductResource\Pages\ManageQuantities;
use Webkul\Inventory\Filament\Clusters\Products\Resources\ProductResource\Pages\ManageVariants;
use Webkul\Inventory\Filament\Clusters\Products\Resources\ProductResource\Pages\ViewProduct;
use Webkul\Inventory\Models\Move;
use Webkul\Inventory\Models\Product;
use Webkul\Inventory\Settings\TraceabilitySettings;
use Webkul\Product\Enums\ProductType;
use Webkul\Product\Filament\Resources\ProductResource as BaseProductResource;

class ProductResource extends BaseProductResource
{
    use HasCustomFields;

    protected static ?string $model = Product::class;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-shopping-bag';

    protected static bool $shouldRegisterNavigation = true;

    protected static ?string $cluster = Products::class;

    protected static ?int $navigationSort = 1;

    protected static ?string $recordTitleAttribute = 'name';

    public static function getNavigationLabel(): string
    {
        return __('inventories::filament/clusters/products/resources/product.navigation.title');
    }

    public static function form(Schema $schema): Schema
    {
        $schema = BaseProductResource::form($schema);

        $components = $schema->getComponents();

        $firstGroupChildComponents = $components[0]->getDefaultChildComponents();

        $firstGroupChildComponents[2] = Section::make(__('inventories::filament/clusters/products/resources/product.form.sections.inventory.title'))
            ->schema([
                Fieldset::make(__('inventories::filament/clusters/products/resources/product.form.sections.inventory.fieldsets.tracking.title'))
                    ->schema([
                        Toggle::make('is_storable')
                            ->label(__('inventories::filament/clusters/products/resources/product.form.sections.inventory.fieldsets.tracking.fields.track-inventory'))
                            ->default(true)
                            ->live()
                            ->afterStateUpdated(function (Set $set, Get $get) {
                                if (! $get('is_storable')) {
                                    $set('tracking', ProductTracking::QTY->value);

                                    $set('use_expiration_date', false);
                                }
                            }),
                        Select::make('tracking')
                            ->label(__('inventories::filament/clusters/products/resources/product.form.sections.inventory.fieldsets.tracking.fields.track-by'))
                            ->selectablePlaceholder(false)
                            ->options(ProductTracking::class)
                            ->default(ProductTracking::QTY->value)
                            ->visible(fn (Get $get, TraceabilitySettings $settings): bool => $settings->enable_lots_serial_numbers && (bool) $get('is_storable'))
                            ->live()
                            ->afterStateUpdated(function (Set $set, Get $get) {
                                if ($get('tracking') == ProductTracking::QTY->value) {
                                    $set('use_expiration_date', false);
                                }
                            }),
                    ])
                    ->columns(1),
                Fieldset::make(__('inventories::filament/clusters/products/resources/product.form.sections.inventory.fieldsets.operation.title'))
                    ->schema([
                        CheckboxList::make('routes')
                            ->label(__('inventories::filament/clusters/products/resources/product.form.sections.inventory.fieldsets.operation.fields.routes'))
                            ->relationship(
                                'routes',
                                'name',
                                fn ($query) => $query->where('product_selectable', true)
                            )
                            ->hintIcon('heroicon-m-question-mark-circle', tooltip: __('inventories::filament/clusters/products/resources/product.form.sections.inventory.fieldsets.operation.fields.routes-hint-tooltip')),
                    ]),

                Fieldset::make(__('inventories::filament/clusters/products/resources/product.form.sections.inventory.fieldsets.logistics.title'))
                    ->schema([
                        Select::make('responsible_id')
                            ->label(__('inventories::filament/clusters/products/resources/product.form.sections.inventory.fieldsets.logistics.fields.responsible'))
                            ->relationship('responsible', 'name')
                            ->searchable()
                            ->preload()
                            ->hintIcon('heroicon-m-question-mark-circle', tooltip: __('inventories::filament/clusters/products/resources/product.form.sections.inventory.fieldsets.logistics.fields.responsible-hint-tooltip')),
                        TextInput::make('weight')
                            ->label(__('inventories::filament/clusters/products/resources/product.form.sections.inventory.fieldsets.logistics.fields.weight'))
                            ->numeric()
                            ->minValue(0)
                            ->maxValue(99999999999),
                        TextInput::make('volume')
                            ->label(__('inventories::filament/clusters/products/resources/product.form.sections.inventory.fieldsets.logistics.fields.volume'))
                            ->numeric()
                            ->minValue(0)
                            ->maxValue(99999999999),
                        TextInput::make('sale_delay')
                            ->label(__('inventories::filament/clusters/products/resources/product.form.sections.inventory.fieldsets.logistics.fields.sale-delay'))
                            ->numeric()
                            ->minValue(0)
                            ->maxValue(99999999999)
                            ->hintIcon('heroicon-m-question-mark-circle', tooltip: __('inventories::filament/clusters/products/resources/product.form.sections.inventory.fieldsets.logistics.fields.sale-delay-hint-tooltip')),
                    ]),

                Fieldset::make(__('inventories::filament/clusters/products/resources/product.form.sections.inventory.fieldsets.traceability.title'))
                    ->schema([
                        TextInput::make('expiration_time')
                            ->label(__('inventories::filament/clusters/products/resources/product.form.sections.inventory.fieldsets.traceability.fields.expiration-date'))
                            ->numeric()
                            ->minValue(0)
                            ->maxValue(99999999999)
                            ->hintIcon('heroicon-m-question-mark-circle', tooltip: __('inventories::filament/clusters/products/resources/product.form.sections.inventory.fieldsets.traceability.fields.expiration-date-hint-tooltip')),
                        TextInput::make('use_time')
                            ->label(__('inventories::filament/clusters/products/resources/product.form.sections.inventory.fieldsets.traceability.fields.best-before-date'))
                            ->numeric()
                            ->minValue(0)
                            ->maxValue(99999999999)
                            ->hintIcon('heroicon-m-question-mark-circle', tooltip: __('inventories::filament/clusters/products/resources/product.form.sections.inventory.fieldsets.traceability.fields.best-before-date-hint-tooltip')),
                        TextInput::make('removal_time')
                            ->label(__('inventories::filament/clusters/products/resources/product.form.sections.inventory.fieldsets.traceability.fields.removal-date'))
                            ->numeric()
                            ->minValue(0)
                            ->maxValue(99999999999)
                            ->hintIcon('heroicon-m-question-mark-circle', tooltip: __('inventories::filament/clusters/products/resources/product.form.sections.inventory.fieldsets.traceability.fields.removal-date-hint-tooltip')),
                        TextInput::make('alert_time')
                            ->label(__('inventories::filament/clusters/products/resources/product.form.sections.inventory.fieldsets.traceability.fields.alert-date'))
                            ->numeric()
                            ->minValue(0)
                            ->maxValue(99999999999)
                            ->hintIcon('heroicon-m-question-mark-circle', tooltip: __('inventories::filament/clusters/products/resources/product.form.sections.inventory.fieldsets.traceability.fields.alert-date-hint-tooltip')),
                    ])
                    ->visible(fn (Get $get): bool => (bool) $get('use_expiration_date')),
            ])
            ->visible(fn (Get $get): bool => $get('type') == ProductType::GOODS);

        $firstGroupChildComponents[] = Section::make(__('inventories::filament/clusters/products/resources/product.form.sections.additional.title'))
            ->visible(! empty($customFormFields = static::getCustomFormFields()))
            ->schema($customFormFields);

        $components[0]->childComponents($firstGroupChildComponents);

        $schema->components($components);

        return $schema;
    }

    public static function table(Table $table): Table
    {
        return BaseProductResource::table($table);
    }

    public static function infolist(Schema $schema): Schema
    {
        $schema = BaseProductResource::infolist($schema);

        $components = $schema->getComponents();

        $firstGroupChildComponents = $components[0]->getDefaultChildComponents();

        $firstGroupChildComponents[2] = Section::make(__('inventories::filament/clusters/products/resources/product.infolist.sections.inventory.title'))
            ->schema([
                Grid::make(3)
                    ->schema([
                        IconEntry::make('is_storable')
                            ->label(__('inventories::filament/clusters/products/resources/product.infolist.sections.inventory.fieldsets.tracking.entries.track-inventory'))
                            ->boolean(),

                        TextEntry::make('tracking')
                            ->label(__('inventories::filament/clusters/products/resources/product.infolist.sections.inventory.fieldsets.tracking.entries.track-by')),

                        IconEntry::make('use_expiration_date')
                            ->label(__('inventories::filament/clusters/products/resources/product.infolist.sections.inventory.fieldsets.tracking.entries.expiration-date'))
                            ->boolean(),
                    ]),

                Section::make(__('inventories::filament/clusters/products/resources/product.infolist.sections.inventory.fieldsets.operation.title'))
                    ->schema([
                        TextEntry::make('routes.name')
                            ->label(__('inventories::filament/clusters/products/resources/product.infolist.sections.inventory.fieldsets.operation.entries.routes'))
                            ->icon('heroicon-o-arrow-path')
                            ->listWithLineBreaks()
                            ->placeholder('—'),
                    ]),

                Section::make(__('inventories::filament/clusters/products/resources/product.infolist.sections.inventory.fieldsets.logistics.title'))
                    ->schema([
                        Grid::make(2)
                            ->schema([
                                TextEntry::make('responsible.name')
                                    ->label(__('inventories::filament/clusters/products/resources/product.infolist.sections.inventory.fieldsets.logistics.entries.responsible'))
                                    ->placeholder('—')
                                    ->icon('heroicon-o-user'),

                                TextEntry::make('weight')
                                    ->label(__('inventories::filament/clusters/products/resources/product.infolist.sections.inventory.fieldsets.logistics.entries.weight'))
                                    ->placeholder('—')
                                    ->icon('heroicon-o-scale'),

                                TextEntry::make('volume')
                                    ->label(__('inventories::filament/clusters/products/resources/product.infolist.sections.inventory.fieldsets.logistics.entries.volume'))
                                    ->placeholder('—')
                                    ->icon('heroicon-o-beaker'),

                                TextEntry::make('sale_delay')
                                    ->label(__('inventories::filament/clusters/products/resources/product.infolist.sections.inventory.fieldsets.logistics.entries.sale-delay'))
                                    ->placeholder('—'),
                            ]),
                    ]),

                Section::make(__('inventories::filament/clusters/products/resources/product.infolist.sections.inventory.fieldsets.traceability.title'))
                    ->schema([
                        Grid::make(2)
                            ->schema([
                                TextEntry::make('expiration_time')
                                    ->label(__('inventories::filament/clusters/products/resources/product.infolist.sections.inventory.fieldsets.traceability.entries.expiration-date'))
                                    ->placeholder('—')
                                    ->icon('heroicon-o-clock'),

                                TextEntry::make('use_time')
                                    ->label(__('inventories::filament/clusters/products/resources/product.infolist.sections.inventory.fieldsets.traceability.entries.best-before-date'))
                                    ->placeholder('—')
                                    ->icon('heroicon-o-clock'),

                                TextEntry::make('removal_time')
                                    ->label(__('inventories::filament/clusters/products/resources/product.infolist.sections.inventory.fieldsets.traceability.entries.removal-date'))
                                    ->placeholder('—')
                                    ->icon('heroicon-o-clock'),

                                TextEntry::make('alert_time')
                                    ->label(__('inventories::filament/clusters/products/resources/product.infolist.sections.inventory.fieldsets.traceability.entries.alert-date'))
                                    ->placeholder('—')
                                    ->icon('heroicon-o-clock'),
                            ]),
                    ])
                    ->visible(fn ($record): bool => (bool) $record->use_expiration_date),
            ])
            ->visible(fn ($record): bool => $record->type == ProductType::GOODS);

        $components[0]->childComponents($firstGroupChildComponents);

        $schema->components($components);

        return $schema;
    }

    public static function getRecordSubNavigation(Page $page): array
    {
        return $page->generateNavigationItems([
            ViewProduct::class,
            EditProduct::class,
            ManageAttributes::class,
            ManageVariants::class,
            ManageQuantities::class,
            ManageMoves::class,
        ]);
    }

    public static function getPages(): array
    {
        return [
            'index'      => ListProducts::route('/'),
            'create'     => CreateProduct::route('/create'),
            'view'       => ViewProduct::route('/{record}'),
            'edit'       => EditProduct::route('/{record}/edit'),
            'attributes' => ManageAttributes::route('/{record}/attributes'),
            'variants'   => ManageVariants::route('/{record}/variants'),
            'moves'      => ManageMoves::route('/{record}/moves'),
            'quantities' => ManageQuantities::route('/{record}/quantities'),
        ];
    }

    public static function createMove($record, $currentQuantity, $sourceLocationId, $destinationLocationId)
    {
        $move = Move::create([
            'name'                    => 'Product Quantity Updated',
            'state'                   => MoveState::DONE,
            'product_id'              => $record->product_id,
            'source_location_id'      => $sourceLocationId,
            'destination_location_id' => $destinationLocationId,
            'product_qty'             => abs($currentQuantity),
            'product_uom_qty'         => abs($currentQuantity),
            'quantity'                => abs($currentQuantity),
            'reference'               => 'Product Quantity Updated',
            'scheduled_at'            => now(),
            'uom_id'                  => $record->product->uom_id,
            'creator_id'              => Auth::id(),
            'company_id'              => $record->company_id,
        ]);

        $move->lines()->create([
            'state'                   => MoveState::DONE,
            'qty'                     => abs($currentQuantity),
            'uom_qty'                 => abs($currentQuantity),
            'is_picked'               => 1,
            'scheduled_at'            => now(),
            'operation_id'            => null,
            'product_id'              => $record->product_id,
            'result_package_id'       => $record->package_id,
            'lot_id'                  => $record->lot_id,
            'uom_id'                  => $record->product->uom_id,
            'source_location_id'      => $sourceLocationId,
            'destination_location_id' => $destinationLocationId,
            'reference'               => $move->reference,
            'company_id'              => $record->company_id,
            'creator_id'              => Auth::id(),
        ]);

        return $move;
    }
}
