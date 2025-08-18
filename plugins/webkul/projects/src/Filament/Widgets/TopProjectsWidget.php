<?php

namespace Webkul\Project\Filament\Widgets;

use BezhanSalleh\FilamentShield\Traits\HasWidgetShield;
use Filament\Tables\Columns\TextColumn;
use Filament\Widgets\Concerns\InteractsWithPageFilters;
use Filament\Widgets\TableWidget as BaseWidget;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;
use Webkul\Project\Models\Timesheet;

class TopProjectsWidget extends BaseWidget
{
    use HasWidgetShield, InteractsWithPageFilters;

    protected static ?string $pollingInterval = '15s';

    public function getHeading(): string|Htmlable|null
    {
        return __('projects::filament/widgets/top-projects.heading');
    }

    protected function getTableQuery(): Builder
    {
        $query = Timesheet::query();

        if (! empty($this->pageFilters['selectedProjects'])) {
            $query->whereIn('project_id', $this->pageFilters['selectedProjects']);
        }

        if (! empty($this->pageFilters['selectedAssignees'])) {
            $query->whereIn('analytic_records.user_id', $this->pageFilters['selectedAssignees']);
        }

        if (! empty($this->pageFilters['selectedTags'])) {
            $query->whereHas('project.tags', function ($q) {
                $q->whereIn('projects_project_tag.tag_id', $this->pageFilters['selectedTags']);
            });
        }

        if (! empty($this->pageFilters['selectedPartners'])) {
            $query->whereIn('analytic_records.partner_id', $this->pageFilters['selectedPartners']);
        }

        $startDate = ! is_null($this->pageFilters['startDate'] ?? null) ?
            Carbon::parse($this->pageFilters['startDate']) :
            null;

        $endDate = ! is_null($this->pageFilters['endDate'] ?? null) ?
            Carbon::parse($this->pageFilters['endDate']) :
            now();

        return $query
            ->join('projects_projects', 'projects_projects.id', '=', 'analytic_records.project_id')
            ->selectRaw('
                analytic_records.project_id, 
                projects_projects.name as project_name, 
                SUM(analytic_records.unit_amount) as total_hours, 
                COUNT(DISTINCT analytic_records.task_id) as total_tasks
            ')
            ->whereBetween('analytic_records.created_at', [$startDate, $endDate])
            ->groupBy('analytic_records.project_id', 'projects_projects.name')
            ->orderByDesc('total_hours')
            ->limit(10);
    }

    protected function getTableColumns(): array
    {
        return [
            TextColumn::make('project_name')
                ->label(__('projects::filament/widgets/top-projects.table-columns.project-name'))
                ->sortable(),
            TextColumn::make('total_hours')
                ->label(__('projects::filament/widgets/top-projects.table-columns.hours-spent'))
                ->sortable(),
            TextColumn::make('total_tasks')
                ->label(__('projects::filament/widgets/top-projects.table-columns.tasks'))
                ->sortable(),
        ];
    }

    public function getTableRecordKey(Model|array $record): string
    {
        return (string) $record->project_id;
    }
}
