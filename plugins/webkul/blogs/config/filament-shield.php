<?php

return [
    'resources' => [
        'manage' => [
            \Webkul\Blog\Filament\Admin\Clusters\Configurations\Resources\CategoryResource::class => ['view_any', 'create', 'update', 'delete', 'restore', 'delete_any', 'force_delete', 'force_delete_any', 'restore_any'],
            \Webkul\Blog\Filament\Admin\Clusters\Configurations\Resources\TagResource::class      => ['view_any', 'create', 'update', 'delete', 'restore', 'delete_any', 'force_delete', 'force_delete_any', 'restore_any'],
            \Webkul\Blog\Filament\Admin\Resources\PostResource::class                             => ['view_any', 'view', 'create', 'update', 'delete', 'restore', 'delete_any', 'force_delete', 'force_delete_any', 'restore_any'],
        ],
        'exclude' => [],
    ],

];
