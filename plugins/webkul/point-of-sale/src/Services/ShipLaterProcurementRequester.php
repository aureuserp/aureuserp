<?php

namespace Webkul\PointOfSale\Services;

use Illuminate\Support\Collection;
use Webkul\Inventory\Enums\LocationType;
use Webkul\Inventory\Facades\Inventory;
use Webkul\Inventory\Models\Location;
use Webkul\Inventory\Models\Product as InventoryProduct;
use Webkul\Inventory\Support\ProcurementOptions;
use Webkul\Inventory\Support\ProcurementRequest;
use Webkul\PointOfSale\Models\Order;
use Webkul\PointOfSale\Models\OrderLine;
use Webkul\Product\Enums\ProductType;

class ShipLaterProcurementRequester
{
    public function request(Order $order): void
    {
        $order->loadMissing(['lines.product', 'config.warehouse', 'config.shipLaterRoute', 'partner']);

        $lines = $order->lines->filter(
            fn (OrderLine $line): bool => $line->product?->type === ProductType::GOODS
                && ! float_is_zero((float) $line->qty, precisionDigits: 4)
        );

        if ($lines->isEmpty()) {
            return;
        }

        $group = $this->procurementGroup($order);

        $requests = $lines->map(
            fn (OrderLine $line): ProcurementRequest => $this->buildRequest($order, $line, $group)
        );

        Inventory::runProcurements($requests);
    }

    protected function procurementGroup(Order $order)
    {
        $group = $order->procurementGroup;

        if ($group) {
            return $group;
        }

        $group = $order->procurementGroup()->create([
            'name'       => $order->name ?? $order->reference,
            'move_type'  => $order->config->picking_policy,
            'partner_id' => $order->partner_id,
        ]);

        $order->forceFill(['procurement_group_id' => $group->id])->save();

        return $group;
    }

    protected function buildRequest(Order $order, OrderLine $line, $group): ProcurementRequest
    {
        $options = ProcurementOptions::make()
            ->group($group)
            ->plannedAt($order->shipped_at?->copy())
            ->deadline($order->shipped_at?->copy())
            ->routes($this->routes($order, $line))
            ->warehouse($line->warehouse ?? $order->config->warehouse)
            ->partner($order->partner)
            ->company($order->company)
            ->finalLocation($this->customerLocation($order));

        return new ProcurementRequest(
            product: InventoryProduct::withTrashed()->findOrFail($line->product_id),
            quantity: abs((float) $line->qty),
            uom: $line->uom ?? $line->product->uom,
            location: $options->destinationLocation() ?? $this->customerLocation($order),
            name: $line->full_product_name ?? $line->product->name,
            origin: $order->name ?? $order->reference,
            company: $order->company,
            options: $options,
        );
    }

    protected function routes(Order $order, OrderLine $line): Collection
    {
        $route = $line->route ?? $order->config->shipLaterRoute;

        return $route ? collect([$route]) : collect();
    }

    protected function customerLocation(Order $order): ?Location
    {
        return Location::withTrashed()
            ->where('type', LocationType::CUSTOMER)
            ->where(owned_by_company($order->company_id))
            ->orderByRaw('company_id IS NOT NULL DESC')
            ->first();
    }
}
