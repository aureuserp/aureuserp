<?php

use Webkul\Recruitment\Filament\Clusters\Applications;
use Webkul\Recruitment\Filament\Clusters\Applications\Resources\ApplicantResource;
use Webkul\Recruitment\Filament\Clusters\Applications\Resources\CandidateResource;
use Webkul\Recruitment\Filament\Clusters\Applications\Resources\JobByPositionResource;
use Webkul\Recruitment\Filament\Clusters\Configurations;
use Webkul\Recruitment\Filament\Clusters\Configurations\Resources\ActivityPlanResource;
use Webkul\Recruitment\Filament\Clusters\Configurations\Resources\ActivityTypeResource;
use Webkul\Recruitment\Filament\Clusters\Configurations\Resources\ApplicantCategoryResource;
use Webkul\Recruitment\Filament\Clusters\Configurations\Resources\DegreeResource;
use Webkul\Recruitment\Filament\Clusters\Configurations\Resources\DepartmentResource;
use Webkul\Recruitment\Filament\Clusters\Configurations\Resources\EmploymentTypeResource;
use Webkul\Recruitment\Filament\Clusters\Configurations\Resources\JobPositionResource;
use Webkul\Recruitment\Filament\Clusters\Configurations\Resources\RefuseReasonResource;
use Webkul\Recruitment\Filament\Clusters\Configurations\Resources\SkillTypeResource;
use Webkul\Recruitment\Filament\Clusters\Configurations\Resources\StageResource;
use Webkul\Recruitment\Filament\Clusters\Configurations\Resources\UTMMediumResource;
use Webkul\Recruitment\Filament\Clusters\Configurations\Resources\UTMSourceResource;

$permissions = [
    'BASIC' => ['view_any', 'view', 'create', 'update', 'delete', 'delete_any'],
    'REORDER' => ['view_any', 'view', 'create', 'update', 'delete', 'delete_any', 'reorder'],
    'SOFT_DELETE' => ['view_any', 'view', 'create', 'update', 'delete', 'delete_any', 'restore', 'force_delete', 'force_delete_any', 'restore_any'],
    'FULL' => ['view_any', 'view', 'create', 'update', 'delete', 'delete_any', 'restore', 'force_delete', 'force_delete_any', 'restore_any', 'reorder'],
];

return [
    'resources' => [
        'manage' => [
            ActivityPlanResource::class => $permissions['SOFT_DELETE'],
            ApplicantCategoryResource::class => $permissions['BASIC'],
            DegreeResource::class => $permissions['REORDER'],
            RefuseReasonResource::class => $permissions['REORDER'],
            UTMMediumResource::class => $permissions['BASIC'],
            UTMSourceResource::class => $permissions['BASIC'],
            SkillTypeResource::class => $permissions['SOFT_DELETE'],
            DepartmentResource::class => $permissions['SOFT_DELETE'],
            StageResource::class => $permissions['REORDER'],
            EmploymentTypeResource::class => $permissions['REORDER'],
            JobByPositionResource::class => $permissions['FULL'],
            CandidateResource::class => $permissions['SOFT_DELETE'],
            ApplicantResource::class => $permissions['SOFT_DELETE'],
            ActivityTypeResource::class => $permissions['FULL'],
            JobPositionResource::class => $permissions['FULL'],
        ],
        'exclude' => [],
    ],

    'pages' => [
        'exclude' => [
            Applications::class,
            Configurations::class,
        ],
    ],

];
