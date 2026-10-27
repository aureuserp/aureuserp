<?php

use Webkul\Crm\Filament\Clusters\Configurations;
use Webkul\Crm\Filament\Clusters\Configurations\Resources\ActivityPlanResource;
use Webkul\Crm\Filament\Clusters\Configurations\Resources\ActivityTypeResource;
use Webkul\Crm\Filament\Clusters\Configurations\Resources\LostReasonResource;
use Webkul\Crm\Filament\Clusters\Configurations\Resources\StageResource;
use Webkul\Crm\Filament\Clusters\Configurations\Resources\TagResource;
use Webkul\Crm\Filament\Clusters\Configurations\Resources\TeamResource;
use Webkul\Crm\Filament\Clusters\Leads;
use Webkul\Crm\Filament\Clusters\Leads\Resources\LeadResource;
use Webkul\Crm\Filament\Clusters\Reporting;
use Webkul\Crm\Filament\Clusters\Reporting\Resources\ActivityAnalysisResource;
use Webkul\Crm\Filament\Clusters\Reporting\Resources\ForecastResource;
use Webkul\Crm\Filament\Clusters\Reporting\Resources\LeadAnalysisResource;
use Webkul\Crm\Filament\Clusters\Reporting\Resources\PipelineAnalysisResource;
use Webkul\Crm\Filament\Clusters\Sales;
use Webkul\Crm\Filament\Clusters\Sales\Resources\ActivityResource;
use Webkul\Crm\Filament\Clusters\Sales\Resources\ActivityResource\Widgets\ActivitiesByStatusChart;
use Webkul\Crm\Filament\Clusters\Sales\Resources\ActivityResource\Widgets\ActivitiesByTypeChart;
use Webkul\Crm\Filament\Clusters\Sales\Resources\ActivityResource\Widgets\ActivitiesByUserChart;
use Webkul\Crm\Filament\Clusters\Sales\Resources\ActivityResource\Widgets\ActivityStatsOverview;
use Webkul\Crm\Filament\Clusters\Sales\Resources\ContactResource;
use Webkul\Crm\Filament\Clusters\Sales\Resources\OpportunityResource;
use Webkul\Crm\Filament\Clusters\Sales\Resources\QuotationResource;
use Webkul\Crm\Filament\Clusters\Sales\Resources\TeamResource as SalesTeamResource;
use Webkul\Crm\Filament\Support\Reports\Widgets\ReportChart;

$basic = ['view_any', 'view', 'create', 'update'];
$delete = ['delete', 'delete_any'];
$forceDelete = ['force_delete', 'force_delete_any'];
$restore = ['restore', 'restore_any'];
$reorder = ['reorder'];

return [
    'resources' => [
        'manage' => [
            QuotationResource::class        => [...$basic, ...$delete, ...$restore, ...$forceDelete],
            LeadResource::class             => [...$basic, ...$delete, ...$restore, ...$forceDelete],
            ActivityResource::class         => ['view_any', 'view', 'update', 'delete', 'delete_any'],
            OpportunityResource::class      => [...$basic, ...$delete, ...$restore, ...$forceDelete],
            StageResource::class            => [...$basic, ...$delete, ...$reorder],
            ContactResource::class          => [...$basic, ...$delete, ...$restore, ...$forceDelete],
            TagResource::class              => [...$basic, ...$delete],
            LostReasonResource::class       => [...$basic, ...$delete, ...$restore, ...$forceDelete],
            ActivityTypeResource::class     => [...$basic, ...$delete, ...$restore, ...$forceDelete, ...$reorder],
            ActivityPlanResource::class     => [...$basic, ...$delete, ...$restore, ...$forceDelete],
            TeamResource::class             => [...$basic, ...$delete, ...$restore, ...$forceDelete, ...$reorder],
            ForecastResource::class         => ['view_any'],
            PipelineAnalysisResource::class => ['view_any'],
            LeadAnalysisResource::class     => ['view_any'],
            ActivityAnalysisResource::class => ['view_any'],
        ],
        'exclude' => [
            SalesTeamResource::class,
        ],
    ],

    'pages' => [
        'exclude' => [
            Sales::class,
            Leads::class,
            Reporting::class,
            Configurations::class,
        ],
    ],

    'widgets' => [
        'exclude' => [
            ActivityStatsOverview::class,
            ActivitiesByStatusChart::class,
            ActivitiesByTypeChart::class,
            ActivitiesByUserChart::class,
            ReportChart::class,
        ],
    ],
];
