<?php

namespace Webkul\Website\Filament\Admin\Widgets;

use Filament\Widgets\Concerns\InteractsWithPageFilters;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Webkul\Blog\Models\Post;
use Webkul\Website\Models\Page;

class StatsOverview extends BaseWidget
{
    use InteractsWithPageFilters;

    protected static ?int $sort = 2;

    protected int|string|array $columnSpan = 'full';

    public function getHeading(): ?string
    {
        return __('website::filament/admin/pages/dashboard.stats-overview');
    }

    protected function getData(): array
    {
        $filters = $this->filters;

        $pageQuery = Page::query();
        $blogQuery = Post::query();

        // 🔍 Apply filters to blog query
        if (! empty($filters['from_date'])) {
            $blogQuery->whereDate('created_at', '>=', $filters['from_date']);
        }

        if (! empty($filters['to_date'])) {
            $blogQuery->whereDate('created_at', '<=', $filters['to_date']);
        }

        if (! empty($filters['author_id'])) {
            $blogQuery->where('author_id', $filters['author_id']);
        }

        return [
            'totalPagesCount'        => $this->TotalPagesCount(clone $pageQuery),
            'totalPublishPageCount'  => $this->TotalPagesPublishCount(clone $pageQuery),
            'totalDraftPageCount'    => $this->TotalPagesDraftCount(clone $pageQuery),
            'blogs'                  => [
                'totalBlogs'          => $this->getTotalBlog(clone $blogQuery),
                'totalPublishedBlogs' => $this->getTotalPublishedBlog(clone $blogQuery),
                'totalDraftBlogs'     => $this->getTotalDraftBlog(clone $blogQuery),
            ],
        ];
    }

    protected function TotalPagesCount($query)
    {
        return $query->count() ?? 0;
    }

    protected function TotalPagesPublishCount($query)
    {
        return $query->where('is_published', true)->count() ?? 0;
    }

    protected function TotalPagesDraftCount($query)
    {
        return $query->where('is_published', false)->count() ?? 0;
    }

    protected function getTotalBlog($query)
    {
        return $query->count();
    }

    protected function getTotalPublishedBlog($query)
    {
        return $query->where('is_published', true)->count();
    }

    protected function getTotalDraftBlog($query)
    {
        return $query->where('is_published', false)->count();
    }

    protected function getStats(): array
    {
        $data = $this->getData();

        return [
            Stat::make(__('website::filament/admin/widgets/stats-overview.total-pages.title'), $data['totalPagesCount'])
                ->description(__('website::filament/admin/widgets/stats-overview.total-pages.description')),

            Stat::make(__('website::filament/admin/widgets/stats-overview.total-pages-publish.title'), $data['totalPublishPageCount'])
                ->description(__('website::filament/admin/widgets/stats-overview.total-pages-publish.description')),

            Stat::make(__('website::filament/admin/widgets/stats-overview.total-pages-draft.title'), $data['totalDraftPageCount'])
                ->description(__('website::filament/admin/widgets/stats-overview.total-pages-draft.description')),

            Stat::make(__('website::filament/admin/widgets/stats-overview.total-blogs.title'), $data['blogs']['totalBlogs'])
                ->description(__('website::filament/admin/widgets/stats-overview.total-blogs.description')),

            Stat::make(__('website::filament/admin/widgets/stats-overview.total-blogs-publish.title'), $data['blogs']['totalPublishedBlogs'])
                ->description(__('website::filament/admin/widgets/stats-overview.total-blogs-publish.description')),

            Stat::make(__('website::filament/admin/widgets/stats-overview.total-blogs-draft.title'), $data['blogs']['totalDraftBlogs'])
                ->description(__('website::filament/admin/widgets/stats-overview.total-blogs-draft.description')),
        ];
    }
}
