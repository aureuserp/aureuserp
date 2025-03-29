<?php

namespace Webkul\Inventory\Filament\Clusters\Products\Resources;

use Filament\Forms;
use Filament\Forms\Form;
use Filament\Infolists;
use Filament\Infolists\Infolist;
use Filament\Pages\SubNavigationPosition;
use Filament\Resources\Pages\Page;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Auth;
use Webkul\Field\Filament\Traits\HasCustomFields;
use Webkul\Inventory\Enums;
use Webkul\Inventory\Filament\Clusters\Products;
use Webkul\Inventory\Filament\Clusters\Products\Resources\ProductResource\Pages;
use Webkul\Inventory\Models\Move;
use Webkul\Inventory\Models\Product;
use Webkul\Inventory\Settings\TraceabilitySettings;
use Webkul\Product\Enums\ProductType;
use Webkul\Product\Filament\Resources\ProductResource as BaseProductResource;

class ProductResource extends BaseProductResource
{
    use HasCustomFields;

    protected static ?string $model = Product::class;

    protected static ?string $navigationIcon = 'heroicon-o-shopping-bag';

    protected static bool $shouldRegisterNavigation = true;

    protected static ?string $cluster = Products::class;

    protected static ?int $navigationSort = 1;

    protected static SubNavigationPosition $subNavigationPosition = SubNavigationPosition::Top;

    protected static ?string $recordTitleAttribute = 'name';

    public static function getNavigationLabel(): string
    {
        return __('inventories::filament/clusters/products/resources/product.navigation.title');
    }

    public static function form(Form $form): Form
    {
        $form = BaseProductResource::form($form);

        $components = $form->getComponents();

        $firstGroupChildComponents = $components[0]->getChildComponents();

        $firstGroupChildComponents[2] = Forms\Components\Section::make(__('inventories::filament/clusters/products/resources/product.form.sections.inventory.title'))
            ->schema([
                Forms\Components\Fieldset::make(__('inventories::filament/clusters/products/resources/product.form.sections.inventory.fieldsets.tracking.title'))
                    ->schema([
                        Forms\Components\Toggle::make('is_storable')
                            ->label(__('inventories::filament/clusters/products/resources/product.form.sections.inventory.fieldsets.tracking.fields.track-inventory'))
                            ->default(true)
                            ->live()
                            ->afterStateUpdated(function (Forms\Set $set, Forms\Get $get) {
                                if (! $get('is_storable')) {
                                    $set('tracking', Enums\ProductTracking::QTY->value);

                                    $set('use_expiration_date', false);
                                }
                            }),
                        Forms\Components\Select::make('tracking')
                            ->label(__('inventories::filament/clusters/products/resources/product.form.sections.inventory.fieldsets.tracking.fields.track-by'))
                            ->selectablePlaceholder(false)
                            ->options(Enums\ProductTracking::class)
                            ->default(Enums\ProductTracking::QTY->value)
                            ->visible(fn (Forms\Get $get, TraceabilitySettings $settings): bool => $settings->enable_lots_serial_numbers && (bool) $get('is_storable'))
                            ->live()
                            ->afterStateUpdated(function (Forms\Set $set, Forms\Get $get) {
                                if ($get('tracking') == Enums\ProductTracking::QTY->value) {
                                    $set('use_expiration_date', false);
                                }
                            }),
                    ])
                    ->columns(1),
                Forms\Components\Fieldset::make(__('inventories::filament/clusters/products/resources/product.form.sections.inventory.fieldsets.operation.title'))
                    ->schema([
                        Forms\Components\CheckboxList::make('routes')
                            ->label(__('inventories::filament/clusters/products/resources/product.form.sections.inventory.fieldsets.operation.fields.routes'))
                            ->relationship(
                                'routes',
                                'name',
                                fn ($query) => $query->where('product_selectable', true)
                            )
                            ->hintIcon('heroicon-m-question-mark-circle', tooltip: __('inventories::filament/clusters/products/resources/product.form.sections.inventory.fieldsets.operation.fields.routes-hint-tooltip')),
                    ]),

                Forms\Components\Fieldset::make(__('inventories::filament/clusters/products/resources/product.form.sections.inventory.fieldsets.logistics.title'))
                    ->schema([
                        Forms\Components\Select::make('responsible_id')
                            ->label(__('inventories::filament/clusters/products/resources/product.form.sections.inventory.fieldsets.logistics.fields.responsible'))
                            ->relationship('responsible', 'name')
                            ->searchable()
                            ->preload()
                            ->hintIcon('heroicon-m-question-mark-circle', tooltip: __('inventories::filament/clusters/products/resources/product.form.sections.inventory.fieldsets.logistics.fields.responsible-hint-tooltip')),
                        Forms\Components\TextInput::make('weight')
                            ->label(__('inventories::filament/clusters/products/resources/product.form.sections.inventory.fieldsets.logistics.fields.weight'))
                            ->numeric()
                            ->minValue(0),
                        Forms\Components\TextInput::make('volume')
                            ->label(__('inventories::filament/clusters/products/resources/product.form.sections.inventory.fieldsets.logistics.fields.volume'))
                            ->numeric()
                            ->minValue(0),
                        Forms\Components\TextInput::make('sale_delay')
                            ->label(__('inventories::filament/clusters/products/resources/product.form.sections.inventory.fieldsets.logistics.fields.sale-delay'))
                            ->numeric()
                            ->minValue(0)
                            ->hintIcon('heroicon-m-question-mark-circle', tooltip: __('inventories::filament/clusters/products/resources/product.form.sections.inventory.fieldsets.logistics.fields.sale-delay-hint-tooltip')),
                    ]),

                Forms\Components\Fieldset::make(__('inventories::filament/clusters/products/resources/product.form.sections.inventory.fieldsets.traceability.title'))
                    ->schema([
                        Forms\Components\TextInput::make('expiration_time')
                            ->label(__('inventories::filament/clusters/products/resources/product.form.sections.inventory.fieldsets.traceability.fields.expiration-date'))
                            ->numeric()
                            ->minValue(0)
                            ->hintIcon('heroicon-m-question-mark-circle', tooltip: __('inventories::filament/clusters/products/resources/product.form.sections.inventory.fieldsets.traceability.fields.expiration-date-hint-tooltip')),
                        Forms\Components\TextInput::make('use_time')
                            ->label(__('inventories::filament/clusters/products/resources/product.form.sections.inventory.fieldsets.traceability.fields.best-before-date'))
                            ->numeric()
                            ->minValue(0)
                            ->hintIcon('heroicon-m-question-mark-circle', tooltip: __('inventories::filament/clusters/products/resources/product.form.sections.inventory.fieldsets.traceability.fields.best-before-date-hint-tooltip')),
                        Forms\Components\TextInput::make('removal_time')
                            ->label(__('inventories::filament/clusters/products/resources/product.form.sections.inventory.fieldsets.traceability.fields.removal-date'))
                            ->numeric()
                            ->minValue(0)
                            ->hintIcon('heroicon-m-question-mark-circle', tooltip: __('inventories::filament/clusters/products/resources/product.form.sections.inventory.fieldsets.traceability.fields.removal-date-hint-tooltip')),
                        Forms\Components\TextInput::make('alert_time')
                            ->label(__('inventories::filament/clusters/products/resources/product.form.sections.inventory.fieldsets.traceability.fields.alert-date'))
                            ->numeric()
                            ->minValue(0)
                            ->hintIcon('heroicon-m-question-mark-circle', tooltip: __('inventories::filament/clusters/products/resources/product.form.sections.inventory.fieldsets.traceability.fields.alert-date-hint-tooltip')),
                    ])
                    ->visible(fn (Forms\Get $get): bool => (bool) $get('use_expiration_date')),
            ])
            ->visible(fn (Forms\Get $get): bool => $get('type') == ProductType::GOODS->value);

        $firstGroupChildComponents[] = Forms\Components\Section::make(__('inventories::filament/clusters/products/resources/product.form.sections.additional.title'))
            ->visible(! empty($customFormFields = static::getCustomFormFields()))
            ->schema($customFormFields);

        $components[0]->childComponents($firstGroupChildComponents);

        $form->components($components);

        return $form;
    }

    public static function table(Table $table): Table
    {
        return BaseProductResource::table($table);
    }

    public static function infolist(Infolist $infolist): Infolist
    {
        $infolist = BaseProductResource::infolist($infolist);

        $components = $infolist->getComponents();

        $firstGroupChildComponents = $components[0]->getChildComponents();

        $firstGroupChildComponents[2] = Infolists\Components\Section::make(__('inventories::filament/clusters/products/resources/product.infolist.sections.inventory.title'))
            ->schema([
                Infolists\Components\Grid::make(3)
                    ->schema([
                        Infolists\Components\IconEntry::make('is_storable')
                            ->label(__('inventories::filament/clusters/products/resources/product.infolist.sections.inventory.fieldsets.tracking.entries.track-inventory'))
                            ->boolean(),

                        Infolists\Components\TextEntry::make('tracking')
                            ->label(__('inventories::filament/clusters/products/resources/product.infolist.sections.inventory.fieldsets.tracking.entries.track-by')),

                        Infolists\Components\IconEntry::make('use_expiration_date')
                            ->label(__('inventories::filament/clusters/products/resources/product.infolist.sections.inventory.fieldsets.tracking.entries.expiration-date'))
                            ->boolean(),
                    ]),

                Infolists\Components\Section::make(__('inventories::filament/clusters/products/resources/product.infolist.sections.inventory.fieldsets.operation.title'))
                    ->schema([
                        Infolists\Components\TextEntry::make('routes.name')
                            ->label(__('inventories::filament/clusters/products/resources/product.infolist.sections.inventory.fieldsets.operation.entries.routes'))
                            ->icon('heroicon-o-arrow-path')
                            ->listWithLineBreaks()
                            ->placeholder('—'),
                    ]),

                Infolists\Components\Section::make(__('inventories::filament/clusters/products/resources/product.infolist.sections.inventory.fieldsets.logistics.title'))
                    ->schema([
                        Infolists\Components\Grid::make(2)
                            ->schema([
                                Infolists\Components\TextEntry::make('responsible.name')
                                    ->label(__('inventories::filament/clusters/products/resources/product.infolist.sections.inventory.fieldsets.logistics.entries.responsible'))
                                    ->placeholder('—')
                                    ->icon('heroicon-o-user'),

                                Infolists\Components\TextEntry::make('weight')
                                    ->label(__('inventories::filament/clusters/products/resources/product.infolist.sections.inventory.fieldsets.logistics.entries.weight'))
                                    ->placeholder('—')
                                    ->icon('heroicon-o-scale'),

                                Infolists\Components\TextEntry::make('volume')
                                    ->label(__('inventories::filament/clusters/products/resources/product.infolist.sections.inventory.fieldsets.logistics.entries.volume'))
                                    ->placeholder('—')
                                    ->icon('heroicon-o-beaker'),

                                Infolists\Components\TextEntry::make('sale_delay')
                                    ->label(__('inventories::filament/clusters/products/resources/product.infolist.sections.inventory.fieldsets.logistics.entries.sale-delay'))
                                    ->placeholder('—'),
                            ]),
                    ]),

                Infolists\Components\Section::make(__('inventories::filament/clusters/products/resources/product.infolist.sections.inventory.fieldsets.traceability.title'))
                    ->schema([
                        Infolists\Components\Grid::make(2)
                            ->schema([
                                Infolists\Components\TextEntry::make('expiration_time')
                                    ->label(__('inventories::filament/clusters/products/resources/product.infolist.sections.inventory.fieldsets.traceability.entries.expiration-date'))
                                    ->placeholder('—')
                                    ->icon('heroicon-o-clock'),

                                Infolists\Components\TextEntry::make('use_time')
                                    ->label(__('inventories::filament/clusters/products/resources/product.infolist.sections.inventory.fieldsets.traceability.entries.best-before-date'))
                                    ->placeholder('—')
                                    ->icon('heroicon-o-clock'),

                                Infolists\Components\TextEntry::make('removal_time')
                                    ->label(__('inventories::filament/clusters/products/resources/product.infolist.sections.inventory.fieldsets.traceability.entries.removal-date'))
                                    ->placeholder('—')
                                    ->icon('heroicon-o-clock'),

                                Infolists\Components\TextEntry::make('alert_time')
                                    ->label(__('inventories::filament/clusters/products/resources/product.infolist.sections.inventory.fieldsets.traceability.entries.alert-date'))
                                    ->placeholder('—')
                                    ->icon('heroicon-o-clock'),
                            ]),
                    ])
                    ->visible(fn ($record): bool => (bool) $record->use_expiration_date),
            ])
            ->visible(fn ($record): bool => $record->type == ProductType::GOODS);

        $components[0]->childComponents($firstGroupChildComponents);

        $infolist->components($components);

        return $infolist;
    }

    public static function getRecordSubNavigation(Page $page): array
    {
        return $page->generateNavigationItems([
            Pages\ViewProduct::class,
            Pages\EditProduct::class,
            Pages\ManageAttributes::class,
            Pages\ManageVariants::class,
            Pages\ManageQuantities::class,
            Pages\ManageMoves::class,
        ]);
    }

    public static function getPages(): array
    {
        return [
            'index'      => Pages\ListProducts::route('/'),
            'create'     => Pages\CreateProduct::route('/create'),
            'view'       => Pages\ViewProduct::route('/{record}'),
            'edit'       => Pages\EditProduct::route('/{record}/edit'),
            'attributes' => Pages\ManageAttributes::route('/{record}/attributes'),
            'variants'   => Pages\ManageVariants::route('/{record}/variants'),
            'moves'      => Pages\ManageMoves::route('/{record}/moves'),
            'quantities' => Pages\ManageQuantities::route('/{record}/quantities'),
        ];
    }

    public static function createMove($record, $currentQuantity, $sourceLocationId, $destinationLocationId)
    {
        $move = Move::create([
            'name'                    => 'Product Quantity Updated',
            'state'                   => Enums\MoveState::DONE,
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
            'state'                   => Enums\MoveState::DONE,
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
