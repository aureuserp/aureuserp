<?php

namespace Webkul\Crm\Filament\Support\Reports;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;
use Webkul\Crm\Enums\LeadType;
use Webkul\Crm\Enums\MeasureFormat;
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
        return [
            'salesperson'      => Dimension::relation(__('crm::filament/clusters/reporting/report.dimensions.salesperson'), 'crm_leads.user_id', 'user', User::class),
            'team'             => Dimension::relation(__('crm::filament/clusters/reporting/report.dimensions.team'), 'crm_leads.team_id', 'team', Team::class),
            'stage'            => Dimension::relation(__('crm::filament/clusters/reporting/report.dimensions.stage'), 'crm_leads.stage_id', 'stage', Stage::class, orderAttribute: 'sort'),
            'type'             => Dimension::options(__('crm::filament/clusters/reporting/report.dimensions.type'), 'crm_leads.type', 'type', LeadType::options()),
            'city'             => Dimension::attribute(__('crm::filament/clusters/reporting/report.dimensions.city'), 'crm_leads.city', 'city'),
            'country'          => Dimension::relation(__('crm::filament/clusters/reporting/report.dimensions.country'), 'crm_leads.country_id', 'country', Country::class),
            'lost_reason'      => Dimension::relation(__('crm::filament/clusters/reporting/report.dimensions.lost-reason'), 'crm_leads.lost_reason_id', 'lostReason', LostReason::class),
            'company'          => Dimension::relation(__('crm::filament/clusters/reporting/report.dimensions.company'), 'crm_leads.company_id', 'company', Company::class),
            'campaign'         => Dimension::relation(__('crm::filament/clusters/reporting/report.dimensions.campaign'), 'crm_leads.utm_campaign_id', 'campaign', UtmCampaign::class),
            'medium'           => Dimension::relation(__('crm::filament/clusters/reporting/report.dimensions.medium'), 'crm_leads.utm_medium_id', 'medium', UTMMedium::class),
            'source'           => Dimension::relation(__('crm::filament/clusters/reporting/report.dimensions.source'), 'crm_leads.utm_source_id', 'source', UTMSource::class),
            'expected_closing' => Dimension::month(__('crm::filament/clusters/reporting/report.dimensions.expected-closing'), 'crm_leads.expected_closing_date', 'expected_closing_date'),
            'created_at'       => Dimension::month(__('crm::filament/clusters/reporting/report.dimensions.created-at'), 'crm_leads.created_at', 'created_at'),
            'converted_at'     => Dimension::month(__('crm::filament/clusters/reporting/report.dimensions.converted-at'), 'crm_leads.converted_at', 'converted_at'),
            'closed_at'        => Dimension::month(__('crm::filament/clusters/reporting/report.dimensions.closed-at'), 'crm_leads.closed_at', 'closed_at'),
        ];
    }

    public static function measures(): array
    {
        return [
            'count'            => new Measure(__('crm::filament/clusters/reporting/report.measures.count'), 'count(*)'),
            'expected_revenue' => new Measure(__('crm::filament/clusters/reporting/report.measures.expected-revenue'), 'sum(crm_leads.expected_revenue)', MeasureFormat::CURRENCY),
            'prorated_revenue' => new Measure(__('crm::filament/clusters/reporting/report.measures.prorated-revenue'), 'sum(crm_leads.expected_revenue * crm_leads.probability / 100)', MeasureFormat::CURRENCY),
            'probability'      => new Measure(__('crm::filament/clusters/reporting/report.measures.probability'), 'avg(crm_leads.probability)', MeasureFormat::PERCENT),
            'days_to_assign'   => new Measure(__('crm::filament/clusters/reporting/report.measures.days-to-assign'), 'avg('.Sql::daysBetween('crm_leads.created_at', 'crm_leads.assigned_at').')', MeasureFormat::DAYS),
            'days_to_close'    => new Measure(__('crm::filament/clusters/reporting/report.measures.days-to-close'), 'avg('.Sql::daysBetween('crm_leads.created_at', 'crm_leads.closed_at').')', MeasureFormat::DAYS),
        ];
    }

    public static function presets(): array
    {
        $presets = [
            'my_pipeline' => [
                'label' => __('crm::filament/clusters/reporting/report.presets.my-pipeline'),
                'query' => fn (Builder $query): Builder => $query->where('crm_leads.user_id', Auth::id()),
            ],
            'unassigned' => [
                'label' => __('crm::filament/clusters/reporting/report.presets.unassigned'),
                'query' => fn (Builder $query): Builder => $query->whereNull('crm_leads.user_id'),
            ],
            'opportunities' => [
                'label' => __('crm::filament/clusters/reporting/report.presets.opportunities'),
                'query' => fn (Builder $query): Builder => $query->where('crm_leads.type', LeadType::OPPORTUNITY),
            ],
            'leads' => [
                'label' => __('crm::filament/clusters/reporting/report.presets.leads'),
                'query' => fn (Builder $query): Builder => $query->where('crm_leads.type', LeadType::LEAD),
            ],
            'upcoming_closings' => [
                'label' => __('crm::filament/clusters/reporting/report.presets.upcoming-closings'),
                'query' => fn (Builder $query): Builder => $query->whereDate('crm_leads.expected_closing_date', '>=', today()->startOfMonth()),
            ],
            'active' => [
                'label' => __('crm::filament/clusters/reporting/report.presets.active'),
                'query' => fn (Builder $query): Builder => $query->whereNull('crm_leads.deleted_at'),
            ],
            'ongoing' => [
                'label' => __('crm::filament/clusters/reporting/report.presets.ongoing'),
                'query' => fn (Builder $query): Builder => $query->whereNull('crm_leads.deleted_at')->whereDoesntHave('stage', fn (Builder $query): Builder => $query->where('is_won', true)),
            ],
            'won' => [
                'label' => __('crm::filament/clusters/reporting/report.presets.won'),
                'query' => fn (Builder $query): Builder => $query->whereNull('crm_leads.deleted_at')->whereHas('stage', fn (Builder $query): Builder => $query->where('is_won', true)),
            ],
            'lost' => [
                'label' => __('crm::filament/clusters/reporting/report.presets.lost'),
                'query' => fn (Builder $query): Builder => $query->whereNotNull('crm_leads.deleted_at'),
            ],
            'created_this_year' => [
                'label' => __('crm::filament/clusters/reporting/report.presets.created-this-year'),
                'query' => fn (Builder $query): Builder => $query->whereBetween('crm_leads.created_at', [now()->startOfYear(), now()->endOfYear()]),
            ],
            'closed_this_year' => [
                'label' => __('crm::filament/clusters/reporting/report.presets.closed-this-year'),
                'query' => fn (Builder $query): Builder => $query->whereBetween('crm_leads.closed_at', [now()->startOfYear(), now()->endOfYear()]),
            ],
        ];

        return collect(static::getPresetDefaults())
            ->filter(fn (bool $default, string $key): bool => isset($presets[$key]))
            ->map(fn (bool $default, string $key): array => [...$presets[$key], 'default' => $default])
            ->all();
    }
}
