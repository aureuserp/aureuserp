<?php

namespace Webkul\Inventory\Filament\Widgets;

use Filament\Widgets\Widget;
use Illuminate\Database\Eloquent\Collection;
use Webkul\Inventory\Enums\OperationType as OperationTypeEnum;
use Webkul\Inventory\Models\OperationType;

class OperationTypeOverviewWidget extends Widget
{
    protected string $view = 'inventories::filament.widgets.operation-type-overview-widget';

    protected int|string|array $columnSpan = 'full';

    public string $activeTab = 'all';

    public function getOperationTypes(): Collection
    {
        return OperationType::query()
            ->whereIn('type', static::getVisibleTypes())
            ->when(
                $this->activeTab !== 'all',
                fn ($query) => $query->where('type', $this->activeTab),
            )
            ->with('warehouse')
            ->orderBy('sort')
            ->orderBy('id')
            ->get();
    }

    /**
     * @return array<int, OperationTypeEnum>
     */
    public static function getVisibleTypes(): array
    {
        return [
            OperationTypeEnum::INCOMING,
            OperationTypeEnum::OUTGOING,
            OperationTypeEnum::INTERNAL,
        ];
    }
}
