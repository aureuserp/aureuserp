<?php

use Webkul\Recruitment\Filament\Clusters\Applications;
use Webkul\Recruitment\Filament\Clusters\Applications\Resources\ApplicantResource;
use Webkul\Recruitment\Filament\Clusters\Applications\Resources\CandidateResource;
use Webkul\Recruitment\Filament\Clusters\Applications\Resources\JobByPositionResource;
use Webkul\Recruitment\Filament\Clusters\Configurations;
use Webkul\Recruitment\Filament\Clusters\Configurations\Resources\ActivityPlanResource;
use Webkul\Recruitment\Filament\Clusters\Configurations\Resources\ApplicantCategoryResource;
use Webkul\Recruitment\Filament\Clusters\Configurations\Resources\DegreeResource;
use Webkul\Recruitment\Filament\Clusters\Configurations\Resources\DepartmentResource;
use Webkul\Recruitment\Filament\Clusters\Configurations\Resources\EmploymentTypeResource;
use Webkul\Recruitment\Filament\Clusters\Configurations\Resources\RefuseReasonResource;
use Webkul\Recruitment\Filament\Clusters\Configurations\Resources\SkillTypeResource;
use Webkul\Recruitment\Filament\Clusters\Configurations\Resources\StageResource;
use Webkul\Recruitment\Filament\Clusters\Configurations\Resources\UTMMediumResource;
use Webkul\Recruitment\Filament\Clusters\Configurations\Resources\UTMSourceResource;

return [
    'resources' => [
        'manage' => [
            ActivityPlanResource::class        => ['view_any', 'view', 'create', 'update', 'delete', 'restore', 'delete_any', 'force_delete', 'force_delete_any', 'restore_any'],
            ApplicantCategoryResource::class   => ['view_any', 'view', 'create', 'update', 'delete', 'delete_any', 'reorder'],
            DegreeResource::class              => ['view_any', 'view', 'create', 'update', 'delete', 'delete_any', 'reorder'],
            RefuseReasonResource::class        => ['view_any', 'view', 'create', 'update', 'delete', 'delete_any', 'reorder'],
            UTMMediumResource::class           => ['view_any', 'view', 'create', 'update', 'delete', 'delete_any'],
            UTMSourceResource::class           => ['view_any', 'view', 'create', 'update', 'delete', 'delete_any'],
            SkillTypeResource::class           => ['view_any', 'view', 'create', 'update', 'delete', 'restore', 'delete_any', 'force_delete', 'force_delete_any', 'restore_any'],
            DepartmentResource::class          => ['view_any', 'view', 'create', 'update', 'delete', 'restore', 'delete_any', 'force_delete', 'force_delete_any', 'restore_any'],
            StageResource::class               => ['view_any', 'view', 'create', 'update', 'delete', 'delete_any', 'reorder'],
            EmploymentTypeResource::class      => ['view_any', 'view', 'create', 'update', 'delete', 'delete_any', 'reorder'],
            JobByPositionResource::class       => ['view_any', 'update'],
            CandidateResource::class           => ['view_any', 'view', 'create', 'update', 'delete', 'delete_any'],
            ApplicantResource::class           => ['view_any', 'view', 'create', 'update', 'delete', 'restore', 'delete_any', 'force_delete', 'force_delete_any', 'restore_any'],
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
