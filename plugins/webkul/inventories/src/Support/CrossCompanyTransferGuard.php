<?php

namespace Webkul\Inventory\Support;

use Webkul\Inventory\Exceptions\CrossCompanyTransferException;
use Webkul\Inventory\Models\Location;
use Webkul\Support\Models\Scopes\CompanyScope;

class CrossCompanyTransferGuard
{
    public static function detect(?int $sourceId, ?int $destinationId): ?array
    {
        if (! $sourceId || ! $destinationId) {
            return null;
        }

        $locations = Location::withoutGlobalScope(CompanyScope::class)
            ->whereIn('id', [$sourceId, $destinationId])
            ->get(['id', 'name', 'full_name', 'company_id'])
            ->keyBy('id');

        $source = $locations->get($sourceId);

        $destination = $locations->get($destinationId);

        if (! $source || ! $destination) {
            return null;
        }

        if ($source->company_id && $destination->company_id && $source->company_id !== $destination->company_id) {
            return [
                'source'      => $source->full_name ?? $source->name,
                'destination' => $destination->full_name ?? $destination->name,
            ];
        }

        return null;
    }

    public static function assert(?int $sourceId, ?int $destinationId): void
    {
        if ($names = static::detect($sourceId, $destinationId)) {
            throw new CrossCompanyTransferException($names['source'], $names['destination']);
        }
    }
}
