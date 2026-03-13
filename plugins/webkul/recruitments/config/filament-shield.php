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

$basic = ['view_any', 'view', 'create', 'update'];
$delete = ['delete', 'delete_any'];
$forceDelete = ['force_delete', 'force_delete_any'];
$restore = ['restore', 'restore_any'];
$reorder = ['reorder'];

return [
    'resources' => [
        'manage' => [
            ActivityPlanResource::class => [...$basic, ...$delete, ...$restore, ...$forceDelete],
            ApplicantCategoryResource::class => [...$basic, ...$delete],
            DegreeResource::class => [...$basic, ...$delete, ...$reorder],
            RefuseReasonResource::class => [...$basic, ...$delete, ...$reorder],
            UTMMediumResource::class => [...$basic, ...$delete],
            UTMSourceResource::class => [...$basic, ...$delete],
            SkillTypeResource::class => [...$basic, ...$delete, ...$restore, ...$forceDelete],
            DepartmentResource::class => [...$basic, ...$delete, ...$restore, ...$forceDelete],
            StageResource::class => [...$basic, ...$delete, ...$reorder],
            EmploymentTypeResource::class => [...$basic, ...$delete, ...$reorder],
            JobByPositionResource::class => [...$basic, ...$delete, ...$restore, ...$forceDelete, ...$reorder],
            CandidateResource::class => [...$basic, ...$delete, ...$restore, ...$forceDelete],
            ApplicantResource::class => [...$basic, ...$delete, ...$restore, ...$forceDelete],
            ActivityTypeResource::class => [...$basic, ...$delete, ...$restore, ...$forceDelete, ...$reorder],
            JobPositionResource::class => [...$basic, ...$delete, ...$restore, ...$forceDelete, ...$reorder],
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
