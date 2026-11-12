<?php

namespace Webkul\Project\Filament\Concerns;

use Illuminate\Contracts\Support\Htmlable;
use Livewire\Attributes\Url;
use Webkul\Project\Enums\TaskState;
use Webkul\Project\Filament\Resources\TaskResource;
use Webkul\Project\Filament\Widgets\TaskGraphChart;
use Webkul\Project\Models\Task;

trait HasTaskGraph
{
    #[Url(as: 'graph_type')]
    public ?string $graphType = 'bar';

    #[Url(as: 'graph_stacked')]
    public bool $graphStacked = false;

    #[Url(as: 'graph_sort')]
    public ?string $graphSort = null;

    public function setGraphType(string $type): void
    {
        $this->graphType = in_array($type, ['bar', 'line', 'pie'], true) ? $type : 'bar';
    }

    public function toggleGraphStacked(): void
    {
        $this->graphStacked = ! $this->graphStacked;
    }

    public function setGraphSort(?string $direction): void
    {
        $this->graphSort = $this->graphSort === $direction
            ? null
            : (in_array($direction, ['asc', 'desc'], true) ? $direction : null);
    }

    public function getTaskGraphEmbed(): Htmlable
    {
        return TaskGraphChart::embedGraph($this->getTaskGraph());
    }

    protected function buildTaskGraphData(array $groups): array
    {
        if (in_array($this->graphSort, ['asc', 'desc'], true)) {
            usort($groups, fn (array $a, array $b): int => [$a['tasks']->count(), $a['label']] <=> [$b['tasks']->count(), $b['label']]);

            if ($this->graphSort === 'desc') {
                $groups = array_reverse($groups);
            }
        }

        $labels = array_map(fn (array $group): string => $group['label'], $groups);

        $stacked = $this->graphStacked && $this->graphType === 'bar';

        if ($stacked) {
            $datasets = [];

            foreach (TaskState::cases() as $state) {
                $data = array_map(
                    fn (array $group): int => $group['tasks']->filter(fn (Task $task): bool => $task->state === $state)->count(),
                    $groups,
                );

                if (array_sum($data) === 0) {
                    continue;
                }

                $datasets[] = [
                    'label' => $state->getLabel(),
                    'data'  => $data,
                    'color' => $state->getColor() ?? 'primary',
                ];
            }
        } else {
            $datasets = [[
                'label' => TaskResource::getPluralModelLabel(),
                'data'  => array_map(fn (array $group): int => $group['tasks']->count(), $groups),
                'color' => null,
            ]];
        }

        return [
            'type'     => $this->graphType,
            'stacked'  => $stacked,
            'labels'   => $labels,
            'datasets' => $datasets,
        ];
    }
}
