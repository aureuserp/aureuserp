<?php

namespace Webkul\Barcode\Support;

use Illuminate\Database\Eloquent\Builder;
use Webkul\Inventory\Models\OperationType;

class OperationTypes
{
    /**
     * @return array<int, int>
     */
    public static function siblingIds(OperationType $operationType): array
    {
        return OperationType::query()
            ->where('name', $operationType->name)
            ->where('type', $operationType->type)
            ->where(function (Builder $query) use ($operationType): void {
                $operationType->warehouse_id === null
                    ? $query->whereNull('warehouse_id')
                    : $query->where('warehouse_id', $operationType->warehouse_id);
            })
            ->pluck('id')
            ->map(fn ($id): int => (int) $id)
            ->all();
    }

    public static function groupKey(OperationType $operationType): string
    {
        return $operationType->name.'|'.$operationType->type?->value.'|'.$operationType->warehouse_id;
    }
}
