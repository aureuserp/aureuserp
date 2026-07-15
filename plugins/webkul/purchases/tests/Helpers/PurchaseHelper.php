<?php

use Illuminate\Support\Facades\Auth;
use Webkul\Account\Enums\AmountType;
use Webkul\Account\Enums\TaxIncludeOverride;
use Webkul\Account\Enums\TypeTaxUse;
use Webkul\Account\Facades\Account as AccountFacade;
use Webkul\Account\Models\Move as AccountMove;
use Webkul\Account\Models\Tax;
use Webkul\Inventory\Enums\LocationType;
use Webkul\Inventory\Enums\MoveState;
use Webkul\Inventory\Enums\OperationState;
use Webkul\Inventory\Facades\Inventory;
use Webkul\Inventory\Models\Operation;
use Webkul\Inventory\Models\Warehouse;
use Webkul\Partner\Models\Partner;
use Webkul\Product\Models\Product;
use Webkul\Purchase\Enums\OrderState;
use Webkul\Purchase\Facades\PurchaseOrder as PurchaseOrderFacade;
use Webkul\Purchase\Models\Order;
use Webkul\Purchase\Models\OrderLine;
use Webkul\Security\Models\User;
use Webkul\Support\Models\Company;
use Webkul\Support\Models\Currency;
use Webkul\Support\Models\UOM;

class PurchaseHelper
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
            'type_tax_use'           => TypeTaxUse::PURCHASE,
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
            'order_id'     => $order->id,
            'state'        => $order->state,
            'product_id'   => $product->id,
            'uom_id'       => $product->uom_id,
            'product_qty'  => $qty,
            'product_uom_qty' => $qty,
            'price_unit'   => $priceUnit,
            'discount'     => $discount,
            'partner_id'   => $order->partner_id,
            'currency_id'  => $order->currency_id,
            'company_id'   => static::company()->id,
        ]);

        if ($taxes) {
            $line->taxes()->attach(collect($taxes)->pluck('id')->all());
        }

        return $line->refresh();
    }

    public static function compute(Order $order): Order
    {
        return PurchaseOrderFacade::computePurchaseOrder($order->refresh());
    }

    public static function confirmedOrder(Warehouse $warehouse, Product $product, float $qty, float $price = 100): Order
    {
        $order = static::order(['operation_type_id' => $warehouse->in_type_id]);

        static::line($order, $product, $qty, $price);

        return PurchaseOrderFacade::confirmPurchaseOrder($order->refresh())->load('operations.moves', 'lines');
    }

    public static function confirm(Order $order): Order
    {
        return PurchaseOrderFacade::confirmPurchaseOrder($order->refresh());
    }

    public static function createBill(Order $order): AccountMove
    {
        PurchaseOrderFacade::createPurchaseOrderBill($order->refresh());

        $bill = $order->refresh()->accountMoves()->orderByDesc('id')->firstOrFail();

        $bill->update(['invoice_date' => now()]);

        return $bill->refresh();
    }

    public static function postBill(AccountMove $move): AccountMove
    {
        return AccountFacade::confirmMove($move->refresh());
    }

    public static function cancelBill(AccountMove $move): AccountMove
    {
        return AccountFacade::cancelMove($move->refresh());
    }

    public static function resetBillToDraft(AccountMove $move): AccountMove
    {
        return AccountFacade::resetToDraftMove($move->refresh());
    }

    public static function reverseBill(AccountMove $move): AccountMove
    {
        return AccountFacade::reverseMoves(collect([$move->refresh()]))->first();
    }

    public static function vendorReceipt(Order $order): ?Operation
    {
        return $order->operations()->get()
            ->first(fn (Operation $op) => $op->moves
                ->contains(fn ($move) => $move->sourceLocation?->type === LocationType::SUPPLIER));
    }

    public static function readyReceipt(Order $order): ?Operation
    {
        $order->load('operations.moves');

        return $order->operations
            ->first(fn (Operation $op) => ! in_array($op->state, [OperationState::DONE, OperationState::CANCELED])
                && $op->moves->contains(fn ($move) => ! in_array($move->state, [MoveState::DONE, MoveState::CANCELED, MoveState::DRAFT])));
    }

    public static function receiveNextLeg(Order $order): ?Operation
    {
        $leg = static::readyReceipt($order);

        if ($leg) {
            Inventory::doneTransfer($leg->refresh());
        }

        return $leg;
    }

    public static function receiveChain(Order $order): void
    {
        for ($i = 0; $i < 6; $i++) {
            if (! static::receiveNextLeg($order)) {
                break;
            }
        }
    }

    public static function partialReceive(Order $order, float $qty): ?Operation
    {
        $leg = static::readyReceipt($order);

        if (! $leg) {
            return null;
        }

        $leg->moves->first()->update(['quantity' => $qty]);

        Inventory::doneTransfer($leg->refresh());

        return $leg->refresh();
    }
}
