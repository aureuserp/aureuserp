<?php

namespace Webkul\Crm\Filament\Support\Reports;

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
}
