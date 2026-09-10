<?php

namespace Webkul\Blog\Filament\Customer\Resources\CategoryResource\Pages;

use Filament\Resources\Pages\ViewRecord;
use Illuminate\Contracts\Support\Htmlable;
use Webkul\Blog\Filament\Customer\Concerns\ListsBlogPosts;
use Webkul\Blog\Filament\Customer\Resources\CategoryResource;
use Webkul\Blog\Models\Category;

class ViewCategory extends ViewRecord
{
    use ListsBlogPosts;

    protected static string $resource = CategoryResource::class;

    protected string $view = 'blogs::filament.customer.resources.category.pages.view-record';

    public function getBreadcrumbs(): array
    {
        return [];
    }

    public function getTitle(): string|Htmlable
    {
        return __('blogs::filament/customer/resources/category/pages/view-category.navigation.title');
    }

    protected function getFilteredCategory(): ?Category
    {
        return $this->getRecord();
    }
}
