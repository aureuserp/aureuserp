<?php

namespace Webkul\Crm\Filament\Support\Reports;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Webkul\Crm\Enums\LeadType;
use Webkul\Crm\Models\ActivityType;
use Webkul\Crm\Models\Stage;
use Webkul\Sale\Models\Team;
use Webkul\Security\Models\User;
use Webkul\Support\Models\Company;

class ActivityReport extends Report
{
    public static function dimensions(): array
    {
        $leadColumn = fn (string $column): string => "(select crm_leads.{$column} from crm_leads where crm_leads.id = crm_activity_logs.lead_id)";

        return [
            'activity_type'    => Dimension::relation(__('crm::filament/clusters/reporting/report.dimensions.activity-type'), 'crm_activity_logs.activity_type_id', 'activityType', ActivityType::class),
            'lead_type'        => Dimension::options(__('crm::filament/clusters/reporting/report.dimensions.type'), 'crm_activity_logs.lead_type', 'lead_type', LeadType::options()),
            'assigned_to'      => Dimension::relation(__('crm::filament/clusters/reporting/report.dimensions.assigned-to'), 'crm_activity_logs.assigned_to', 'assignedTo', User::class),
            'done_by'          => Dimension::relation(__('crm::filament/clusters/reporting/report.dimensions.done-by'), 'crm_activity_logs.done_by', 'doneBy', User::class),
            'done_at'          => Dimension::month(__('crm::filament/clusters/reporting/report.dimensions.done-at'), 'crm_activity_logs.done_at', 'done_at'),
            'salesperson'      => Dimension::relation(__('crm::filament/clusters/reporting/report.dimensions.salesperson'), 'crm_activity_logs.user_id', 'user', User::class),
            'team'             => Dimension::relation(__('crm::filament/clusters/reporting/report.dimensions.team'), 'crm_activity_logs.team_id', 'team', Team::class),
            'stage'            => Dimension::relation(__('crm::filament/clusters/reporting/report.dimensions.stage'), 'crm_activity_logs.stage_id', 'stage', Stage::class, orderAttribute: 'sort'),
            'company'          => Dimension::relation(__('crm::filament/clusters/reporting/report.dimensions.company'), 'crm_activity_logs.company_id', 'company', Company::class),
            'created_at'       => Dimension::month(__('crm::filament/clusters/reporting/report.dimensions.activity-created-at'), 'crm_activity_logs.activity_created_at', 'activity_created_at'),
            'expected_closing' => Dimension::month(__('crm::filament/clusters/reporting/report.dimensions.expected-closing'), $leadColumn('expected_closing_date'), 'lead.expected_closing_date', fn (Model $record): mixed => $record->lead?->expected_closing_date),
            'closed_at'        => Dimension::month(__('crm::filament/clusters/reporting/report.dimensions.closed-at'), $leadColumn('closed_at'), 'lead.closed_at', fn (Model $record): mixed => $record->lead?->closed_at),
        ];
    }

    public static function measures(): array
    {
        return [
            'count' => new Measure(__('crm::filament/clusters/reporting/report.measures.count'), 'count(*)'),
        ];
    }

    public static function presets(): array
    {
        return [
            'last_12_months' => [
                'label'   => __('crm::filament/clusters/reporting/report.presets.last-12-months'),
                'query'   => fn (Builder $query): Builder => $query->where('crm_activity_logs.done_at', '>=', now()->subYear()->startOfDay()),
                'default' => true,
            ],
            'my_activities' => [
                'label' => __('crm::filament/clusters/reporting/report.presets.my-activities'),
                'query' => fn (Builder $query): Builder => $query->where('crm_activity_logs.assigned_to', Auth::id()),
            ],
            'done_by_me' => [
                'label' => __('crm::filament/clusters/reporting/report.presets.done-by-me'),
                'query' => fn (Builder $query): Builder => $query->where('crm_activity_logs.done_by', Auth::id()),
            ],
            'opportunities' => [
                'label' => __('crm::filament/clusters/reporting/report.presets.opportunities'),
                'query' => fn (Builder $query): Builder => $query->where('crm_activity_logs.lead_type', LeadType::OPPORTUNITY),
            ],
            'leads' => [
                'label' => __('crm::filament/clusters/reporting/report.presets.leads'),
                'query' => fn (Builder $query): Builder => $query->where('crm_activity_logs.lead_type', LeadType::LEAD),
            ],
        ];
    }

    public static function getDefaultMeasure(): string
    {
        return 'count';
    }

    public static function getDefaultGroupBy(): string
    {
        return 'done_at';
    }

    public static function getDefaultSplitBy(): ?string
    {
        return 'activity_type';
    }
}
