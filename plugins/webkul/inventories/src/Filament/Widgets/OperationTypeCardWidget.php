<?php

namespace Webkul\Inventory\Filament\Widgets;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Carbon;
use Livewire\Component;
use Webkul\Inventory\Enums\MoveState;
use Webkul\Inventory\Enums\OperationState;
use Webkul\Inventory\Enums\OperationType as OperationTypeEnum;
use Webkul\Inventory\Filament\Clusters\Operations\Resources\DeliveryResource;
use Webkul\Inventory\Filament\Clusters\Operations\Resources\InternalResource;
use Webkul\Inventory\Filament\Clusters\Operations\Resources\ReceiptResource;
use Webkul\Inventory\Models\Move;
use Webkul\Inventory\Models\Operation;
use Webkul\Inventory\Models\OperationType;

class OperationTypeCardWidget extends Component
{
    public OperationType $operationType;

    public function mount(OperationType $operationType): void
    {
        $this->operationType = $operationType;
    }

    public function getDashboardData(): array
    {
        return [
            'ready'       => $this->getReadyCount(),
            'links'       => $this->getLinks(),
            'ready_label' => $this->getReadyLabel(),
        ];
    }

    protected function baseQuery(): Builder
    {
        return Operation::query()
            ->where('operation_type_id', $this->operationType->id)
            ->whereNotIn('state', [OperationState::DONE, OperationState::CANCELED]);
    }

    protected function getReadyCount(): int
    {
        return $this->baseQuery()
            ->where('state', OperationState::ASSIGNED)
            ->count();
    }

    protected function getWaitingCount(): int
    {
        return $this->baseQuery()
            ->whereIn('state', [OperationState::CONFIRMED, OperationState::WAITING])
            ->count();
    }

    protected function getLateCount(): int
    {
        return $this->baseQuery()
            ->whereIn('state', [OperationState::ASSIGNED, OperationState::WAITING, OperationState::CONFIRMED])
            ->where(function (Builder $query) {
                $query->whereDate('scheduled_at', '<', today())
                    ->orWhere('has_deadline_issue', true);
            })
            ->count();
    }

    protected function getBackOrderCount(): int
    {
        return $this->baseQuery()
            ->whereNotNull('back_order_id')
            ->whereIn('state', [OperationState::CONFIRMED, OperationState::ASSIGNED, OperationState::WAITING])
            ->count();
    }

    protected function getMoveReadyCount(): int
    {
        return Move::query()
            ->where('operation_type_id', $this->operationType->id)
            ->where('state', MoveState::ASSIGNED)
            ->count();
    }

    protected function getReadyLabel(): string
    {
        $count = $this->getReadyCount();

        if ($count === 0) {
            return __('inventories::filament/widgets/operation-type-card-widget.open');
        }

        return match ($this->operationType->type) {
            OperationTypeEnum::INCOMING => __('inventories::filament/widgets/operation-type-card-widget.to-receive', ['count' => $count]),
            OperationTypeEnum::OUTGOING => __('inventories::filament/widgets/operation-type-card-widget.to-deliver', ['count' => $count]),
            default                     => __('inventories::filament/widgets/operation-type-card-widget.to-process', ['count' => $count]),
        };
    }

    protected function getLinks(): array
    {
        $links = [];

        if ($waiting = $this->getWaitingCount()) {
            $links[] = [
                'label' => __('inventories::filament/widgets/operation-type-card-widget.links.waiting'),
                'count' => $waiting,
                'url'   => $this->getUrl('waiting'),
            ];
        }

        if ($late = $this->getLateCount()) {
            $links[] = [
                'label' => __('inventories::filament/widgets/operation-type-card-widget.links.late'),
                'count' => $late,
                'url'   => $this->getUrl('todo'),
            ];
        }

        if ($backOrders = $this->getBackOrderCount()) {
            $links[] = [
                'label' => __('inventories::filament/widgets/operation-type-card-widget.links.back-orders'),
                'count' => $backOrders,
                'url'   => $this->getUrl('backorders'),
            ];
        }

        if ($moves = $this->getMoveReadyCount()) {
            $links[] = [
                'label' => __('inventories::filament/widgets/operation-type-card-widget.links.operations'),
                'count' => $moves,
                'url'   => $this->getUrl('ready'),
            ];
        }

        return $links;
    }

    public function getUrl(?string $activeTableView = null): string
    {
        $resource = match ($this->operationType->type) {
            OperationTypeEnum::INCOMING => ReceiptResource::class,
            OperationTypeEnum::OUTGOING => DeliveryResource::class,
            default                     => InternalResource::class,
        };

        return $resource::getUrl('index', array_filter([
            'activeTableView' => $activeTableView,
            'filters'         => [
                'queryBuilder' => [
                    'rules' => [
                        [
                            'type' => 'operationType',
                            'data' => [
                                'operator' => 'isRelatedTo',
                                'settings' => [
                                    'values' => [$this->operationType->id],
                                ],
                            ],
                        ],
                    ],
                ],
            ],
        ]));
    }

    /**
     * @return array<string, array{label: string, type: string}>
     */
    protected function getDateCategories(): array
    {
        return [
            'before'    => ['label' => __('inventories::filament/widgets/operation-type-card-widget.chart.before'), 'type' => 'past'],
            'yesterday' => ['label' => __('inventories::filament/widgets/operation-type-card-widget.chart.yesterday'), 'type' => 'past'],
            'today'     => ['label' => __('inventories::filament/widgets/operation-type-card-widget.chart.today'), 'type' => 'present'],
            'day_1'     => ['label' => __('inventories::filament/widgets/operation-type-card-widget.chart.tomorrow'), 'type' => 'future'],
            'day_2'     => ['label' => __('inventories::filament/widgets/operation-type-card-widget.chart.day-after-tomorrow'), 'type' => 'future'],
            'after'     => ['label' => __('inventories::filament/widgets/operation-type-card-widget.chart.after'), 'type' => 'future'],
        ];
    }

    public static function calculateDateCategory(?Carbon $date): ?string
    {
        if (! $date) {
            return null;
        }

        $startToday = today();

        return match (true) {
            $date->lt($startToday->copy()->subDay())     => 'before',
            $date->lt($startToday)                       => 'yesterday',
            $date->lt($startToday->copy()->addDay())     => 'today',
            $date->lt($startToday->copy()->addDays(2))   => 'day_1',
            $date->lt($startToday->copy()->addDays(3))   => 'day_2',
            default                                      => 'after',
        };
    }

    public function getChartData(): array
    {
        $categories = $this->getDateCategories();

        $totals = array_fill_keys(array_keys($categories), 0);

        $scheduledDates = Operation::query()
            ->where('operation_type_id', $this->operationType->id)
            ->whereIn('state', [OperationState::ASSIGNED, OperationState::WAITING, OperationState::CONFIRMED])
            ->pluck('scheduled_at');

        foreach ($scheduledDates as $scheduledDate) {
            $category = static::calculateDateCategory($scheduledDate);

            if ($category) {
                $totals[$category]++;
            }
        }

        $isEmpty = array_sum($totals) === 0;

        $colors = [
            'past'    => '#ef4444',
            'present' => '#3b82f6',
            'future'  => '#22c55e',
        ];

        return [
            'labels'   => array_column($categories, 'label'),
            'datasets' => [[
                'label' => $isEmpty
                    ? __('inventories::filament/widgets/operation-type-card-widget.chart.sample-data')
                    : __('inventories::filament/widgets/operation-type-card-widget.chart.transfers'),
                'data'            => array_values($totals),
                'backgroundColor' => $isEmpty
                    ? array_fill(0, count($categories), '#ebebeb')
                    : array_map(fn (array $category): string => $colors[$category['type']], array_values($categories)),
            ]],
        ];
    }

    public function render()
    {
        return view('inventories::filament.widgets.operation-type-card-widget', [
            'dashboard' => $this->getDashboardData(),
        ]);
    }
}
