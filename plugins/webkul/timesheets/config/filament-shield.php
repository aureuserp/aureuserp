<?php

return [
    'resources' => [
        'manage' => [
            \Webkul\Timesheet\Filament\Resources\TimesheetResource::class => ['view_any', 'create', 'update', 'delete', 'delete_any'],
        ],
        'exclude' => [],
    ],

];
