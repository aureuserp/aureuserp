<?php

use Webkul\Blog\Filament\Admin\Clusters\Configurations\Resources\CategoryResource;
use Webkul\Blog\Filament\Admin\Clusters\Configurations\Resources\TagResource;
use Webkul\Blog\Filament\Admin\Resources\PostResource;

return [
    'resources' => [
        'manage' => [
            CategoryResource::class                         => ['view_any', 'create', 'update', 'delete', 'restore', 'delete_any', 'force_delete', 'force_delete_any', 'restore_any'],
            TagResource::class                              => ['view_any', 'create', 'update', 'delete', 'restore', 'delete_any', 'force_delete', 'force_delete_any', 'restore_any'],
            PostResource::class                             => ['view_any', 'view', 'create', 'update', 'delete', 'restore', 'delete_any', 'force_delete', 'force_delete_any', 'restore_any'],
        ],
        'exclude' => [],
    ],

];
