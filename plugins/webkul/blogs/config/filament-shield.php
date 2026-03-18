<?php

use Webkul\Blog\Filament\Admin\Clusters\Configurations\Resources\CategoryResource;
use Webkul\Blog\Filament\Admin\Clusters\Configurations\Resources\TagResource;
use Webkul\Blog\Filament\Admin\Resources\PostResource;
use Webkul\Blog\Filament\Customer\Resources\CategoryResource as BlogCategoryResource;
use Webkul\Blog\Filament\Customer\Resources\PostResource as BlogPostResource;

$permissions = [
    'SOFT_DELETE' => ['view_any', 'view', 'create', 'update', 'delete', 'delete_any', 'restore', 'force_delete', 'force_delete_any', 'restore_any'],
    'FULL' => ['view_any', 'view', 'create', 'update', 'delete', 'delete_any', 'restore', 'force_delete', 'force_delete_any', 'restore_any', 'reorder'],
];

return [
    'resources' => [
        'manage' => [
            CategoryResource::class => $permissions['SOFT_DELETE'],
            TagResource::class => $permissions['FULL'],
            PostResource::class => $permissions['SOFT_DELETE'],
            BlogCategoryResource::class => $permissions['SOFT_DELETE'],
            BlogPostResource::class => $permissions['SOFT_DELETE'],
        ],
        'exclude' => [],
    ],

];
