<?php

namespace Webkul\PointOfSale\Services;

use Illuminate\Support\Collection;
use Webkul\Account\Models\FiscalPosition;
use Webkul\Account\Models\FiscalPositionTax;
use Webkul\Account\Models\Tax;
use Webkul\PointOfSale\Models\Order;

class FiscalPositionResolver
{
    public function resolveFor(Order $order): ?FiscalPosition
    {
        $config = $order->config;

        if ($order->is_takeaway && $config?->is_restaurant && $config->enable_takeaway && $config->takeaway_fiscal_position_id) {
            return $config->takeawayFiscalPosition;
        }

        if (! $config?->enable_fiscal_position) {
            return null;
        }

        if ($order->fiscal_position_id) {
            return $order->fiscalPosition;
        }

        return $config->fiscalPosition;
    }

    public function mapTaxIds(?FiscalPosition $fiscalPosition, array $taxIds): array
    {
        if (! $fiscalPosition || empty($taxIds)) {
            return $taxIds;
        }

        $mappings = FiscalPositionTax::query()
            ->where('fiscal_position_id', $fiscalPosition->id)
            ->get();

        if ($mappings->isEmpty()) {
            return $taxIds;
        }

        return collect($taxIds)
            ->flatMap(fn ($taxId): array => $this->mapTaxId($mappings, (int) $taxId))
            ->unique()
            ->values()
            ->all();
    }

    public function mapAccount(?FiscalPosition $fiscalPosition, $account)
    {
        if (! $fiscalPosition || ! $account) {
            return $account;
        }

        return $fiscalPosition->mapAccount($account) ?? $account;
    }

    public function mapTaxes(?FiscalPosition $fiscalPosition, Collection $taxes): Collection
    {
        if (! $fiscalPosition || $taxes->isEmpty()) {
            return $taxes;
        }

        $sourceIds = $taxes->pluck('id')->all();

        $mappedIds = $this->mapTaxIds($fiscalPosition, $sourceIds);

        if ($mappedIds === $sourceIds) {
            return $taxes;
        }

        return Tax::withoutGlobalScopes()->whereKey($mappedIds)->get();
    }

    public function applyTo(Order $order): Order
    {
        $fiscalPosition = $this->resolveFor($order);

        if (! $fiscalPosition) {
            return $order;
        }

        $order->forceFill(['fiscal_position_id' => $fiscalPosition->id])->save();

        return $order->refresh();
    }

    protected function mapTaxId(Collection $mappings, int $taxId): array
    {
        $matches = $mappings->where('tax_source_id', $taxId);

        if ($matches->isEmpty()) {
            return [$taxId];
        }

        return $matches
            ->pluck('tax_destination_id')
            ->filter()
            ->values()
            ->all();
    }
}
