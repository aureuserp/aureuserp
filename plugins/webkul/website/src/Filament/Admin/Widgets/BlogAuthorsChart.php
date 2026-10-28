<?php

namespace Webkul\Website\Filament\Admin\Widgets;

use Filament\Widgets\Concerns\InteractsWithPageFilters;
use Illuminate\Support\Facades\DB;
use Webkul\Blog\Models\Post;
use Webkul\Support\Filament\Widgets\DashboardChartWidget;

class BlogAuthorsChart extends DashboardChartWidget
{
    use InteractsWithPageFilters;

    protected static ?int $sort = 3;

    protected ?string $maxHeight = '250px';

    public function getHeading(): ?string
    {
        return __('website::filament/admin/widgets/blog-chart.top-10-authors-count-by-blog-count');
    }

    protected function getData(): array
    {
        $filters = $this->filters;

        $query = Post::query()
            ->select(
                'author_id',
                DB::raw('COUNT(*) as total_blogs')
            )
            ->whereNotNull('author_id')
            ->with('author:id,name');

        if (! empty($filters['from_date'])) {
            $query->whereDate('created_at', '>=', $filters['from_date']);
        }

        if (! empty($filters['to_date'])) {
            $query->whereDate('created_at', '<=', $filters['to_date']);
        }

        if (! empty($filters['author_id'])) {
            $query->where('author_id', $filters['author_id']);
        }

        $authors = $query
            ->groupBy('author_id')
            ->orderByDesc('total_blogs')
            ->limit(10)
            ->get();

        $labels = $authors->map(fn ($post) => $post->author?->name ?? __('website::filament/admin/widgets/blog-chart.no-data-available'))->toArray();

        $data = $authors->pluck('total_blogs')->toArray();

        if (empty($labels)) {
            $labels = [__('website::filament/admin/widgets/blog-chart.no-data-available')];

            $data = [0];
        }

        $ramp = $this->chartRamp();

        return [
            'datasets' => [
                [
                    'label'           => __('website::filament/admin/widgets/blog-chart.number-of-blogs'),
                    'data'            => $data,
                    'backgroundColor' => array_map(fn (int $index): string => $ramp[$index % count($ramp)], array_keys($data)),
                    'borderWidth'     => 0,
                ],
            ],
            'labels' => $labels,
        ];
    }

    protected function getOptions(): array
    {
        return $this->doughnutChartOptions();
    }

    protected function getType(): string
    {
        return 'pie';
    }
}
