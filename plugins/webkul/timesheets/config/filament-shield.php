<?php

use Webkul\Timesheet\Filament\Resources\TimesheetResource;

return [
    'resources' => [
        'manage' => [
            TimesheetResource::class => ['view_any', 'create', 'update', 'delete', 'delete_any'],
        ],
        'exclude' => [],
    ],

];
