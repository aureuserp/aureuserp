<?php

return [
    'resources' => [
        'manage' => [
            \Webkul\Recruitment\Filament\Clusters\Configurations\Resources\ActivityPlanResource::class => ['view_any', 'view', 'create', 'update', 'delete', 'restore', 'delete_any', 'force_delete', 'force_delete_any', 'restore_any'],
            \Webkul\Recruitment\Filament\Clusters\Configurations\Resources\ApplicantCategoryResource::class => ['view_any', 'view', 'create', 'update', 'delete', 'delete_any', 'reorder'],
            \Webkul\Recruitment\Filament\Clusters\Configurations\Resources\DegreeResource::class => ['view_any', 'view', 'create', 'update', 'delete', 'delete_any', 'reorder'],
            \Webkul\Recruitment\Filament\Clusters\Configurations\Resources\RefuseReasonResource::class => ['view_any', 'view', 'create', 'update', 'delete', 'delete_any', 'reorder'],
            \Webkul\Recruitment\Filament\Clusters\Configurations\Resources\UTMMediumResource::class => ['view_any', 'view', 'create', 'update', 'delete', 'delete_any'],
            \Webkul\Recruitment\Filament\Clusters\Configurations\Resources\UTMSourceResource::class => ['view_any', 'view', 'create', 'update', 'delete', 'delete_any'],
            \Webkul\Recruitment\Filament\Clusters\Configurations\Resources\SkillTypeResource::class => ['view_any', 'view', 'create', 'update', 'delete', 'restore', 'delete_any', 'force_delete', 'force_delete_any', 'restore_any'],
            \Webkul\Recruitment\Filament\Clusters\Configurations\Resources\DepartmentResource::class => ['view_any', 'view', 'create', 'update', 'delete', 'restore', 'delete_any', 'force_delete', 'force_delete_any', 'restore_any'],
            \Webkul\Recruitment\Filament\Clusters\Configurations\Resources\StageResource::class => ['view_any', 'view', 'create', 'update', 'delete', 'delete_any', 'reorder'],
            \Webkul\Recruitment\Filament\Clusters\Configurations\Resources\EmploymentTypeResource::class => ['view_any', 'view', 'create', 'update', 'delete', 'delete_any', 'reorder'],
            \Webkul\Recruitment\Filament\Clusters\Applications\Resources\JobByPositionResource::class => ['view_any', 'update'],
            \Webkul\Recruitment\Filament\Clusters\Applications\Resources\CandidateResource::class => ['view_any', 'view', 'create', 'update', 'delete', 'delete_any'],
            \Webkul\Recruitment\Filament\Clusters\Applications\Resources\ApplicantResource::class => ['view_any', 'view', 'create', 'update', 'delete', 'restore', 'delete_any', 'force_delete', 'force_delete_any', 'restore_any'],
        ],
        'exclude' => [],
    ],

    'pages' => [
        'exclude' => [
            \Webkul\Recruitment\Filament\Clusters\Applications::class,
            \Webkul\Recruitment\Filament\Clusters\Configurations::class,
        ],
    ],

];
