<?php

namespace Webkul\Manufacturing\Filament\Clusters\Products\Resources\BillsOfMaterialResource\Pages;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Resources\Pages\Concerns\InteractsWithRecord;
use Filament\Resources\Pages\Page;
use Illuminate\Support\Collection;
use Webkul\Manufacturing\Filament\Clusters\Products\Resources\BillsOfMaterialResource;
use Webkul\Manufacturing\Models\BillOfMaterialByproduct;
use Webkul\Manufacturing\Models\BillOfMaterialLine;
use Webkul\Manufacturing\Models\Operation;
use Webkul\Manufacturing\Models\Product;
use Webkul\Support\Traits\HasRecordNavigationTabs;

class BillOfMaterialOverview extends Page implements HasForms
{
    use HasRecordNavigationTabs;
    use InteractsWithForms;
    use InteractsWithRecord {
        HasRecordNavigationTabs::getSubNavigation insteadof InteractsWithRecord;
    }

    protected static string $resource = BillsOfMaterialResource::class;

    protected string $view = 'manufacturing::filament.clusters.products.resources.bill-of-material.pages.bill-of-material-overview';

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-bars-3-bottom-left';

    public ?array $data = [];

    public function mount(int|string $record): void
    {
        $this->record = $this->resolveRecord($record);

        $this->getRecord()->load([
            'product.uom',
            'product.routes',
            'product.quantities.location',
            'product.variants',
            'product.variants.combinations.productAttributeValue.attributeOption',
            'uom',
            'lines.product.uom',
            'lines.attributeValues.attributeOption',
            'operations.workCenter',
            'operations.attributeValues.attributeOption',
            'byproducts.product.uom',
            'byproducts.attributeValues.attributeOption',
        ]);

        $this->form->fill([
            'quantity' => (float) $this->getRecord()->quantity,
        ]);
    }

    public static function getNavigationLabel(): string
    {
        return __('manufacturing::filament/clusters/products/resources/bill-of-material/pages/bill-of-material-overview.navigation.title');
    }

    public function getTitle(): string
    {
        return __('manufacturing::filament/clusters/products/resources/bill-of-material/pages/bill-of-material-overview.title');
    }

    public function getHeading(): string
    {
        return __('manufacturing::filament/clusters/products/resources/bill-of-material/pages/bill-of-material-overview.heading');
    }

    public function getProductAvailableQuantity(): float
    {
        return (float) $this->getRecord()->product?->quantities->sum(
            fn ($quantity): float => (float) $quantity->available_quantity,
        );
    }

    public function getProductOnHandQuantity(): float
    {
        return (float) ($this->getRecord()->product?->available_qty ?? 0);
    }

    public function getProductDateLabel(): string
    {
        return now()->format('m/d/Y');
    }

    public function getProductRows(): Collection
    {
        $billOfMaterial = $this->getRecord();
        $product = $billOfMaterial->product;
        $quantityMultiplier = $this->getQuantityMultiplier();
        $selectedAttributeValueIds = $this->getSelectedVariantAttributeValueIds();

        return collect([
            [
                'label'        => $product?->name ?? '—',
                'quantity'     => $this->getOverviewQuantity(),
                'uom'          => $billOfMaterial->uom?->name ?? $product?->uom?->name ?? '—',
                'lead_time'    => (int) ($billOfMaterial->produce_delay ?? 0),
                'route'        => $product?->routes?->pluck('name')->filter()->implode(', ') ?: '—',
                'bom_cost'     => $billOfMaterial->getTotalCost($this->getOverviewQuantity(), $selectedAttributeValueIds, $this->getOverviewProduct()),
                'product_cost' => (float) ($product?->cost ?? 0),
                'is_parent'    => true,
            ],
        ])->merge(
            $billOfMaterial->getMatchedLines($selectedAttributeValueIds)
                ->map(function (BillOfMaterialLine $line) use ($quantityMultiplier): array {
                    $unitCost = (float) ($line->product?->cost ?? 0);
                    $totalCost = $unitCost * ((float) $line->quantity * $quantityMultiplier);

                    return [
                        'label'        => $line->product?->name ?? '—',
                        'quantity'     => (float) $line->quantity * $quantityMultiplier,
                        'uom'          => $line->uom?->name ?? $line->product?->uom?->name ?? '—',
                        'lead_time'    => null,
                        'route'        => '—',
                        'bom_cost'     => $totalCost,
                        'product_cost' => $totalCost,
                        'is_parent'    => false,
                    ];
                }),
        );
    }

    public function getOperationRows(): Collection
    {
        $overviewQuantity = $this->getOverviewQuantity();
        $selectedAttributeValueIds = $this->getSelectedVariantAttributeValueIds();
        $overviewProduct = $this->getOverviewProduct();

        return $this->getRecord()->getMatchedOperations($selectedAttributeValueIds)
            ->map(function (Operation $operation) use ($overviewProduct, $overviewQuantity): array {
                $duration = $operation->getExpectedDuration($overviewProduct, $overviewQuantity);

                return [
                    'label'          => trim(implode(' - ', array_filter([
                        $operation->name,
                        $operation->workCenter?->name,
                    ]))),
                    'duration'       => $duration,
                    'duration_label' => format_float_time($duration, 'minutes'),
                    'uom'            => __('manufacturing::filament/clusters/products/resources/bill-of-material/pages/bill-of-material-overview.table.rows.minutes'),
                    'cost'           => $operation->getExpectedCost($overviewProduct, $overviewQuantity),
                ];
            });
    }

    public function getByproductRows(): Collection
    {
        $quantityMultiplier = $this->getQuantityMultiplier();
        $selectedAttributeValueIds = $this->getSelectedVariantAttributeValueIds();

        return $this->getRecord()->getMatchedByproducts($selectedAttributeValueIds)
            ->map(function (BillOfMaterialByproduct $byproduct) use ($quantityMultiplier): array {
                return [
                    'label'    => $byproduct->product?->name ?? '—',
                    'quantity' => (float) $byproduct->quantity * $quantityMultiplier,
                    'uom'      => $byproduct->uom?->name ?? $byproduct->product?->uom?->name ?? '—',
                ];
            });
    }

    public function getTotalOperationDuration(): float
    {
        return $this->getRecord()->getOperationDuration(
            $this->getOverviewQuantity(),
            $this->getSelectedVariantAttributeValueIds(),
            $this->getOverviewProduct(),
        );
    }

    public function getTotalOperationDurationLabel(): string
    {
        return format_float_time($this->getTotalOperationDuration(), 'minutes');
    }

    public function getTotalOperationCost(): float
    {
        return $this->getRecord()->getOperationCost(
            $this->getOverviewQuantity(),
            $this->getSelectedVariantAttributeValueIds(),
            $this->getOverviewProduct(),
        );
    }

    public function getUnitOperationCost(): float
    {
        return $this->getRecord()->getUnitOperationCost(
            $this->getSelectedVariantAttributeValueIds(),
            $this->getOverviewProduct(),
        );
    }

    public function getTotalComponentCost(): float
    {
        return $this->getRecord()->getComponentCost(
            $this->getOverviewQuantity(),
            $this->getSelectedVariantAttributeValueIds(),
        );
    }

    public function getUnitComponentCost(): float
    {
        return $this->getRecord()->getUnitComponentCost(
            $this->getSelectedVariantAttributeValueIds(),
        );
    }

    public function getTotalBomCost(): float
    {
        return $this->getRecord()->getTotalCost(
            $this->getOverviewQuantity(),
            $this->getSelectedVariantAttributeValueIds(),
            $this->getOverviewProduct(),
        );
    }

    public function getUnitBomCost(): float
    {
        return $this->getRecord()->getUnitCost(
            $this->getSelectedVariantAttributeValueIds(),
            $this->getOverviewProduct(),
        );
    }

    public function getDisplayedUnitBomCost(): float
    {
        $overviewQuantity = $this->getOverviewQuantity();

        if ($overviewQuantity <= 0) {
            return 0.0;
        }

        return round($this->getTotalBomCost() / $overviewQuantity, 2);
    }

    public function hasVariantSelector(): bool
    {
        $product = $this->getRecord()->product;

        return (bool) ($product?->is_configurable && filled($product?->parent_id) === false && $product?->variants->isNotEmpty());
    }

    public function getVariantOptions(): array
    {
        if (! $this->hasVariantSelector()) {
            return [];
        }

        return $this->getRecord()->product->variants
            ->mapWithKeys(function (Product $variant): array {
                $label = $variant->combinations
                    ->pluck('productAttributeValue.attributeOption.name')
                    ->filter()
                    ->implode(' / ');

                return [
                    $variant->getKey() => $label !== '' ? $label : $variant->name,
                ];
            })
            ->all();
    }

    public function getSelectedVariant(): ?Product
    {
        $variantId = $this->data['variant_id'] ?? null;

        if (! $variantId) {
            return null;
        }

        return $this->getRecord()->product?->variants?->firstWhere('id', (int) $variantId);
    }

    public function getSelectedVariantLabel(): ?string
    {
        return $this->getSelectedVariant()
            ? $this->getVariantOptions()[$this->getSelectedVariant()->getKey()] ?? $this->getSelectedVariant()->name
            : null;
    }

    public function getOverviewProduct(): ?Product
    {
        return $this->getSelectedVariant() ?? $this->getRecord()->product;
    }

    protected function getFormSchema(): array
    {
        return [
            TextInput::make('quantity')
                ->label(__('manufacturing::filament/clusters/products/resources/bill-of-material/pages/bill-of-material-overview.filters.quantity'))
                ->numeric()
                ->minValue(0.0001)
                ->step('0.0001')
                ->default((float) $this->getRecord()->quantity)
                ->suffix($this->getRecord()->uom?->name ?? $this->getRecord()->product?->uom?->name ?? '—')
                ->live(),

            Select::make('variant_id')
                ->label(__('manufacturing::filament/clusters/products/resources/bill-of-material/pages/bill-of-material-overview.filters.variant'))
                ->options(fn (): array => $this->getVariantOptions())
                ->searchable()
                ->native(false)
                ->wrapOptionLabels(false)
                ->visible(fn (): bool => $this->hasVariantSelector())
                ->live(),
        ];
    }

    protected function getFormStatePath(): string
    {
        return 'data';
    }

    public function getOverviewQuantity(): float
    {
        return max((float) ($this->data['quantity'] ?? $this->getRecord()->quantity ?? 1), 0.0001);
    }

    protected function getQuantityMultiplier(): float
    {
        $baseQuantity = (float) ($this->getRecord()->quantity ?? 1);

        if ($baseQuantity <= 0) {
            return 1.0;
        }

        return $this->getOverviewQuantity() / $baseQuantity;
    }

    protected function getSelectedVariantAttributeValueIds(): array
    {
        $variant = $this->getSelectedVariant();

        if (! $variant) {
            return [];
        }

        return $variant->combinations
            ->pluck('product_attribute_value_id')
            ->filter()
            ->map(fn (mixed $id): int => (int) $id)
            ->all();
    }

    protected function getHeaderActions(): array
    {
        return [];
    }
}
