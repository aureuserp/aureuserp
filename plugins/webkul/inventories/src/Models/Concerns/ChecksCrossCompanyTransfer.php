<?php

namespace Webkul\Inventory\Models\Concerns;

use Webkul\Inventory\Support\CrossCompanyTransferGuard;

/**
 * Rejects a record that moves stock between two companies.
 *
 * Same contract as ChecksCompanyConsistency: the model decides, the caller presents.
 */
trait ChecksCrossCompanyTransfer
{
    public static function bootChecksCrossCompanyTransfer(): void
    {
        $check = function ($model) {
            if (app()->runningInConsole()) {
                return;
            }

            $model->assertNoCrossCompanyTransfer();
        };

        static::creating($check);
        static::updating($check);
    }

    public function assertNoCrossCompanyTransfer(): void
    {
        if (! $this->isDirty(['source_location_id', 'destination_location_id'])) {
            return;
        }

        CrossCompanyTransferGuard::assert(
            $this->source_location_id,
            $this->destination_location_id,
        );
    }
}
