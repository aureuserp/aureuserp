<?php

use Webkul\Blog\Filament\Admin\Clusters\Configurations\Resources\CategoryResource;
use Webkul\Blog\Filament\Admin\Clusters\Configurations\Resources\TagResource;
use Webkul\Blog\Filament\Admin\Resources\PostResource;
use Webkul\Blog\Filament\Customer\Resources\CategoryResource as BlogCategoryResource;
use Webkul\Blog\Filament\Customer\Resources\PostResource as BlogPostResource;

$basic = ['view_any', 'view', 'create', 'update'];
$delete = ['delete', 'delete_any'];
$forceDelete = ['force_delete', 'force_delete_any'];
$restore = ['restore', 'restore_any'];
$reorder = ['reorder'];

return [
    'resources' => [
        'manage' => [
            CategoryResource::class     => [...$basic, ...$delete, ...$restore, ...$forceDelete],
            TagResource::class          => [...$basic, ...$delete, ...$restore, ...$forceDelete, ...$reorder],
            PostResource::class         => [...$basic, ...$delete, ...$restore, ...$forceDelete],
            BlogCategoryResource::class => [...$basic, ...$delete, ...$restore, ...$forceDelete],
            BlogPostResource::class     => [...$basic, ...$delete, ...$restore, ...$forceDelete],
        ],
        'exclude' => [],
    ],
];
