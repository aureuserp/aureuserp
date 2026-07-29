<?php

namespace Webkul\Inventory\Filament\Concerns;

use Webkul\Inventory\Models\OperationType;
use Webkul\Inventory\Models\Product;
use Webkul\Inventory\Support\CrossCompanyTransferGuard;
use Webkul\Support\Filament\Concerns\HandlesCrossCompanyException;

trait HandlesCrossCompanyTransferException
{
    use HandlesCrossCompanyException;

    protected function assertCrossCompany(): void
    {
        CrossCompanyTransferGuard::assert(
            $this->data['source_location_id'] ?? null,
            $this->data['destination_location_id'] ?? null,
        );

        $this->assertCompanyConsistency();
    }

    protected function companyConsistencyMap(): array
    {
        return [
            'moves' => ['product_id' => Product::class],
        ];
    }

    protected function companyConsistencyCompanyId(): ?int
    {
        return $this->data['company_id']
            ?? OperationType::withTrashed()->find($this->data['operation_type_id'] ?? null)?->company_id;
    }
}
