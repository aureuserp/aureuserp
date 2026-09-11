<?php

namespace Webkul\Blog\Filament\Customer\Concerns;

use Illuminate\Contracts\Pagination\Paginator;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Livewire\Attributes\Url;
use Webkul\Blog\Models\Category;
use Webkul\Blog\Models\Post;
use Webkul\Blog\Models\Tag;

trait ListsBlogPosts
{
    #[Url]
    public ?string $tags = null;

    #[Url]
    public ?string $search = null;

    protected ?Collection $cachedActiveTags = null;

    protected function getRecords(): Collection
    {
        return Category::all();
    }

    public function getActiveTags(): Collection
    {
        if ($this->cachedActiveTags !== null) {
            return $this->cachedActiveTags;
        }

        $ids = collect(explode(',', (string) $this->tags))
            ->map(fn (string $id): string => trim($id))
            ->filter(fn (string $id): bool => is_numeric($id))
            ->map(fn (string $id): int => (int) $id)
            ->unique()
            ->all();

        if (! $ids) {
            return $this->cachedActiveTags = new Collection;
        }

        return $this->cachedActiveTags = Tag::whereKey($ids)->ordered()->get();
    }

    public function isTagActive(Tag $tag): bool
    {
        return $this->getActiveTags()->contains(fn (Tag $activeTag): bool => $activeTag->is($tag));
    }

    public function getTagFilterUrl(Tag $tag): string
    {
        $ids = $this->getActiveTags()->modelKeys();

        $ids = $this->isTagActive($tag)
            ? array_values(array_diff($ids, [$tag->getKey()]))
            : [...$ids, $tag->getKey()];

        return $this->getPostsUrl(['tags' => $ids ? implode(',', $ids) : null]);
    }

    public function getClearTagsUrl(): string
    {
        return $this->getPostsUrl(['tags' => null]);
    }

    public function getCategoryFilterUrl(?Category $category): string
    {
        return $this->getPostsUrl([], $category);
    }

    protected function getPostsUrl(array $parameters = [], ?Category $category = null): string
    {
        $parameters = array_filter(
            array_merge([
                'tags'   => implode(',', $this->getActiveTags()->modelKeys()),
                'search' => $this->search,
            ], $parameters),
            fn ($value): bool => $value !== null && $value !== '',
        );

        if (! $category) {
            return static::getResource()::getUrl('index', $parameters);
        }

        return static::getResource()::getUrl('view', array_merge(['record' => $category->slug], $parameters));
    }

    protected function getFilteredCategory(): ?Category
    {
        return null;
    }

    protected function getPosts(): Paginator
    {
        $query = Post::with(['category', 'creator', 'tags'])
            ->where('is_published', true);

        if ($category = $this->getFilteredCategory()) {
            $query->where('category_id', $category->id);
        }

        if ($tagIds = $this->getActiveTags()->modelKeys()) {
            $query->whereHas('tags', fn (Builder $query) => $query->whereKey($tagIds));
        }

        if (filled($this->search)) {
            $locales = array_unique([app()->getLocale(), config('app.fallback_locale', 'en')]);

            $query->where(function (Builder $query) use ($locales) {
                foreach ($locales as $locale) {
                    $query->orWhereLike("title->{$locale}", "%{$this->search}%")
                        ->orWhereLike("content->{$locale}", "%{$this->search}%");
                }
            });
        }

        $query->orderBy('published_at', 'desc');

        return $query->paginate(9);
    }
}
