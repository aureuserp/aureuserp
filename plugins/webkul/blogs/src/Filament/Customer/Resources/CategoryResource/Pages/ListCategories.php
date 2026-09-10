<?php

namespace Webkul\Blog\Filament\Customer\Resources\CategoryResource\Pages;

use Filament\Resources\Pages\ListRecords;
use Illuminate\Contracts\Support\Htmlable;
use Webkul\Blog\Filament\Customer\Concerns\ListsBlogPosts;
use Webkul\Blog\Filament\Customer\Resources\CategoryResource;

class ListCategories extends ListRecords
{
    use ListsBlogPosts;

    protected static string $resource = CategoryResource::class;

    protected string $view = 'blogs::filament.customer.resources.category.pages.list-records';

    public function getTitle(): string|Htmlable
    {
        return __('blogs::filament/customer/resources/post/pages/list-records.navigation.title');
    }

    public function getBreadcrumbs(): array
    {
        return [];
    }
}
