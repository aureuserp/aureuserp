<?php

use Illuminate\Support\Facades\Auth;
use Webkul\Account\Enums\AmountType;
use Webkul\Account\Enums\TaxIncludeOverride;
use Webkul\Account\Enums\TypeTaxUse;
use Webkul\Account\Facades\Account as AccountFacade;
use Webkul\Account\Models\Move;
use Webkul\Account\Models\Tax;
use Webkul\Sale\Enums\AdvancedPayment;
use Webkul\Inventory\Enums\LocationType;
use Webkul\Inventory\Enums\MoveState;
use Webkul\Inventory\Enums\OperationState;
use Webkul\Inventory\Facades\Inventory;
use Webkul\Inventory\Models\Operation;
use Webkul\Inventory\Models\ProductQuantity;
use Webkul\Inventory\Models\Warehouse;
use Webkul\Partner\Models\Partner;
use Webkul\Product\Models\Product;
use Webkul\Sale\Enums\OrderState;
use Webkul\Sale\Facades\SaleOrder as SaleOrderFacade;
use Webkul\Sale\Models\Order;
use Webkul\Sale\Models\OrderLine;
use Webkul\Security\Models\User;
use Webkul\Support\Models\Company;
use Webkul\Support\Models\Currency;
use Webkul\Support\Models\UOM;

class SaleHelper
{
    public static function company(): Company
    {
        return Company::query()->firstOrFail();
    }

    public static function actingAsAdmin(): User
    {
        $user = User::query()->firstOrFail();

        Auth::login($user);

        return $user;
    }

    public static function unitsUom(): UOM
    {
        return UOM::query()->where('name', 'Units')->firstOrFail();
    }

    public static function currency(): Currency
    {
        return Currency::query()->firstOrFail();
    }

    public static function partner(): Partner
    {
        return Partner::query()->first() ?? Partner::factory()->create();
    }

    public static function product(array $overrides = []): Product
    {
        $uom = static::unitsUom();

        return Product::factory()->create(array_merge([
            'is_storable' => true,
            'uom_id'      => $uom->id,
            'uom_po_id'   => $uom->id,
            'company_id'  => static::company()->id,
        ], $overrides));
    }

    public static function tax(
        float $amount = 10,
        AmountType $amountType = AmountType::PERCENT,
        TaxIncludeOverride $include = TaxIncludeOverride::TAX_EXCLUDED,
        bool $isBaseAffected = false,
        bool $includeBaseAmount = false,
        int $sort = 0,
    ): Tax {
        return Tax::factory()->create([
            'amount'                 => $amount,
            'amount_type'            => $amountType,
            'price_include_override' => $include,
            'type_tax_use'           => TypeTaxUse::SALE,
            'is_base_affected'       => $isBaseAffected,
            'include_base_amount'    => $includeBaseAmount,
            'sort'                   => $sort,
            'company_id'             => static::company()->id,
        ]);
    }

    public static function order(array $overrides = []): Order
    {
        return Order::factory()->create(array_merge([
            'state'       => OrderState::DRAFT,
            'partner_id'  => static::partner()->id,
            'currency_id' => static::currency()->id,
            'company_id'  => static::company()->id,
        ], $overrides));
    }

    public static function line(Order $order, Product $product, float $qty, float $priceUnit, float $discount = 0, array $taxes = []): OrderLine
    {
        $line = OrderLine::factory()->create([
            'order_id'        => $order->id,
            'state'           => $order->state,
            'product_id'      => $product->id,
            'product_uom_id'  => $product->uom_id,
            'product_uom_qty' => $qty,
            'product_qty'     => $qty,
            'price_unit'      => $priceUnit,
            'discount'        => $discount,
            'customer_lead'   => 0,
            'currency_id'     => $order->currency_id,
            'company_id'      => static::company()->id,
        ]);

        if ($taxes) {
            $line->taxes()->attach(collect($taxes)->pluck('id')->all());
        }

        return $line->refresh();
    }

    public static function compute(Order $order): Order
    {
        return SaleOrderFacade::computeSaleOrder($order->refresh());
    }

    public static function confirm(Order $order): Order
    {
        return SaleOrderFacade::confirmSaleOrder($order->refresh());
    }

    public static function createInvoice(Order $order): Move
    {
        SaleOrderFacade::createInvoice($order->refresh(), [
            'advance_payment_method' => AdvancedPayment::DELIVERED->value,
        ]);

        return $order->refresh()->accountMoves()->orderByDesc('id')->firstOrFail();
    }

    public static function postInvoice(Move $move): Move
    {
        return AccountFacade::confirmMove($move->refresh());
    }

    public static function cancelInvoice(Move $move): Move
    {
        return AccountFacade::cancelMove($move->refresh());
    }

    public static function resetInvoiceToDraft(Move $move): Move
    {
        return AccountFacade::resetToDraftMove($move->refresh());
    }

    public static function reverseInvoice(Move $move): Move
    {
        return AccountFacade::reverseMoves(collect([$move->refresh()]))->first();
    }

    public static function setLineQty(OrderLine $line, float $qty): void
    {
        $line->update([
            'product_uom_qty' => $qty,
            'product_qty'     => $qty,
        ]);
    }

    public static function confirmedOrder(Warehouse $warehouse, Product $product, float $qty, float $price = 100): Order
    {
        ProductQuantity::factory()->create([
            'product_id'        => $product->id,
            'location_id'       => $warehouse->lot_stock_location_id,
            'quantity'          => $qty,
            'reserved_quantity' => 0,
            'incoming_at'       => now(),
            'company_id'        => static::company()->id,
        ]);

        $order = static::order(['warehouse_id' => $warehouse->id]);

        static::line($order, $product, $qty, $price);

        return SaleOrderFacade::confirmSaleOrder($order->refresh())->load('operations.moves', 'lines');
    }

    public static function customerDelivery(Order $order): ?Operation
    {
        return $order->operations()->get()
            ->first(fn (Operation $op) => $op->moves
                ->contains(fn ($move) => $move->destinationLocation?->type === LocationType::CUSTOMER));
    }

    public static function deliverNextLeg(Order $order): ?Operation
    {
        $order->load('operations.moves');

        $ready = $order->operations
            ->first(fn (Operation $op) => ! in_array($op->state, [OperationState::DONE, OperationState::CANCELED])
                && $op->moves->contains(fn ($move) => in_array($move->state, [MoveState::ASSIGNED, MoveState::PARTIALLY_ASSIGNED])));

        if ($ready) {
            Inventory::doneTransfer($ready->refresh());
        }

        return $ready;
    }

    public static function deliverChain(Order $order): void
    {
        for ($i = 0; $i < 6; $i++) {
            if (! static::deliverNextLeg($order)) {
                break;
            }
        }
    }

    public static function readyLeg(Order $order): ?Operation
    {
        $order->load('operations.moves');

        return $order->operations
            ->first(fn (Operation $op) => ! in_array($op->state, [OperationState::DONE, OperationState::CANCELED])
                && $op->moves->contains(fn ($move) => in_array($move->state, [MoveState::ASSIGNED, MoveState::PARTIALLY_ASSIGNED])));
    }

    public static function partialDeliver(Order $order, float $qty): ?Operation
    {
        $leg = static::readyLeg($order);

        if (! $leg) {
            return null;
        }

        InventoryHelper::pick($leg->moves->first(), $qty);

        Inventory::doneTransfer($leg->refresh());

        return $leg->refresh();
    }
}
