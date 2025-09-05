<?php

namespace Webkul\Website\Filament\Admin\Widgets;

use BezhanSalleh\FilamentShield\Traits\HasWidgetShield;
use Filament\Widgets\Concerns\InteractsWithPageFilters;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use phpDocumentor\Reflection\Types\This;
use Webkul\Blog\Models\Post;
use Webkul\Website\Models\Page;

class StatsOverview extends BaseWidget
{
    protected static ?int $sort = 2;

    protected int|string|array $columnSpan = 'full';


    public function getHeading(): ?string
    {
        return __('website::filament/admin/pages/dashboard.stats-overview');
    }


    protected  function getData()
    {

        $query = Page::query();

        $blogQuery = Post::query();

        return [
            'totalPagesCount' => $this->TotalPagesCount($query),
            'totalPublishPageCount' => $this->TotalPagesPublishCount($query),
            'totalDraftPageCount' => $this->TotalPagesDraftCount($query),
            'blogs' => [
                'totalBlogs' => $this->getTotalBlog($blogQuery),
                'totalPublishedBlogs' => $this->getTotalPublishedlBlog($blogQuery),
                'totalDraftBlogs' => $this->getTotalDraftlBlog($blogQuery),
            ]
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

    protected function getTotalPublishedlBlog($query)
    {

        return $query->where('is_published', true)->count();
    }

    protected function getTotalDraftlBlog($query)
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
                
            Stat::make(__('website::filament/admin/widgets/stats-overview.total-pages-draft.title'), $data['totalDraftPageCount'])
                ->description(__('website::filament/admin/widgets/stats-overview.total-pages-draft.description')),
        ];
    }
}
