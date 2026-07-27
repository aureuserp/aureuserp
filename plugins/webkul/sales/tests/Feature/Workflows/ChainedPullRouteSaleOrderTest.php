<?php

use Webkul\Inventory\Enums\GroupPropagation;
use Webkul\Inventory\Enums\LocationType;
use Webkul\Inventory\Enums\MoveState;
use Webkul\Inventory\Enums\OperationType as OperationTypeEnum;
use Webkul\Inventory\Enums\ProcureMethod;
use Webkul\Inventory\Enums\RuleAction;
use Webkul\Inventory\Enums\RuleAuto;
use Webkul\Inventory\Facades\Inventory;
use Webkul\Inventory\Models\Location;
use Webkul\Inventory\Models\Move;
use Webkul\Inventory\Models\OperationType;
use Webkul\Inventory\Models\Product as InventoryProduct;
use Webkul\Inventory\Models\ProductQuantity;
use Webkul\Inventory\Models\Route;
use Webkul\Inventory\Models\Rule;
use Webkul\Sale\Enums\OrderState;
use Webkul\Sale\Facades\SaleOrder as SaleOrderFacade;

require_once __DIR__.'/../../../../support/tests/Helpers/TestBootstrapHelper.php';
require_once __DIR__.'/../../../../inventories/tests/Helpers/InventoryHelper.php';
require_once __DIR__.'/../../Helpers/SaleHelper.php';

beforeEach(function () {
    TestBootstrapHelper::ensurePluginInstalled('inventories');
    TestBootstrapHelper::ensurePluginInstalled('sales');

    foreach (['inventories', 'sales'] as $plugin) {
        Illuminate\Support\Facades\DB::table('plugins')->updateOrInsert(
            ['name' => $plugin],
            ['is_installed' => true, 'is_active' => true, 'updated_at' => now()],
        );
    }

    Webkul\PluginManager\Package::$plugins = Webkul\PluginManager\Models\Plugin::all()->keyBy('name');

    Illuminate\Support\Facades\URL::resolveMissingNamedRoutesUsing(fn () => '#');

    SaleHelper::actingAsAdmin();

    $this->warehouse = InventoryHelper::warehouse();
    $this->stock = $this->warehouse->lotStockLocation;
    $this->customer = Location::where('type', LocationType::CUSTOMER)->firstOrFail();

    $this->sharpening = InventoryHelper::sublocation($this->stock->parent, 'Sharpening');
    $this->shining = InventoryHelper::sublocation($this->stock->parent, 'Shining');

    $this->plainProduct = SaleHelper::product();
    $this->routedProduct = SaleHelper::product();

    foreach ([$this->plainProduct, $this->routedProduct] as $product) {
        ProductQuantity::factory()->create([
            'product_id'        => $product->id,
            'location_id'       => $this->stock->id,
            'quantity'          => 50,
            'reserved_quantity' => 0,
            'incoming_at'       => now(),
            'company_id'        => SaleHelper::company()->id,
        ]);
    }

    $this->sharpType = OperationType::create([
        'name'                    => 'Sharping',
        'type'                    => OperationTypeEnum::INTERNAL,
        'sequence_code'           => 'SHRP',
        'source_location_id'      => $this->stock->id,
        'destination_location_id' => $this->sharpening->id,
        'warehouse_id'            => $this->warehouse->id,
        'company_id'              => SaleHelper::company()->id,
    ]);

    $this->shineType = OperationType::create([
        'name'                    => 'Shining',
        'type'                    => OperationTypeEnum::INTERNAL,
        'sequence_code'           => 'SHIN',
        'source_location_id'      => $this->sharpening->id,
        'destination_location_id' => $this->shining->id,
        'warehouse_id'            => $this->warehouse->id,
        'company_id'              => SaleHelper::company()->id,
    ]);

    $this->deliverType = OperationType::create([
        'name'                    => 'Deliver',
        'type'                    => OperationTypeEnum::OUTGOING,
        'sequence_code'           => 'CDLV',
        'source_location_id'      => $this->shining->id,
        'destination_location_id' => $this->customer->id,
        'warehouse_id'            => $this->warehouse->id,
        'company_id'              => SaleHelper::company()->id,
    ]);

    $this->route = Route::create([
        'name'                        => 'Sharp-Shine-Deliver',
        'product_selectable'          => true,
        'product_category_selectable' => false,
        'warehouse_selectable'        => false,
        'packaging_selectable'        => false,
        'company_id'                  => SaleHelper::company()->id,
    ]);

    $rule = function (int $sort, string $name, Location $src, Location $dst, ProcureMethod $method, int $opType) {
        Rule::create([
            'sort'                     => $sort,
            'name'                     => $name,
            'route_sort'               => $sort,
            'group_propagation_option' => GroupPropagation::PROPAGATE,
            'action'                   => RuleAction::PULL,
            'procure_method'           => $method,
            'auto'                     => RuleAuto::MANUAL,
            'source_location_id'       => $src->id,
            'destination_location_id'  => $dst->id,
            'route_id'                 => $this->route->id,
            'operation_type_id'        => $opType,
            'warehouse_id'             => $this->warehouse->id,
            'company_id'               => SaleHelper::company()->id,
        ]);
    };

    $rule(6, 'Sharp: Stock → Sharpening', $this->stock, $this->sharpening, ProcureMethod::MAKE_TO_STOCK, $this->sharpType->id);
    $rule(7, 'Shine: Sharpening → Shining', $this->sharpening, $this->shining, ProcureMethod::MAKE_TO_ORDER, $this->shineType->id);
    $rule(8, 'Deliver: Shining → Customers', $this->shining, $this->customer, ProcureMethod::MTS_ELSE_MTO, $this->deliverType->id);

    InventoryProduct::findOrFail($this->routedProduct->id)
        ->routes()
        ->syncWithoutDetaching([$this->route->id]);

    $order = SaleHelper::order(['warehouse_id' => $this->warehouse->id]);

    SaleHelper::line($order, $this->plainProduct, 10, 100);
    SaleHelper::line($order, $this->routedProduct, 10, 100);

    $this->order = SaleOrderFacade::confirmSaleOrder($order->refresh())
        ->load('operations.moves', 'lines');
});

function chainMoves($order, int $productId): \Illuminate\Support\Collection
{
    return $order->operations
        ->flatMap->moves
        ->where('product_id', $productId)
        ->sortBy('id')
        ->values();
}

it('confirms the sale order', function () {
    expect($this->order->state)->toBe(OrderState::SALE);
});

it('creates a single default delivery leg for the plain product', function () {
    $moves = chainMoves($this->order, $this->plainProduct->id);

    expect($moves)->toHaveCount(1);
    expect($moves->first()->source_location_id)->toBe($this->stock->id)
        ->and($moves->first()->destination_location_id)->toBe($this->customer->id)
        ->and($moves->first()->operation_type_id)->toBe($this->warehouse->out_type_id);
});

it('creates the full three leg chain for the routed product', function () {
    $moves = chainMoves($this->order, $this->routedProduct->id);

    expect($moves)->toHaveCount(3);

    $byOpType = $moves->keyBy('operation_type_id');

    expect($byOpType->has($this->sharpType->id))->toBeTrue()
        ->and($byOpType->has($this->shineType->id))->toBeTrue()
        ->and($byOpType->has($this->deliverType->id))->toBeTrue();

    expect($byOpType[$this->sharpType->id]->source_location_id)->toBe($this->stock->id)
        ->and($byOpType[$this->sharpType->id]->destination_location_id)->toBe($this->sharpening->id);

    expect($byOpType[$this->shineType->id]->source_location_id)->toBe($this->sharpening->id)
        ->and($byOpType[$this->shineType->id]->destination_location_id)->toBe($this->shining->id);

    expect($byOpType[$this->deliverType->id]->source_location_id)->toBe($this->shining->id)
        ->and($byOpType[$this->deliverType->id]->destination_location_id)->toBe($this->customer->id);
});

it('produces four inventory documents in total, matching odoo', function () {
    expect($this->order->operations)->toHaveCount(4);
});

it('auto-readies each downstream leg as the upstream leg is validated', function () {
    $moveFor = fn (int $opTypeId) => Move::query()
        ->where('procurement_group_id', $this->order->procurement_group_id)
        ->where('operation_type_id', $opTypeId)
        ->first();

    $sharpMove = $moveFor($this->sharpType->id);

    expect($sharpMove->state)->toBe(MoveState::ASSIGNED);
    expect($moveFor($this->shineType->id)->state)->toBe(MoveState::WAITING);
    expect($moveFor($this->deliverType->id)->state)->toBe(MoveState::CONFIRMED);

    Inventory::doneTransfer($sharpMove->operation->refresh());

    expect($moveFor($this->shineType->id)->state)->toBe(MoveState::ASSIGNED);
    expect($moveFor($this->deliverType->id)->state)->toBe(MoveState::CONFIRMED);

    Inventory::doneTransfer($moveFor($this->shineType->id)->operation->refresh());

    expect($moveFor($this->deliverType->id)->state)->toBe(MoveState::ASSIGNED);
});
