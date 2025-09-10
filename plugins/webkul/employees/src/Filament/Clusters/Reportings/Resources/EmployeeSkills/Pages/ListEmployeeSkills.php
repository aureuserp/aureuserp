<?php

namespace Webkul\Employee\Filament\Clusters\Reportings\Resources\EmployeeSkills\Pages;

use Filament\Resources\Pages\ListRecords;
use Illuminate\Database\Eloquent\Builder;
use Webkul\Employee\Filament\Clusters\Reportings\Resources\EmployeeSkills\EmployeeSkillResource;
use Webkul\TableViews\Filament\Components\PresetView;
use Webkul\TableViews\Filament\Concerns\HasTableViews;

class ListEmployeeSkills extends ListRecords
{
    use HasTableViews;

    protected static string $resource = EmployeeSkillResource::class;

    public function getPresetTableViews(): array
    {
        return [
            'with_skills' => PresetView::make(__('employees::filament/clusters/reportings/resources/employee-skill/pages/list-employee-skill.tabs.with-skill'))
                ->icon('heroicon-s-bolt')
                ->favorite()
                ->modifyQueryUsing(fn (Builder $query) => $query->whereHas('skill', fn ($q) => $q->whereNull('deleted_at'))),
            'without_skills' => PresetView::make(__('employees::filament/clusters/reportings/resources/employee-skill/pages/list-employee-skill.tabs.without-skill'))
                ->icon('heroicon-s-bolt-slash')
                ->favorite()
                ->modifyQueryUsing(fn (Builder $query) => $query->doesntHave('skill')->whereNull('deleted_at')),
            'archived' => PresetView::make(__('employees::filament/clusters/reportings/resources/employee-skill/pages/list-employee-skill.tabs.archived'))
                ->icon('heroicon-s-archive-box')
                ->favorite()
                ->modifyQueryUsing(fn (Builder $query) => $query->onlyTrashed()),
        ];
    }
}
