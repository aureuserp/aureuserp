<?php

return [
    'resources' => [
        'manage' => [
            \Webkul\Project\Filament\Clusters\Configurations\Resources\MilestoneResource::class    => ['view_any', 'view', 'create', 'update', 'delete', 'delete_any'],
            \Webkul\Project\Filament\Clusters\Configurations\Resources\TagResource::class          => ['view_any', 'view', 'create', 'update', 'delete', 'restore', 'delete_any', 'force_delete', 'force_delete_any', 'restore_any'],
            \Webkul\Project\Filament\Clusters\Configurations\Resources\ActivityPlanResource::class => ['view_any', 'view', 'create', 'update', 'delete', 'restore', 'delete_any', 'force_delete', 'force_delete_any', 'restore_any'],
        ],
        'exclude' => [],
    ],

    'pages' => [
        'exclude' => [
            \Webkul\Project\Filament\Clusters\Configurations::class,
        ],
    ],

];
