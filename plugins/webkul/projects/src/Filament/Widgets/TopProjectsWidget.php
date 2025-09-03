<?php

namespace Webkul\Project\Filament\Widgets;

use BezhanSalleh\FilamentShield\Traits\HasWidgetShield;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Widgets\Concerns\InteractsWithPageFilters;
use Filament\Widgets\TableWidget as BaseWidget;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Support\Carbon;
use Webkul\Project\Models\Timesheet;

class TopProjectsWidget extends BaseWidget
{
    use HasWidgetShield, InteractsWithPageFilters;

    protected static bool $isLazy = false;

    protected static ?string $pollingInterval = '15s';

    public function getHeading(): string|Htmlable|null
    {
        return __('projects::filament/widgets/top-projects.heading.title');
    }

    public function table(Table $table): Table
    {
        $query = Timesheet::query();

        if (! empty($this->filters['selectedProjects'])) {
            $query->whereIn('project_id', $this->filters['selectedProjects']);
        }

        if (! empty($this->filters['selectedAssignees'])) {
            $query->whereIn('analytic_records.user_id', $this->filters['selectedAssignees']);
        }

        if (! empty($this->filters['selectedTags'])) {
            $query->whereHas('project.tags', function ($q) {
                $q->whereIn('projects_project_tag.tag_id', $this->filters['selectedTags']);
            });
        }

        if (! empty($this->filters['selectedPartners'])) {
            $query->whereIn('analytic_records.partner_id', $this->filters['selectedPartners']);
        }

        $startDate = ! is_null($this->filters['startDate'] ?? null) ?
            Carbon::parse($this->filters['startDate']) :
            null;

        $endDate = ! is_null($this->filters['endDate'] ?? null) ?
            Carbon::parse($this->filters['endDate']) :
            now();

        $query = $query
            ->join('projects_projects', 'projects_projects.id', '=', 'analytic_records.project_id')
            ->selectRaw('
                analytic_records.project_id,
                projects_projects.name as project_name,
                SUM(analytic_records.unit_amount) as total_hours,
                COUNT(DISTINCT analytic_records.task_id) as total_tasks
            ')
            ->whereBetween('analytic_records.created_at', [$startDate, $endDate])
            ->groupBy('analytic_records.project_id', 'projects_projects.name')
            ->orderByRaw('SUM(analytic_records.unit_amount) DESC')
            ->limit(10);

        return $table
            ->query($query)
            ->defaultPaginationPageOption(5)
            ->columns([
                Tables\Columns\TextColumn::make('project_name')
                    ->label(__('projects::filament/widgets/top-projects.table-columns.project-name'))
                    ->sortable(),
                Tables\Columns\TextColumn::make('total_hours')
                    ->label(__('projects::filament/widgets/top-projects.table-columns.hours-spent'))
                    ->sortable(),
                Tables\Columns\TextColumn::make('total_tasks')
                    ->label(__('projects::filament/widgets/top-projects.table-columns.tasks'))
                    ->sortable(),
            ]);
    }
}
