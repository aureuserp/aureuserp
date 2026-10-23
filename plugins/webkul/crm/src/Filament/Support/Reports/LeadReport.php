<?php

namespace Webkul\Crm\Filament\Support\Reports;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;
use Webkul\Crm\Models\Lead;
use Webkul\Crm\Models\LostReason;
use Webkul\Crm\Models\Stage;
use Webkul\Sale\Models\Team;
use Webkul\Security\Models\User;
use Webkul\Support\Models\Company;
use Webkul\Support\Models\Country;
use Webkul\Support\Models\UtmCampaign;
use Webkul\Support\Models\UTMMedium;
use Webkul\Support\Models\UTMSource;

abstract class LeadReport extends Report
{
    abstract protected static function getPresetDefaults(): array;

    public static function dimensions(): array
    {
        $label = fn (string $key): string => __('crm::filament/clusters/reporting/report.dimensions.'.$key);

        return [
            'salesperson'      => Dimension::relation($label('salesperson'), 'crm_leads.user_id', 'user', User::class),
            'team'             => Dimension::relation($label('team'), 'crm_leads.team_id', 'team', Team::class),
            'stage'            => Dimension::relation($label('stage'), 'crm_leads.stage_id', 'stage', Stage::class, orderAttribute: 'sort'),
            'type'             => Dimension::options($label('type'), 'crm_leads.type', 'type', [
                Lead::TYPE_LEAD        => __('crm::models/lead.title'),
                Lead::TYPE_OPPORTUNITY => __('crm::models/opportunity.title'),
            ]),
            'city'             => Dimension::attribute($label('city'), 'crm_leads.city', 'city'),
            'country'          => Dimension::relation($label('country'), 'crm_leads.country_id', 'country', Country::class),
            'lost_reason'      => Dimension::relation($label('lost-reason'), 'crm_leads.lost_reason_id', 'lostReason', LostReason::class),
            'company'          => Dimension::relation($label('company'), 'crm_leads.company_id', 'company', Company::class),
            'campaign'         => Dimension::relation($label('campaign'), 'crm_leads.utm_campaign_id', 'campaign', UtmCampaign::class),
            'medium'           => Dimension::relation($label('medium'), 'crm_leads.utm_medium_id', 'medium', UTMMedium::class),
            'source'           => Dimension::relation($label('source'), 'crm_leads.utm_source_id', 'source', UTMSource::class),
            'expected_closing' => Dimension::month($label('expected-closing'), 'crm_leads.expected_closing_date', 'expected_closing_date'),
            'created_at'       => Dimension::month($label('created-at'), 'crm_leads.created_at', 'created_at'),
            'converted_at'     => Dimension::month($label('converted-at'), 'crm_leads.converted_at', 'converted_at'),
            'closed_at'        => Dimension::month($label('closed-at'), 'crm_leads.closed_at', 'closed_at'),
        ];
    }

    public static function measures(): array
    {
        $label = fn (string $key): string => __('crm::filament/clusters/reporting/report.measures.'.$key);

        return [
            'count'            => new Measure($label('count'), 'count(*)'),
            'expected_revenue' => new Measure($label('expected-revenue'), 'sum(crm_leads.expected_revenue)', Measure::FORMAT_CURRENCY),
            'prorated_revenue' => new Measure($label('prorated-revenue'), 'sum(crm_leads.expected_revenue * crm_leads.probability / 100)', Measure::FORMAT_CURRENCY),
            'probability'      => new Measure($label('probability'), 'avg(crm_leads.probability)', Measure::FORMAT_PERCENT),
            'days_to_assign'   => new Measure($label('days-to-assign'), 'avg('.Sql::daysBetween('crm_leads.created_at', 'crm_leads.assigned_at').')', Measure::FORMAT_DAYS),
            'days_to_close'    => new Measure($label('days-to-close'), 'avg('.Sql::daysBetween('crm_leads.created_at', 'crm_leads.closed_at').')', Measure::FORMAT_DAYS),
        ];
    }

    public static function presets(): array
    {
        $label = fn (string $key): string => __('crm::filament/clusters/reporting/report.presets.'.$key);

        $presets = [
            'my_pipeline' => [
                'label' => $label('my-pipeline'),
                'query' => fn (Builder $query): Builder => $query->where('crm_leads.user_id', Auth::id()),
            ],
            'unassigned' => [
                'label' => $label('unassigned'),
                'query' => fn (Builder $query): Builder => $query->whereNull('crm_leads.user_id'),
            ],
            'opportunities' => [
                'label' => $label('opportunities'),
                'query' => fn (Builder $query): Builder => $query->where('crm_leads.type', Lead::TYPE_OPPORTUNITY),
            ],
            'leads' => [
                'label' => $label('leads'),
                'query' => fn (Builder $query): Builder => $query->where('crm_leads.type', Lead::TYPE_LEAD),
            ],
            'upcoming_closings' => [
                'label' => $label('upcoming-closings'),
                'query' => fn (Builder $query): Builder => $query->whereDate('crm_leads.expected_closing_date', '>=', today()->startOfMonth()),
            ],
            'active' => [
                'label' => $label('active'),
                'query' => fn (Builder $query): Builder => $query->whereNull('crm_leads.deleted_at'),
            ],
            'ongoing' => [
                'label' => $label('ongoing'),
                'query' => fn (Builder $query): Builder => $query->whereNull('crm_leads.deleted_at')->whereDoesntHave('stage', fn (Builder $query): Builder => $query->where('is_won', true)),
            ],
            'won' => [
                'label' => $label('won'),
                'query' => fn (Builder $query): Builder => $query->whereNull('crm_leads.deleted_at')->whereHas('stage', fn (Builder $query): Builder => $query->where('is_won', true)),
            ],
            'lost' => [
                'label' => $label('lost'),
                'query' => fn (Builder $query): Builder => $query->whereNotNull('crm_leads.deleted_at'),
            ],
            'created_this_year' => [
                'label' => $label('created-this-year'),
                'query' => fn (Builder $query): Builder => $query->whereBetween('crm_leads.created_at', [now()->startOfYear(), now()->endOfYear()]),
            ],
            'closed_this_year' => [
                'label' => $label('closed-this-year'),
                'query' => fn (Builder $query): Builder => $query->whereBetween('crm_leads.closed_at', [now()->startOfYear(), now()->endOfYear()]),
            ],
        ];

        return collect(static::getPresetDefaults())
            ->filter(fn (bool $default, string $key): bool => isset($presets[$key]))
            ->map(fn (bool $default, string $key): array => [...$presets[$key], 'default' => $default])
            ->all();
    }
}
