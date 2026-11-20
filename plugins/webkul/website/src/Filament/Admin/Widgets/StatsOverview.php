<?php

namespace Webkul\Website\Filament\Admin\Widgets;

use Filament\Widgets\Concerns\InteractsWithPageFilters;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Flowframe\Trend\Trend;
use Flowframe\Trend\TrendValue;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Carbon;
use Webkul\Blog\Models\Post;
use Webkul\PluginManager\Package;
use Webkul\Support\Filament\Widgets\DashboardStatsWidget;
use Webkul\Website\Models\Page;

class StatsOverview extends DashboardStatsWidget
{
    use InteractsWithPageFilters;

    protected static ?int $sort = 2;

    public function getHeading(): ?string
    {
        return __('website::filament/admin/widgets/stats-overview.stats-overview');
    }

    protected function getData(): array
    {
        $filters = $this->filters;

        $pageQuery = Page::query();

        $blogQuery = Post::query();

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
                'totalBlogs'          => Package::isPluginInstalled('blogs') ? $this->getTotalBlog(clone $blogQuery) : 0,
                'totalPublishedBlogs' => Package::isPluginInstalled('blogs') ? $this->getTotalPublishedBlog(clone $blogQuery) : 0,
                'totalDraftBlogs'     => Package::isPluginInstalled('blogs') ? $this->getTotalDraftBlog(clone $blogQuery) : 0,
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
        return $query->count() ?? 0;
    }

    protected function getTotalPublishedBlog($query)
    {
        return $query->where('is_published', true)->count() ?? 0;
    }

    protected function getTotalDraftBlog($query)
    {
        return $query->where('is_published', false)->count() ?? 0;
    }

    protected function getStats(): array
    {
        $data = $this->getData();

        [$start, $end] = $this->period();

        [$previousStart, $previousEnd] = $this->previousPeriod($start, $end);

        $authorId = $this->filters['author_id'] ?? null;

        $stats = [
            $this->trendStat(
                __('website::filament/admin/widgets/stats-overview.total-pages.title'),
                'heroicon-o-document-text',
                'primary',
                $data['totalPagesCount'],
                Page::query(),
                $start,
                $end,
                $previousStart,
                $previousEnd,
            ),
            $this->trendStat(
                __('website::filament/admin/widgets/stats-overview.total-pages-publish.title'),
                'heroicon-o-check-circle',
                'success',
                $data['totalPublishPageCount'],
                Page::query()->where('is_published', true),
                $start,
                $end,
                $previousStart,
                $previousEnd,
            ),
            $this->trendStat(
                __('website::filament/admin/widgets/stats-overview.total-pages-draft.title'),
                'heroicon-o-pencil-square',
                'warning',
                $data['totalDraftPageCount'],
                Page::query()->where('is_published', false),
                $start,
                $end,
                $previousStart,
                $previousEnd,
            ),
        ];

        if (Package::isPluginInstalled('blogs')) {
            $stats[] = $this->trendStat(
                __('website::filament/admin/widgets/stats-overview.total-blogs.title'),
                'heroicon-o-newspaper',
                'info',
                $data['blogs']['totalBlogs'],
                Post::query()->when($authorId, fn (Builder $q) => $q->where('author_id', $authorId)),
                $start,
                $end,
                $previousStart,
                $previousEnd,
            );

            $stats[] = $this->trendStat(
                __('website::filament/admin/widgets/stats-overview.total-blogs-publish.title'),
                'heroicon-o-check-badge',
                'primary',
                $data['blogs']['totalPublishedBlogs'],
                Post::query()->where('is_published', true)->when($authorId, fn (Builder $q) => $q->where('author_id', $authorId)),
                $start,
                $end,
                $previousStart,
                $previousEnd,
            );

            $stats[] = $this->trendStat(
                __('website::filament/admin/widgets/stats-overview.total-blogs-draft.title'),
                'heroicon-o-document',
                'danger',
                $data['blogs']['totalDraftBlogs'],
                Post::query()->where('is_published', false)->when($authorId, fn (Builder $q) => $q->where('author_id', $authorId)),
                $start,
                $end,
                $previousStart,
                $previousEnd,
            );
        }

        return $stats;
    }

    protected function trendStat(string $label, string $icon, string $color, int $value, Builder $base, Carbon $start, Carbon $end, Carbon $previousStart, Carbon $previousEnd): Stat
    {
        $series = $this->monthlySeries(clone $base, $start, $end);

        $current = (float) (clone $base)->whereBetween('created_at', [$start, $end])->count();

        $previous = (float) (clone $base)->whereBetween('created_at', [$previousStart, $previousEnd])->count();

        return $this->themedStat($label, $icon, $color, number_format($value), $current, $previous, $series);
    }

    /**
     * @return list<int>
     */
    protected function monthlySeries(Builder $query, Carbon $start, Carbon $end): array
    {
        return Trend::query($query)
            ->between(start: $start, end: $end)
            ->perMonth()
            ->count()
            ->map(fn (TrendValue $value): int => (int) $value->aggregate)
            ->all();
    }

    /**
     * @return array{0: Carbon, 1: Carbon}
     */
    protected function period(): array
    {
        $filters = $this->filters;

        $start = ! empty($filters['from_date'])
            ? Carbon::parse($filters['from_date'])->startOfDay()
            : now()->startOfYear();

        $end = ! empty($filters['to_date'])
            ? Carbon::parse($filters['to_date'])->endOfDay()
            : now()->endOfDay();

        return [$start, $end];
    }

    /**
     * @return array{0: Carbon, 1: Carbon}
     */
    protected function previousPeriod(Carbon $start, Carbon $end): array
    {
        $length = $start->diffInDays($end) + 1;

        $previousEnd = (clone $start)->subDay()->endOfDay();

        return [(clone $previousEnd)->subDays($length - 1)->startOfDay(), $previousEnd];
    }
}
