<?php

use Webkul\Crm\Filament\Clusters\Configurations;
use Webkul\Crm\Filament\Clusters\Configurations\Resources\ActivityPlanResource;
use Webkul\Crm\Filament\Clusters\Configurations\Resources\ActivityTypeResource;
use Webkul\Crm\Filament\Clusters\Configurations\Resources\CompanyResource;
use Webkul\Crm\Filament\Clusters\Configurations\Resources\LostReasonResource;
use Webkul\Crm\Filament\Clusters\Configurations\Resources\StageResource;
use Webkul\Crm\Filament\Clusters\Configurations\Resources\TagResource;
use Webkul\Crm\Filament\Clusters\Configurations\Resources\TeamResource;
use Webkul\Crm\Filament\Clusters\Configurations\Resources\UserResource;
use Webkul\Crm\Filament\Clusters\Leads;
use Webkul\Crm\Filament\Clusters\Leads\Resources\LeadResource;
use Webkul\Crm\Filament\Clusters\Sales;
use Webkul\Crm\Filament\Clusters\Sales\Resources\ActivityResource;
use Webkul\Crm\Filament\Clusters\Sales\Resources\ActivityResource\Widgets\ActivitiesByStatusChart;
use Webkul\Crm\Filament\Clusters\Sales\Resources\ActivityResource\Widgets\ActivitiesByTypeChart;
use Webkul\Crm\Filament\Clusters\Sales\Resources\ActivityResource\Widgets\ActivitiesByUserChart;
use Webkul\Crm\Filament\Clusters\Sales\Resources\ActivityResource\Widgets\ActivityStatsOverview;
use Webkul\Crm\Filament\Clusters\Sales\Resources\ContactResource;
use Webkul\Crm\Filament\Clusters\Sales\Resources\OpportunityResource;
use Webkul\Crm\Filament\Clusters\Sales\Resources\TeamResource as SalesTeamResource;

$basic = ['view_any', 'view', 'create', 'update'];
$delete = ['delete', 'delete_any'];
$forceDelete = ['force_delete', 'force_delete_any'];
$restore = ['restore', 'restore_any'];
$reorder = ['reorder'];

return [
    'resources' => [
        'manage' => [
            LeadResource::class         => [...$basic, ...$delete, ...$restore, ...$forceDelete],
            ActivityResource::class     => ['view_any', 'view', 'update', 'delete', 'delete_any'],
            OpportunityResource::class  => [...$basic, ...$delete, ...$restore, ...$forceDelete],
            StageResource::class        => [...$basic, ...$delete, ...$reorder],
            ContactResource::class      => [...$basic, ...$delete, ...$restore, ...$forceDelete],
            TagResource::class          => [...$basic, ...$delete],
            LostReasonResource::class   => [...$basic, ...$delete, ...$restore, ...$forceDelete],
            ActivityTypeResource::class => [...$basic, ...$delete, ...$restore, ...$forceDelete, ...$reorder],
            ActivityPlanResource::class => [...$basic, ...$delete, ...$restore, ...$forceDelete],
            UserResource::class         => [...$basic, ...$delete, ...$restore, ...$forceDelete],
            CompanyResource::class      => [...$basic, ...$delete, ...$restore, ...$forceDelete, ...$reorder],
            TeamResource::class         => [...$basic, ...$delete, ...$restore, ...$forceDelete, ...$reorder],
        ],
        'exclude' => [
            SalesTeamResource::class,
        ],
    ],

    'pages' => [
        'exclude' => [
            Sales::class,
            Leads::class,
            Configurations::class,
        ],
    ],

    'widgets' => [
        'exclude' => [
            ActivityStatsOverview::class,
            ActivitiesByStatusChart::class,
            ActivitiesByTypeChart::class,
            ActivitiesByUserChart::class,
        ],
    ],
];
