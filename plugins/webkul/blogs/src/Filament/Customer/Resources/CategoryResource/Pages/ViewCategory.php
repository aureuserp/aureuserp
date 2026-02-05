<?php

namespace Webkul\Blog\Filament\Customer\Resources\CategoryResource\Pages;

use Filament\Resources\Pages\ViewRecord;
use Illuminate\Contracts\Pagination\Paginator;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Webkul\Blog\Filament\Customer\Resources\CategoryResource;
use Webkul\Blog\Models\Category;
use Webkul\Blog\Models\Post;

class ViewCategory extends ViewRecord
{
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

    protected function getRecords(): Collection
    {
        return Category::all();
    }

    protected function getPosts(): Paginator
    {
        $query = Post::with(['category', 'creator', 'tags'])
            ->where('category_id', $this->getRecord()->id)
            ->where('is_published', 1);

        if (request()->has('search') && $search = request()->input('search')) {
            $query->where(function (Builder $query) use ($search) {
                $query->where('title', 'like', "%{$search}%")
                    ->orWhere('content', 'like', "%{$search}%");
            });
        }

        $query->orderBy('published_at', 'desc');

        return $query->paginate(9);
    }
}
