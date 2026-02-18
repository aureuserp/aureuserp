<?php

namespace Webkul\Inventory\Models;

use Illuminate\Support\Facades\Auth;
use Webkul\Inventory\Enums\OperationState;

class Dropship extends Operation
{
    public function getModelTitle(): string
    {
        return __('inventories::models/dropship.title');
    }

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($operation) {
            $operationType = OperationType::find($operation->operation_type_id);

            $operation->company_id ??= $operationType->destinationLocation->company_id;

            $operation->source_location_id ??= $operationType->source_location_id;

            $operation->destination_location_id ??= $operationType->destination_location_id;

            $operation->state ??= OperationState::DRAFT;

            $operation->creator_id ??= Auth::id();
        });
    }
}
