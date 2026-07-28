<?php

namespace Webkul\Manufacturing\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Webkul\Inventory\Models\OperationType;
use Webkul\Manufacturing\Database\Factories\BillOfMaterialFactory;
use Webkul\Manufacturing\Enums\BillOfMaterialConsumption;
use Webkul\Manufacturing\Enums\BillOfMaterialReadyToProduce;
use Webkul\Manufacturing\Enums\BillOfMaterialType;
use Webkul\Product\Enums\ProductType;
use Webkul\Security\Models\User;
use Webkul\Support\Models\Company;
use Webkul\Support\Models\UOM;

class BillOfMaterial extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'manufacturing_bills_of_materials';

    protected $fillable = [
        'code',
        'type',
        'ready_to_produce',
        'consumption',
        'quantity',
        'allow_operation_dependencies',
        'produce_delay',
        'days_to_prepare_mo',
        'product_id',
        'uom_id',
        'operation_type_id',
        'company_id',
        'creator_id',
        'deleted_at',
    ];

    protected $casts = [
        'type'                         => BillOfMaterialType::class,
        'ready_to_produce'             => BillOfMaterialReadyToProduce::class,
        'consumption'                  => BillOfMaterialConsumption::class,
        'quantity'                     => 'decimal:4',
        'allow_operation_dependencies' => 'boolean',
        'produce_delay'                => 'integer',
        'days_to_prepare_mo'           => 'integer',
    ];

    protected array $context = [];

    public function setContext(array $context)
    {
        $this->context = array_merge($this->context, $context);

        return $this;
    }

    public function getModelTitle(): string
    {
        return __('manufacturing::models/bill-of-material.title');
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class)->withTrashed();
    }

    public function uom(): BelongsTo
    {
        return $this->belongsTo(UOM::class)->withTrashed();
    }

    public function operationType(): BelongsTo
    {
        return $this->belongsTo(OperationType::class)->withTrashed();
    }

    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function lines(): HasMany
    {
        return $this->hasMany(BillOfMaterialLine::class, 'bill_of_material_id');
    }

    public function byproducts(): HasMany
    {
        return $this->hasMany(BillOfMaterialByproduct::class, 'bill_of_material_id');
    }

    public function operations(): HasMany
    {
        return $this->hasMany(Operation::class, 'bill_of_material_id');
    }

    public function orders(): HasMany
    {
        return $this->hasMany(Order::class, 'bill_of_material_id');
    }

    public function unbuildOrders(): HasMany
    {
        return $this->hasMany(UnbuildOrder::class, 'bill_of_material_id');
    }

    public function getMatchedLines(array $selectedAttributeValueIds = []): Collection
    {
        return $this->lines
            ->filter(fn (BillOfMaterialLine $line): bool => $this->matchesSelectedVariant(
                $line->attributeValues->pluck('id')->all(),
                $selectedAttributeValueIds,
            ))
            ->values();
    }

    public function getMatchedOperations(array $selectedAttributeValueIds = []): Collection
    {
        return $this->operations
            ->filter(fn (Operation $operation): bool => $this->matchesSelectedVariant(
                $operation->attributeValues->pluck('id')->all(),
                $selectedAttributeValueIds,
            ))
            ->values();
    }

    public function getMatchedByproducts(array $selectedAttributeValueIds = []): Collection
    {
        return $this->byproducts
            ->filter(fn (BillOfMaterialByproduct $byproduct): bool => $this->matchesSelectedVariant(
                $byproduct->attributeValues->pluck('id')->all(),
                $selectedAttributeValueIds,
            ))
            ->values();
    }

    public function getQuantityMultiplier(float $quantity): float
    {
        $baseQuantity = (float) ($this->quantity ?? 1);

        if ($baseQuantity <= 0) {
            return 1.0;
        }

        return max($quantity, 0.0001) / $baseQuantity;
    }

    public function getComponentCost(float $quantity, array $selectedAttributeValueIds = []): float
    {
        $quantityMultiplier = $this->getQuantityMultiplier($quantity);

        return (float) $this->getMatchedLines($selectedAttributeValueIds)
            ->sum(fn (BillOfMaterialLine $line): float => round(
                ((float) $line->quantity * $quantityMultiplier) * (float) ($line->product?->cost ?? 0),
                2,
            ));
    }

    public function getUnitComponentCost(array $selectedAttributeValueIds = []): float
    {
        return (float) $this->getMatchedLines($selectedAttributeValueIds)
            ->sum(fn (BillOfMaterialLine $line): float => round(
                (float) $line->quantity * (float) ($line->product?->cost ?? 0),
                2,
            ));
    }

    public function getOperationDuration(float $quantity, array $selectedAttributeValueIds = [], ?Product $product = null): float
    {
        return (float) $this->getMatchedOperations($selectedAttributeValueIds)
            ->sum(fn (Operation $operation): float => $operation->getExpectedDuration($product ?? $this->product, $quantity));
    }

    public function getOperationCost(float $quantity, array $selectedAttributeValueIds = [], ?Product $product = null): float
    {
        return (float) $this->getMatchedOperations($selectedAttributeValueIds)
            ->sum(fn (Operation $operation): float => round(
                $operation->getExpectedCost($product ?? $this->product, $quantity),
                2,
            ));
    }

    public function getUnitOperationCost(array $selectedAttributeValueIds = [], ?Product $product = null): float
    {
        return (float) $this->getMatchedOperations($selectedAttributeValueIds)
            ->sum(fn (Operation $operation): float => round(
                $operation->getExpectedCost($product ?? $this->product, (float) ($this->quantity ?? 1)),
                2,
            ));
    }

    public function getTotalCost(float $quantity, array $selectedAttributeValueIds = [], ?Product $product = null): float
    {
        return $this->getComponentCost($quantity, $selectedAttributeValueIds)
            + $this->getOperationCost($quantity, $selectedAttributeValueIds, $product);
    }

    public function getUnitCost(array $selectedAttributeValueIds = [], ?Product $product = null): float
    {
        return $this->getUnitComponentCost($selectedAttributeValueIds)
            + $this->getUnitOperationCost($selectedAttributeValueIds, $product);
    }

    protected function matchesSelectedVariant(array $recordAttributeValueIds, array $selectedAttributeValueIds): bool
    {
        $recordAttributeValueIds = array_values(array_filter(array_map('intval', $recordAttributeValueIds)));
        $selectedAttributeValueIds = array_values(array_filter(array_map('intval', $selectedAttributeValueIds)));

        if ($recordAttributeValueIds === []) {
            return true;
        }

        if ($selectedAttributeValueIds === []) {
            return false;
        }

        return count(array_intersect($recordAttributeValueIds, $selectedAttributeValueIds)) === count($recordAttributeValueIds);
    }

    protected static function newFactory(): BillOfMaterialFactory
    {
        return BillOfMaterialFactory::new();
    }

    protected static function boot(): void
    {
        parent::boot();

        static::creating(function (self $billOfMaterial): void {
            $authUser = Auth::user();

            $billOfMaterial->creator_id ??= $authUser?->id;

            $billOfMaterial->company_id ??= $authUser?->default_company_id;

            $billOfMaterial->type ??= BillOfMaterialType::NORMAL;

            $billOfMaterial->ready_to_produce ??= BillOfMaterialReadyToProduce::ALL_AVAILABLE;

            $billOfMaterial->consumption ??= BillOfMaterialConsumption::WARNING;

            $billOfMaterial->produce_delay ??= 0;

            $billOfMaterial->days_to_prepare_mo ??= 0;
        });
    }

    public static function bomFindFilters($products, $operationType = null, $companyId = null, $bomType = false)
    {
        $productIds = $products->pluck('id')->all();

        $productTmplIds = $products->pluck('product_tmpl_id')->unique()->all();

        $query = static::query()
            ->where(function ($q) use ($productIds) {
                $q->whereIn('product_id', $productIds);
                // ->orWhere(function ($q2) use ($productTmplIds) {
                //     $q2->whereNull('product_id')
                //         ->whereIn('product_tmpl_id', $productTmplIds);
                // });
            });

        $resolvedCompanyId = $companyId ?? (static::$context['company_id'] ?? null);

        if ($resolvedCompanyId) {
            $query->where(function ($q) use ($resolvedCompanyId) {
                $q->whereNull('company_id')
                    ->orWhere('company_id', $resolvedCompanyId);
            });
        }

        if ($operationType) {
            $query->where(function ($q) use ($operationType) {
                $q->where('operation_type_id', $operationType->id)
                    ->orWhereNull('operation_type_id');
            });
        }

        if ($bomType) {
            $query->where('type', $bomType);
        }

        return $query;
    }

    public static function bomFind($products, $operationType = null, $companyId = false, $bomType = false): array
    {
        $bomByProduct = [];

        $products = $products->filter(fn ($product) => $product->type !== ProductType::SERVICE);

        if ($products->isEmpty()) {
            return $bomByProduct;
        }

        $query = static::bomFindFilters($products, operationType: $operationType, companyId: $companyId, bomType: $bomType)
            // ->orderBy('sequence')
            ->orderBy('product_id')
            ->orderBy('id');

        if ($products->count() === 1) {
            $bom = $query->first();

            if ($bom) {
                $bomByProduct[$products->first()->id] = $bom;
            }

            return $bomByProduct;
        }

        $billOfMaterials = $query->get();

        $productIds = $products->pluck('id')->all();

        foreach ($billOfMaterials as $bom) {
            $productsImplies = $bom->product ?: $bom->productTemplate->productVariants;

            if (! $productsImplies instanceof Collection) {
                $productsImplies = collect([$productsImplies]);
            }

            foreach ($productsImplies as $product) {
                if (
                    in_array($product->id, $productIds)
                    && ! array_key_exists($product->id, $bomByProduct)
                ) {
                    $bomByProduct[$product->id] = $bom;
                }
            }
        }

        return $bomByProduct;
    }

    public function explode($product, $quantity, $operationType = false, $neverAttributeValues = false): array
    {
        $productIds = [];

        $productBillOfMaterials = [];

        $updateProductBillOfMaterials = function () use (&$productIds, &$productBillOfMaterials, $operationType) {
            $products = Product::whereIn('id', $productIds)->get();

            $productBillOfMaterials = array_replace(
                $productBillOfMaterials,
                $this->bomFind(
                    $products,
                    operationType: $operationType ?: $this->operationType,
                    companyId: $this->company_id,
                    bomType: 'phantom'
                )
            );

            foreach ($products as $product) {
                if (! array_key_exists($product->id, $productBillOfMaterials)) {
                    $productBillOfMaterials[$product->id] = null;
                }
            }
        };

        $billOfMaterialsDone = [
            [
                $this,
                [
                    'qty'          => $quantity,
                    'product'      => $product,
                    'original_qty' => $quantity,
                    'parent_line'  => false,
                ],
            ],
        ];

        $linesDone = [];

        $lines = [];

        foreach ($this->lines as $line) {
            $productId = $line->product;

            $lines[] = [$line, $product, $quantity, false];

            $productIds[] = $productId->id;
        }

        $updateProductBillOfMaterials();

        $productIds = [];

        while (! empty($lines)) {
            [$currentLine, $currentProduct, $currentQty, $parentLine] = $lines[0];

            $lines = array_slice($lines, 1);

            if ($currentLine->skipBomLine($currentProduct, $neverAttributeValues)) {
                continue;
            }

            $lineQuantity = $currentQty * $currentLine->quantity;

            if (! array_key_exists($currentLine->product_id, $productBillOfMaterials)) {
                $updateProductBillOfMaterials();

                $productIds = [];
            }

            $bom = $productBillOfMaterials[$currentLine->product_id] ?? null;

            if ($bom) {
                $convertedLineQuantity = $currentLine->uom->computeQuantity(
                    $lineQuantity / $bom->quantity,
                    $bom->uom
                );

                foreach ($bom->lines as $line) {
                    $lines[] = [
                        $line,
                        $currentLine->product,
                        $convertedLineQuantity,
                        $currentLine,
                    ];
                }

                foreach ($bom->lines as $bomLine) {
                    if (! array_key_exists($bomLine->product_id, $productBillOfMaterials)) {
                        $productIds[] = $bomLine->product_id;
                    }
                }

                $billOfMaterialsDone[] = [
                    $bom,
                    [
                        'qty'          => $convertedLineQuantity,
                        'product'      => $currentProduct,
                        'original_qty' => $quantity,
                        'parent_line'  => $currentLine,
                    ],
                ];
            } else {
                $rounding = $currentLine->uom->rounding;

                $lineQuantity = float_round(
                    $lineQuantity,
                    precisionRounding: $rounding,
                    roundingMethod: 'UP'
                );

                $linesDone[] = [
                    $currentLine,
                    [
                        'qty'          => $lineQuantity,
                        'product'      => $currentProduct,
                        'original_qty' => $quantity,
                        'parent_line'  => $parentLine,
                    ],
                ];
            }
        }

        return [$billOfMaterialsDone, $linesDone];
    }
}
