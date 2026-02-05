<?php

namespace Webkul\Blog\Filament\Customer\Resources\CategoryResource\Pages;

use Filament\Resources\Pages\ListRecords;
use Illuminate\Contracts\Pagination\Paginator;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Webkul\Blog\Filament\Customer\Resources\CategoryResource;
use Webkul\Blog\Models\Category;
use Webkul\Blog\Models\Post;

class ListCategories extends ListRecords
{
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

    protected function getRecords(): Collection
    {
        return Category::all();
    }

    protected function getPosts(): Paginator
    {
        $query = Post::with(['category', 'creator', 'tags'])
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
