<?php

use Webkul\Project\Filament\Clusters\Configurations;
use Webkul\Project\Filament\Clusters\Configurations\Resources\ActivityPlanResource;
use Webkul\Project\Filament\Clusters\Configurations\Resources\MilestoneResource;
use Webkul\Project\Filament\Clusters\Configurations\Resources\TagResource;

return [
    'resources' => [
        'manage' => [
            MilestoneResource::class    => ['view_any', 'view', 'create', 'update', 'delete', 'delete_any'],
            TagResource::class          => ['view_any', 'view', 'create', 'update', 'delete', 'restore', 'delete_any', 'force_delete', 'force_delete_any', 'restore_any'],
            ActivityPlanResource::class => ['view_any', 'view', 'create', 'update', 'delete', 'restore', 'delete_any', 'force_delete', 'force_delete_any', 'restore_any'],
        ],
        'exclude' => [],
    ],

    'pages' => [
        'exclude' => [
            Configurations::class,
        ],
    ],

];
