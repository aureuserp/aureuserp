<?php

namespace Webkul\Manufacturing\Filament\Clusters\Products\Resources\BillsOfMaterialResource\Schemas;

use BackedEnum;
use Closure;
use Filament\Actions\Action;
use Filament\Forms\Components\Checkbox;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Radio;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\FusedGroup;
use Filament\Schemas\Components\Group;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Tabs;
use Filament\Schemas\Components\Tabs\Tab;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Components\Utilities\Set;
use Filament\Schemas\Schema;
use Filament\Support\Enums\Width;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;
use Webkul\Inventory\Enums\OperationType;
use Webkul\Inventory\Models\OperationType as OperationTypeModel;
use Webkul\Manufacturing\Enums\BillOfMaterialConsumption;
use Webkul\Manufacturing\Enums\BillOfMaterialReadyToProduce;
use Webkul\Manufacturing\Enums\BillOfMaterialType;
use Webkul\Manufacturing\Enums\OperationTimeMode;
use Webkul\Manufacturing\Enums\OperationWorksheetType;
use Webkul\Manufacturing\Filament\Clusters\Configurations\Resources\OperationResource;
use Webkul\Manufacturing\Filament\Clusters\Products\Resources\BillsOfMaterialResource;
use Webkul\Manufacturing\Filament\Clusters\Products\Resources\ProductResource;
use Webkul\Manufacturing\Models\BillOfMaterial;
use Webkul\Manufacturing\Models\Operation;
use Webkul\Manufacturing\Models\Product;
use Webkul\Manufacturing\Models\WorkCenter;
use Webkul\Product\Models\ProductAttributeValue;
use Webkul\Support\Filament\Forms\Components\Repeater;
use Webkul\Support\Filament\Forms\Components\Repeater\TableColumn as RepeaterTableColumn;
use Webkul\Support\Models\Company;

class BillOfMaterialForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Group::make()
                    ->schema([
                        Section::make(__('manufacturing::filament/clusters/products/resources/bill-of-material.form.sections.general.title'))
                            ->schema([
                                Select::make('product_id')
                                    ->label(__('manufacturing::filament/clusters/products/resources/bill-of-material.form.sections.general.fields.product'))
                                    ->relationship('product', 'name', fn (Builder $query, Get $get) => $query
                                        ->withTrashed()
                                        ->whereNull('parent_id')
                                        ->where(owned_by_company($get('company_id'))))
                                    ->getOptionLabelFromRecordUsing(function ($record): string {
                                        return $record->name.($record->trashed() ? ' (Deleted)' : '');
                                    })
                                    ->wrapOptionLabels(false)
                                    ->disableOptionWhen(function ($label, $value, $state, $component) {
                                        $isDeleted = str_contains($label, ' (Deleted)');

                                        $isDuplicate = false;

                                        if ($component?->getParentRepeater()) {
                                            $repeater = $component->getParentRepeater();

                                            $isDuplicate = collect($repeater->getState())
                                                ->pluck(
                                                    (string) str($component->getStatePath())
                                                        ->after("{$repeater->getStatePath()}.")
                                                        ->after('.'),
                                                )
                                                ->flatten()
                                                ->diff(Arr::wrap($state))
                                                ->filter(fn (mixed $siblingItemState): bool => filled($siblingItemState))
                                                ->contains($value);
                                        }

                                        return $isDeleted || $isDuplicate;
                                    })
                                    ->searchable()
                                    ->preload()
                                    ->live()
                                    ->afterStateHydrated(function (Set $set, ?string $state): void {
                                        $product = Product::query()->find($state);

                                        if (! $product?->parent_id) {
                                            return;
                                        }

                                        $set('product_variant_id', $product->id);
                                        $set('product_id', $product->parent_id);
                                    })
                                    ->afterStateUpdated(function (Set $set, ?string $state): void {
                                        $product = Product::query()->find($state);

                                        if (! $product) {
                                            return;
                                        }

                                        $set('product_variant_id', null);
                                        $set('uom_id', $product->uom_id);
                                        $set('company_id', $product->company_id);
                                    })
                                    ->required()
                                    ->columnSpanFull(),
                                Select::make('product_variant_id')
                                    ->label(__('manufacturing::filament/clusters/products/resources/bill-of-material.form.sections.general.fields.product-variant'))
                                    ->options(fn (Get $get): array => Product::query()
                                        ->where('parent_id', $get('product_id'))
                                        ->orderBy('name')
                                        ->pluck('name', 'id')
                                        ->all())
                                    ->native(false)
                                    ->live()
                                    ->dehydrated(false)
                                    ->afterStateUpdated(function (Set $set, ?string $state): void {
                                        $variant = Product::query()->find($state);

                                        if (! $variant) {
                                            return;
                                        }

                                        $set('uom_id', $variant->uom_id);
                                        $set('company_id', $variant->company_id);
                                    })
                                    ->columnSpanFull(),
                                FusedGroup::make([
                                    TextInput::make('quantity')
                                        ->numeric()
                                        ->minValue(0.0001)
                                        ->default(1)
                                        ->step('0.0001')
                                        ->required()
                                        ->columnSpan(2),
                                    Select::make('uom_id')
                                        ->placeholder(__('manufacturing::filament/clusters/products/resources/bill-of-material.form.sections.general.fields.uom'))
                                        ->relationship(
                                            'uom',
                                            'name',
                                            function (Builder $query, Get $get): Builder {
                                                $product = Product::query()->withTrashed()->find($get('product_id'));
                                                $categoryId = $product?->uom?->category_id;

                                                return $query
                                                    ->when($categoryId, fn ($q) => $q->where('category_id', $categoryId))
                                                    ->orderBy('name');
                                            },
                                        )
                                        ->default(fn (Get $get): ?int => Product::query()->withTrashed()->find($get('product_id'))?->uom_id)
                                        ->searchable()
                                        ->preload()
                                        ->required(),
                                ])
                                    ->label(__('manufacturing::filament/clusters/products/resources/bill-of-material.form.sections.general.fields.quantity'))
                                    ->columns(3),
                                TextInput::make('code')
                                    ->label(__('manufacturing::filament/clusters/products/resources/bill-of-material.form.sections.general.fields.reference'))
                                    ->maxLength(255)
                                    ->placeholder(__('manufacturing::filament/clusters/products/resources/bill-of-material.form.sections.general.fields.reference-placeholder')),
                                Radio::make('type')
                                    ->label(__('manufacturing::filament/clusters/products/resources/bill-of-material.form.sections.general.fields.type'))
                                    ->hidden()
                                    ->options(BillOfMaterialType::class)
                                    ->default(BillOfMaterialType::NORMAL->value)
                                    ->live()
                                    ->inline(false)
                                    ->required(),
                                Select::make('company_id')
                                    ->label(__('manufacturing::filament/clusters/products/resources/bill-of-material.form.sections.general.fields.company'))
                                    ->relationship('company', 'name')
                                    ->searchable()
                                    ->preload()
                                    ->default(current_company_id())
                                    ->live()
                                    ->afterStateUpdated(fn (Set $set, Get $get, $state) => clear_foreign_company_values($set, $get, [
                                        'product_id'        => Product::class,
                                        'operation_type_id' => OperationTypeModel::class,
                                    ], $state))
                                    ->required(),
                            ])
                            ->columns(2),

                        Tabs::make('bom-tabs')
                            ->tabs([
                                Tab::make(__('manufacturing::filament/clusters/products/resources/bill-of-material.form.tabs.components.title'))
                                    ->schema([
                                        static::getComponentsRepeater(),
                                    ]),

                                Tab::make(__('manufacturing::filament/clusters/products/resources/bill-of-material.form.tabs.operations.title'))
                                    ->visible(BillsOfMaterialResource::getOperationSettings()->enable_work_orders)
                                    ->schema([
                                        static::getOperationsRepeater(),
                                    ]),

                                Tab::make(__('manufacturing::filament/clusters/products/resources/bill-of-material.form.tabs.by-products.title'))
                                    ->hidden(! BillsOfMaterialResource::getOperationSettings()->enable_byproducts)
                                    ->schema([
                                        static::getByProductsRepeater(),
                                    ]),
                            ]),
                    ])
                    ->columnSpan(['lg' => 2]),

                Group::make()
                    ->schema([
                        Section::make(__('manufacturing::filament/clusters/products/resources/bill-of-material.form.sections.miscellaneous.title'))
                            ->schema([
                                TextEntry::make('kit_information')
                                    ->label(__('manufacturing::filament/clusters/products/resources/bill-of-material.form.sections.miscellaneous.fields.kit-information'))
                                    ->state(__('manufacturing::filament/clusters/products/resources/bill-of-material.form.sections.miscellaneous.fields.kit-information-content'))
                                    ->columnSpanFull()
                                    ->visible(fn (Get $get): bool => static::matchesEnumState($get('type'), BillOfMaterialType::PHANTOM)),
                                Radio::make('ready_to_produce')
                                    ->label(__('manufacturing::filament/clusters/products/resources/bill-of-material.form.tabs.miscellaneous.fields.ready-to-produce'))
                                    ->options(BillOfMaterialReadyToProduce::class)
                                    ->default(BillOfMaterialReadyToProduce::ALL_AVAILABLE->value)
                                    ->inline(false)
                                    ->visible(fn (Get $get): bool => ! static::matchesEnumState($get('type'), BillOfMaterialType::PHANTOM))
                                    ->required()
                                    ->columnSpanFull(),
                                Select::make('operation_type_id')
                                    ->label(__('manufacturing::filament/clusters/products/resources/bill-of-material.form.tabs.miscellaneous.fields.routing'))
                                    ->relationship('operationType', 'name', fn (Builder $query, Get $get) => $query
                                        ->withTrashed()
                                        ->where('type', OperationType::MANUFACTURE)
                                        ->where(owned_by_company($get('company_id'))))
                                    ->searchable()
                                    ->preload()
                                    ->native(false)
                                    ->wrapOptionLabels(false)
                                    ->getOptionLabelFromRecordUsing(fn ($record): string => $record->warehouse
                                        ? $record->warehouse->name.': '.$record->name
                                        : $record->name)
                                    ->visible(fn (Get $get): bool => ! static::matchesEnumState($get('type'), BillOfMaterialType::PHANTOM))
                                    ->columnSpanFull(),
                                Select::make('consumption')
                                    ->label(__('manufacturing::filament/clusters/products/resources/bill-of-material.form.tabs.miscellaneous.fields.consumption'))
                                    ->options(BillOfMaterialConsumption::class)
                                    ->native(false)
                                    ->default(BillOfMaterialConsumption::WARNING->value)
                                    ->visible(fn (Get $get): bool => ! static::matchesEnumState($get('type'), BillOfMaterialType::PHANTOM))
                                    ->required()
                                    ->columnSpanFull(),
                                Toggle::make('allow_operation_dependencies')
                                    ->label(__('manufacturing::filament/clusters/products/resources/bill-of-material.form.tabs.miscellaneous.fields.operation-dependencies'))
                                    ->default(false)
                                    ->inline(false)
                                    ->columnSpanFull(),
                                TextInput::make('produce_delay')
                                    ->label(__('manufacturing::filament/clusters/products/resources/bill-of-material.form.sections.miscellaneous.fields.manufacturing-lead-time'))
                                    ->numeric()
                                    ->default(0)
                                    ->minValue(0)
                                    ->suffix(__('manufacturing::filament/clusters/products/resources/bill-of-material.form.sections.miscellaneous.fields.days-suffix'))
                                    ->required()
                                    ->columnSpanFull(),
                                TextInput::make('days_to_prepare_mo')
                                    ->label(__('manufacturing::filament/clusters/products/resources/bill-of-material.form.sections.miscellaneous.fields.days-to-prepare-manufacturing-order'))
                                    ->numeric()
                                    ->default(0)
                                    ->minValue(0)
                                    ->suffix(__('manufacturing::filament/clusters/products/resources/bill-of-material.form.sections.miscellaneous.fields.days-suffix'))
                                    ->required()
                                    ->columnSpanFull(),
                            ])
                            ->columns(1),
                    ])
                    ->columnSpan(['lg' => 1]),

            ])
            ->columns(3);
    }

    private static function getSelectedBillOfMaterialProductId(Get $get): mixed
    {
        return $get('../../product_variant_id') ?: $get('../../product_id');
    }

    private static function isSelectedBillOfMaterialProduct(Get $get, mixed $productId): bool
    {
        $billOfMaterialProductId = static::getSelectedBillOfMaterialProductId($get);

        return filled($productId)
            && filled($billOfMaterialProductId)
            && (string) $productId === (string) $billOfMaterialProductId;
    }

    private static function notBillOfMaterialProductRule(Get $get): Closure
    {
        return function (string $attribute, mixed $value, Closure $fail) use ($get): void {
            if (static::isSelectedBillOfMaterialProduct($get, $value)) {
                $fail(__(
                    'manufacturing::filament/clusters/products/resources/bill-of-material.form.tabs.components.validation.component-different-from-product',
                ));
            }
        };
    }

    private static function getComponentsRepeater(): Repeater
    {
        return Repeater::make('lines')
            ->relationship('lines')
            ->hiddenLabel()
            ->defaultItems(0)
            ->compact()
            ->addActionLabel(__('manufacturing::filament/clusters/products/resources/bill-of-material.form.tabs.components.add-action'))
            ->table([
                RepeaterTableColumn::make('product_id')
                    ->label(__('manufacturing::filament/clusters/products/resources/bill-of-material.form.tabs.components.columns.component'))
                    ->markAsRequired()
                    ->resizable(),
                RepeaterTableColumn::make('quantity')
                    ->label(__('manufacturing::filament/clusters/products/resources/bill-of-material.form.tabs.components.columns.quantity'))
                    ->markAsRequired()
                    ->resizable(),
                RepeaterTableColumn::make('uom_id')
                    ->label(__('manufacturing::filament/clusters/products/resources/bill-of-material.form.tabs.components.columns.uom'))
                    ->markAsRequired()
                    ->resizable(),
                RepeaterTableColumn::make('attributeValues')
                    ->label(__('manufacturing::filament/clusters/products/resources/bill-of-material.form.tabs.components.columns.apply-on-variants'))
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->resizable(),
                RepeaterTableColumn::make('operation_id')
                    ->label(__('manufacturing::filament/clusters/products/resources/bill-of-material.form.tabs.components.columns.consumed-in-operation'))
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->resizable(),
                RepeaterTableColumn::make('is_manual_consumption')
                    ->label(__('manufacturing::filament/clusters/products/resources/bill-of-material.form.tabs.components.columns.highlight-consumption'))
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->resizable(),
            ])
            ->schema([
                Hidden::make('company_id'),
                Select::make('product_id')
                    ->relationship('product', 'name', fn (Builder $query, Get $get) => $query
                        ->withTrashed()
                        ->where(function (Builder $productQuery): void {
                            $productQuery
                                ->where('is_configurable', false)
                                ->orWhereNull('is_configurable');
                        })
                        ->where(owned_by_company($get('../../company_id'))))
                    ->searchable()
                    ->preload()
                    ->required()
                    ->createOptionForm(fn (Schema $schema): Schema => ProductResource::form($schema))
                    ->createOptionAction(fn (Action $action) => $action->modalWidth(Width::SevenExtraLarge))
                    ->live()
                    ->getOptionLabelFromRecordUsing(function ($record): string {
                        return $record->name.($record->trashed() ? ' (Deleted)' : '');
                    })
                    ->wrapOptionLabels(false)
                    ->disableOptionWhen(function ($label, $value, $state, $component, Get $get) {
                        $isDeleted = str_contains($label, ' (Deleted)');

                        $isDuplicate = false;

                        if ($component?->getParentRepeater()) {
                            $repeater = $component->getParentRepeater();

                            $isDuplicate = collect($repeater->getState())
                                ->pluck(
                                    (string) str($component->getStatePath())
                                        ->after("{$repeater->getStatePath()}.")
                                        ->after('.'),
                                )
                                ->flatten()
                                ->diff(Arr::wrap($state))
                                ->filter(fn (mixed $siblingItemState): bool => filled($siblingItemState))
                                ->contains($value);
                        }

                        return $isDeleted
                            || $isDuplicate
                            || static::isSelectedBillOfMaterialProduct($get, $value);
                    })
                    ->rules([
                        fn (Get $get): Closure => static::notBillOfMaterialProductRule($get),
                    ])
                    ->afterStateUpdated(function (Set $set, Get $get, ?string $state): void {
                        $product = Product::query()->find($state);

                        if (! $product) {
                            return;
                        }

                        $set('uom_id', $product->uom_id);
                        $set('company_id', $get('../../company_id'));
                    }),
                TextInput::make('quantity')
                    ->numeric()
                    ->minValue(0.0001)
                    ->default(1)
                    ->step('0.0001')
                    ->required(),
                Select::make('uom_id')
                    ->relationship(
                        'uom',
                        'name',
                        function (Builder $query, Get $get): Builder {
                            $product = Product::query()->withTrashed()->find($get('product_id'));
                            $categoryId = $product?->uom?->category_id;

                            return $query
                                ->when($categoryId, fn ($q) => $q->where('category_id', $categoryId))
                                ->orderBy('name');
                        },
                    )
                    ->default(fn (Get $get): ?int => Product::query()->withTrashed()->find($get('product_id'))?->uom_id)
                    ->searchable()
                    ->preload()
                    ->required(),
                Select::make('attributeValues')
                    ->label(__('manufacturing::filament/clusters/products/resources/bill-of-material.form.tabs.components.columns.apply-on-variants'))
                    ->relationship(
                        name: 'attributeValues',
                        titleAttribute: 'id',
                        modifyQueryUsing: function (Builder $query, Get $get) {
                            $bomProductId = $get('../../product_id');

                            return $query->where('product_id', $bomProductId);
                        },
                    )
                    ->getOptionLabelFromRecordUsing(fn ($record): string => $record->attribute?->name && $record->attributeOption?->name ? "{$record->attribute->name}: {$record->attributeOption->name}" : ($record->attributeOption?->name ?? (string) $record->id))
                    ->searchable()
                    ->preload()
                    ->multiple(),
                Select::make('operation_id')
                    ->relationship('operation', 'name')
                    ->native(false)
                    ->searchable()
                    ->preload()
                    ->wrapOptionLabels(false)
                    ->createOptionForm(fn (Schema $schema): Schema => OperationResource::form($schema))
                    ->createOptionAction(fn (Action $action) => $action->modalWidth(Width::SevenExtraLarge)),
                Checkbox::make('is_manual_consumption')
                    ->label(__('manufacturing::filament/clusters/products/resources/bill-of-material.form.tabs.components.columns.highlight-consumption'))
                    ->default(false),
            ]);
    }

    private static function getOperationsRepeater(): Repeater
    {
        return Repeater::make('operations')
            ->relationship('operations')
            ->hiddenLabel()
            ->defaultItems(0)
            ->compact()
            ->addActionLabel(__('manufacturing::filament/clusters/products/resources/bill-of-material.form.tabs.operations.add-action'))
            ->addAction(function (Action $action): Action {
                return $action
                    ->schema(fn (Schema $schema): Schema => OperationResource::form($schema->model(Operation::class)))
                    ->modalWidth(Width::SevenExtraLarge)
                    ->fillForm(function (Get $get): array {
                        return static::getDefaultOperationRepeaterData([
                            'bill_of_material_id' => $get('../../id'),
                            'company_id'          => $get('../../company_id'),
                        ]);
                    })
                    ->action(function (array $data, Repeater $component): void {
                        $state = $component->getState() ?? [];
                        $state[(string) Str::uuid()] = static::normalizeOperationRepeaterData($data);

                        $component->state($state);
                    });
            })
            ->footerActions([
                Action::make('copyExistingOperation')
                    ->label(__('manufacturing::filament/clusters/products/resources/bill-of-material.form.tabs.operations.actions.copy-existing'))
                    ->link()
                    ->schema([
                        Select::make('operation_id')
                            ->label(__('manufacturing::filament/clusters/products/resources/bill-of-material.form.tabs.operations.actions.copy-existing-fields.operation'))
                            ->options(fn (): array => Operation::query()
                                ->with(['billOfMaterial.product', 'workCenter'])
                                ->orderBy('name')
                                ->get()
                                ->mapWithKeys(function (Operation $operation): array {
                                    $billOfMaterialLabel = static::getBillOfMaterialLabel($operation->billOfMaterial);
                                    $workCenterName = $operation->workCenter?->name;

                                    $labelParts = array_filter([
                                        $operation->name,
                                        $workCenterName,
                                        $billOfMaterialLabel !== '—' ? $billOfMaterialLabel : null,
                                    ]);

                                    return [$operation->id => implode(' / ', $labelParts)];
                                })
                                ->all())
                            ->native(false)
                            ->searchable()
                            ->preload()
                            ->wrapOptionLabels(false)
                            ->required(),
                    ])
                    ->modalWidth(Width::ThreeExtraLarge)
                    ->action(function (array $data, Repeater $component, Get $get): void {
                        $operation = Operation::query()
                            ->with(['attributeValues'])
                            ->find($data['operation_id']);

                        if (! $operation) {
                            return;
                        }

                        $state = $component->getState() ?? [];
                        $state[(string) Str::uuid()] = static::normalizeOperationRepeaterData([
                            'bill_of_material_id'        => $get('../../id'),
                            'company_id'                 => $get('../../company_id') ?? $operation->company_id,
                            'name'                       => $operation->name,
                            'work_center_id'             => $operation->work_center_id,
                            'time_mode'                  => $operation->time_mode?->value ?? $operation->time_mode,
                            'time_mode_batch'            => $operation->time_mode_batch,
                            'manual_cycle_time'          => (string) $operation->manual_cycle_time,
                            'worksheet_type'             => $operation->worksheet_type?->value ?? $operation->worksheet_type,
                            'worksheet'                  => $operation->worksheet,
                            'worksheet_google_slide_url' => $operation->worksheet_google_slide_url,
                            'note'                       => $operation->note,
                            'attributeValues'            => $operation->attributeValues->pluck('id')->all(),
                        ]);

                        $component->state($state);
                    }),
            ])
            ->table([
                RepeaterTableColumn::make('display_name')
                    ->label(__('manufacturing::filament/clusters/products/resources/bill-of-material.form.tabs.operations.columns.operation'))
                    ->markAsRequired()
                    ->resizable(),
                RepeaterTableColumn::make('display_work_center_id')
                    ->label(__('manufacturing::filament/clusters/products/resources/bill-of-material.form.tabs.operations.columns.work-center'))
                    ->markAsRequired()
                    ->resizable(),
                RepeaterTableColumn::make('display_time_mode')
                    ->label(__('manufacturing::filament/clusters/products/resources/bill-of-material.form.tabs.operations.columns.time-mode'))
                    ->markAsRequired()
                    ->resizable(),
                RepeaterTableColumn::make('display_time_mode_batch')
                    ->label(__('manufacturing::filament/clusters/products/resources/bill-of-material.form.tabs.operations.columns.time-mode-batch'))
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->resizable(),
                RepeaterTableColumn::make('display_company')
                    ->label(__('manufacturing::filament/clusters/products/resources/bill-of-material.form.tabs.operations.columns.company'))
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->resizable(),
                RepeaterTableColumn::make('display_attribute_values')
                    ->label(__('manufacturing::filament/clusters/products/resources/bill-of-material.form.tabs.operations.columns.apply-on-variants'))
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->resizable(),
                RepeaterTableColumn::make('display_manual_cycle_time')
                    ->label(__('manufacturing::filament/clusters/products/resources/bill-of-material.form.tabs.operations.columns.duration'))
                    ->markAsRequired()
                    ->resizable(),
            ])
            ->schema([
                Hidden::make('bill_of_material_id'),
                Hidden::make('company_id'),
                Hidden::make('name'),
                Hidden::make('work_center_id'),
                Hidden::make('time_mode'),
                Hidden::make('time_mode_batch'),
                Hidden::make('manual_cycle_time'),
                Hidden::make('worksheet_type'),
                Hidden::make('worksheet'),
                Hidden::make('worksheet_google_slide_url'),
                Hidden::make('note'),
                TextEntry::make('display_name')
                    ->hiddenLabel()
                    ->state(fn (Get $get): string => (string) ($get('name') ?? '—')),
                TextEntry::make('display_work_center_id')
                    ->hiddenLabel()
                    ->state(function (Get $get): string {
                        return WorkCenter::query()->find($get('work_center_id'))?->name ?? '—';
                    }),
                TextEntry::make('display_time_mode')
                    ->hiddenLabel()
                    ->state(function (Get $get): string {
                        $state = $get('time_mode');

                        if ($state instanceof OperationTimeMode) {
                            return $state->getLabel();
                        }

                        return OperationTimeMode::tryFrom((string) $state)?->getLabel() ?? '—';
                    }),
                TextEntry::make('display_time_mode_batch')
                    ->hiddenLabel()
                    ->state(fn (Get $get): string => filled($get('time_mode_batch')) ? (string) $get('time_mode_batch') : '—'),
                TextEntry::make('display_company')
                    ->label(__('manufacturing::filament/clusters/products/resources/bill-of-material.form.tabs.operations.columns.company'))
                    ->state(function (Get $get): string {
                        $companyId = $get('../../company_id');

                        return Company::query()->find($companyId)?->name ?? '—';
                    }),
                Select::make('attributeValues')
                    ->label(__('manufacturing::filament/clusters/products/resources/bill-of-material.form.tabs.operations.columns.apply-on-variants'))
                    ->native(false)
                    ->wrapOptionLabels(false)
                    ->hidden()
                    ->relationship(
                        'attributeValues',
                        'id',
                        modifyQueryUsing: function (Get $get, Builder $query): void {
                            $productId = $get('../../product_id');

                            if (! $productId) {
                                $query->whereRaw('1 = 0');

                                return;
                            }

                            $query->where('product_id', $productId);
                        }
                    )
                    ->getOptionLabelFromRecordUsing(fn ($record): string => $record->attribute?->name && $record->attributeOption?->name ? "{$record->attribute->name}: {$record->attributeOption->name}" : ($record->attributeOption?->name ?? (string) $record->id))
                    ->searchable()
                    ->preload()
                    ->multiple(),
                TextEntry::make('display_attribute_values')
                    ->hiddenLabel()
                    ->state(function (Get $get): string {
                        $attributeValueIds = $get('attributeValues') ?? [];

                        if (! is_array($attributeValueIds) || $attributeValueIds === []) {
                            return '—';
                        }

                        return ProductAttributeValue::query()
                            ->with(['attribute', 'attributeOption'])
                            ->whereIn('id', $attributeValueIds)
                            ->get()
                            ->map(fn ($record): string => $record->attribute?->name && $record->attributeOption?->name ? "{$record->attribute->name}: {$record->attributeOption->name}" : ($record->attributeOption?->name ?? (string) $record->id))
                            ->implode(', ');
                    }),
                TextEntry::make('display_manual_cycle_time')
                    ->hiddenLabel()
                    ->state(function (Get $get): string {
                        return format_float_time($get('manual_cycle_time') ?? 60, 'minutes');
                    }),
            ])
            ->mutateRelationshipDataBeforeCreateUsing(fn (array $data): array => static::normalizeOperationRepeaterData($data))
            ->mutateRelationshipDataBeforeSaveUsing(fn (array $data): array => static::normalizeOperationRepeaterData($data))
            ->extraItemActions([
                Action::make('editOperation')
                    ->icon('heroicon-o-pencil-square')
                    ->label(__('manufacturing::filament/clusters/products/resources/bill-of-material.form.tabs.operations.actions.edit'))
                    ->tooltip(__('manufacturing::filament/clusters/products/resources/bill-of-material.form.tabs.operations.actions.edit'))
                    ->extraAttributes(['data-row-click-action' => 'true'])
                    ->schema(fn (Schema $schema): Schema => OperationResource::form($schema->model(Operation::class)))
                    ->modalWidth(Width::SevenExtraLarge)
                    ->fillForm(function (array $arguments, Repeater $component, Get $get): array {
                        return array_replace(
                            static::getDefaultOperationRepeaterData([
                                'bill_of_material_id' => $get('../../id'),
                                'company_id'          => $get('../../company_id'),
                            ]),
                            static::normalizeOperationRepeaterData(
                                $component->getRawItemState($arguments['item'])
                            ),
                        );
                    })
                    ->action(function (array $arguments, array $data, Repeater $component): void {
                        $state = $component->getState() ?? [];
                        $state[$arguments['item']] = static::normalizeOperationRepeaterData($data);

                        $component->state($state);
                    }),
            ]);
    }

    private static function getByProductsRepeater(): Repeater
    {
        return Repeater::make('byproducts')
            ->relationship('byproducts')
            ->hiddenLabel()
            ->defaultItems(0)
            ->compact()
            ->addActionLabel(__('manufacturing::filament/clusters/products/resources/bill-of-material.form.tabs.by-products.add-action'))
            ->table([
                RepeaterTableColumn::make('product_id')
                    ->label(__('manufacturing::filament/clusters/products/resources/bill-of-material.form.tabs.by-products.columns.product'))
                    ->markAsRequired()
                    ->resizable(),
                RepeaterTableColumn::make('quantity')
                    ->label(__('manufacturing::filament/clusters/products/resources/bill-of-material.form.tabs.by-products.columns.quantity'))
                    ->markAsRequired()
                    ->resizable(),
                RepeaterTableColumn::make('uom_id')
                    ->label(__('manufacturing::filament/clusters/products/resources/bill-of-material.form.tabs.by-products.columns.uom'))
                    ->markAsRequired()
                    ->resizable(),
                RepeaterTableColumn::make('operation_id')
                    ->label(__('manufacturing::filament/clusters/products/resources/bill-of-material.form.tabs.by-products.columns.operation'))
                    ->resizable(),
            ])
            ->schema([
                Hidden::make('company_id'),
                Select::make('product_id')
                    ->relationship('product', 'name', fn (Builder $query, Get $get) => $query
                        ->withTrashed()
                        ->where(owned_by_company($get('../../company_id'))))
                    ->searchable()
                    ->preload()
                    ->required()
                    ->live()
                    ->getOptionLabelFromRecordUsing(function ($record): string {
                        return $record->name.($record->trashed() ? ' (Deleted)' : '');
                    })
                    ->wrapOptionLabels(false)
                    ->disableOptionWhen(function ($label, $value, $state, $component) {
                        $isDeleted = str_contains($label, ' (Deleted)');

                        $isDuplicate = false;

                        if ($component?->getParentRepeater()) {
                            $repeater = $component->getParentRepeater();

                            $isDuplicate = collect($repeater->getState())
                                ->pluck(
                                    (string) str($component->getStatePath())
                                        ->after("{$repeater->getStatePath()}.")
                                        ->after('.'),
                                )
                                ->flatten()
                                ->diff(Arr::wrap($state))
                                ->filter(fn (mixed $siblingItemState): bool => filled($siblingItemState))
                                ->contains($value);
                        }

                        return $isDeleted || $isDuplicate;
                    })
                    ->afterStateUpdated(function (Set $set, Get $get, ?string $state): void {
                        $product = Product::query()->find($state);

                        if (! $product) {
                            return;
                        }

                        $set('uom_id', $product->uom_id);
                        $set('company_id', $get('../../company_id'));
                    }),
                TextInput::make('quantity')
                    ->numeric()
                    ->minValue(0.0001)
                    ->default(1)
                    ->step('0.0001')
                    ->required(),
                Select::make('uom_id')
                    ->relationship(
                        'uom',
                        'name',
                        function (Builder $query, Get $get): Builder {
                            $product = Product::query()->withTrashed()->find($get('product_id'));
                            $categoryId = $product?->uom?->category_id;

                            return $query
                                ->when($categoryId, fn ($q) => $q->where('category_id', $categoryId))
                                ->orderBy('name');
                        },
                    )
                    ->default(fn (Get $get): ?int => Product::query()->withTrashed()->find($get('product_id'))?->uom_id)
                    ->searchable()
                    ->preload()
                    ->required(),
                Select::make('operation_id')
                    ->relationship('operation', 'name')
                    ->native(false)
                    ->searchable()
                    ->preload()
                    ->wrapOptionLabels(false)
                    ->createOptionForm(fn (Schema $schema): Schema => OperationResource::form($schema))
                    ->createOptionAction(fn (Action $action) => $action->modalWidth(Width::SevenExtraLarge)),
            ]);
    }

    private static function getBillOfMaterialLabel(?BillOfMaterial $billOfMaterial): string
    {
        if (! $billOfMaterial) {
            return '—';
        }

        return filled($billOfMaterial->code)
            ? (string) $billOfMaterial->code
            : ($billOfMaterial->product?->name ?? '—');
    }

    private static function getDefaultOperationRepeaterData(array $overrides = []): array
    {
        return static::normalizeOperationRepeaterData([
            'bill_of_material_id'        => null,
            'time_mode'                  => OperationTimeMode::MANUAL->value,
            'time_mode_batch'            => 10,
            'manual_cycle_time'          => '60:00',
            'worksheet_type'             => OperationWorksheetType::TEXT->value,
            'worksheet'                  => null,
            'worksheet_google_slide_url' => null,
            'note'                       => null,
            'company_id'                 => current_company_id(),
            'attributeValues'            => [],
            ...$overrides,
        ]);
    }

    private static function normalizeOperationRepeaterData(array $data): array
    {
        $data['company_id'] ??= current_company_id();
        $data['attributeValues'] = array_values($data['attributeValues'] ?? []);
        $data['time_mode'] ??= OperationTimeMode::MANUAL->value;
        $data['time_mode_batch'] ??= 10;
        $data['manual_cycle_time'] ??= '60';

        return $data;
    }

    private static function matchesEnumState(mixed $state, BackedEnum $enum): bool
    {
        if ($state instanceof BackedEnum) {
            return $state->value === $enum->value;
        }

        return $state === $enum->value;
    }
}
