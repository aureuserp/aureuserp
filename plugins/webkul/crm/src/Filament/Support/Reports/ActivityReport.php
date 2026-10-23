<?php

namespace Webkul\Crm\Filament\Support\Reports;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Webkul\Crm\Models\ActivityType;
use Webkul\Crm\Models\Lead;
use Webkul\Crm\Models\Stage;
use Webkul\Sale\Models\Team;
use Webkul\Security\Models\User;
use Webkul\Support\Models\Company;

class ActivityReport extends Report
{
    public static function dimensions(): array
    {
        $label = fn (string $key): string => __('crm::filament/clusters/reporting/report.dimensions.'.$key);

        $leadColumn = fn (string $column): string => "(select crm_leads.{$column} from crm_leads where crm_leads.id = crm_activity_logs.lead_id)";

        return [
            'activity_type'    => Dimension::relation($label('activity-type'), 'crm_activity_logs.activity_type_id', 'activityType', ActivityType::class),
            'lead_type'        => Dimension::options($label('type'), 'crm_activity_logs.lead_type', 'lead_type', [
                Lead::TYPE_LEAD        => __('crm::models/lead.title'),
                Lead::TYPE_OPPORTUNITY => __('crm::models/opportunity.title'),
            ]),
            'assigned_to'      => Dimension::relation($label('assigned-to'), 'crm_activity_logs.assigned_to', 'assignedTo', User::class),
            'done_by'          => Dimension::relation($label('done-by'), 'crm_activity_logs.done_by', 'doneBy', User::class),
            'done_at'          => Dimension::month($label('done-at'), 'crm_activity_logs.done_at', 'done_at'),
            'salesperson'      => Dimension::relation($label('salesperson'), 'crm_activity_logs.user_id', 'user', User::class),
            'team'             => Dimension::relation($label('team'), 'crm_activity_logs.team_id', 'team', Team::class),
            'stage'            => Dimension::relation($label('stage'), 'crm_activity_logs.stage_id', 'stage', Stage::class, orderAttribute: 'sort'),
            'company'          => Dimension::relation($label('company'), 'crm_activity_logs.company_id', 'company', Company::class),
            'created_at'       => Dimension::month($label('activity-created-at'), 'crm_activity_logs.activity_created_at', 'activity_created_at'),
            'expected_closing' => Dimension::month($label('expected-closing'), $leadColumn('expected_closing_date'), 'lead.expected_closing_date', fn (Model $record): mixed => $record->lead?->expected_closing_date),
            'closed_at'        => Dimension::month($label('closed-at'), $leadColumn('closed_at'), 'lead.closed_at', fn (Model $record): mixed => $record->lead?->closed_at),
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
        $label = fn (string $key): string => __('crm::filament/clusters/reporting/report.presets.'.$key);

        return [
            'last_12_months' => [
                'label'   => $label('last-12-months'),
                'query'   => fn (Builder $query): Builder => $query->where('crm_activity_logs.done_at', '>=', now()->subYear()->startOfDay()),
                'default' => true,
            ],
            'my_activities' => [
                'label' => $label('my-activities'),
                'query' => fn (Builder $query): Builder => $query->where('crm_activity_logs.assigned_to', Auth::id()),
            ],
            'done_by_me' => [
                'label' => $label('done-by-me'),
                'query' => fn (Builder $query): Builder => $query->where('crm_activity_logs.done_by', Auth::id()),
            ],
            'opportunities' => [
                'label' => $label('opportunities'),
                'query' => fn (Builder $query): Builder => $query->where('crm_activity_logs.lead_type', Lead::TYPE_OPPORTUNITY),
            ],
            'leads' => [
                'label' => $label('leads'),
                'query' => fn (Builder $query): Builder => $query->where('crm_activity_logs.lead_type', Lead::TYPE_LEAD),
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
