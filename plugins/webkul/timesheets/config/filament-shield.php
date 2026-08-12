<?php

use Webkul\Timesheet\Filament\Resources\TimesheetResource;

$basic = ['view_any', 'view', 'create', 'update'];
$delete = ['delete', 'delete_any'];

return [
    'resources' => [
        'manage' => [
            TimesheetResource::class => [...array_diff($basic, ['view']), ...$delete],
        ],
        'exclude' => [],
    ],
];
