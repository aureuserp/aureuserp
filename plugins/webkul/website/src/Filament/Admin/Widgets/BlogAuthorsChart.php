<?php

namespace Webkul\Website\Filament\Admin\Widgets;

use Filament\Widgets\ChartWidget;
use Filament\Widgets\Concerns\InteractsWithPageFilters;
use Illuminate\Support\Facades\DB;
use Webkul\Blog\Models\Post;

class BlogAuthorsChart extends ChartWidget
{
    use InteractsWithPageFilters;

    public function getHeading(): ?string
    {
        return __('website::filament/admin/widgets/blog-chart.top-10-authors-count-by-blog-count');
    }

    protected static ?int $sort = 3;

    protected ?string $maxHeight = '250px';

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

        // Apply filters
        if (! empty($filters['from_date'])) {
            $query->whereDate('created_at', '>=', $filters['from_date']);
        }

        if (! empty($filters['to_date'])) {
            $query->whereDate('created_at', '<=', $filters['to_date']);
        }

        if (! empty($filters['author_id'])) {
            $query->where('author_id', $filters['author_id']);
        }

        // Group by author & get top 10
        $authors = $query
            ->groupBy('author_id')
            ->orderByDesc('total_blogs')
            ->limit(10)
            ->get();

        $labels = $authors->map(fn ($post) => $post->author?->name ?? 'Unknown')->toArray();
        $data = $authors->pluck('total_blogs')->toArray();

        return [
            'datasets' => [
                [
                    'label'           => 'Number of Blogs',
                    'data'            => $data,
                    'backgroundColor' => [
                        '#4CAF50',
                        '#2196F3',
                        '#FFC107',
                        '#FF5722',
                        '#9C27B0',
                        '#00BCD4',
                        '#8BC34A',
                        '#FF9800',
                        '#E91E63',
                        '#795548',
                    ],
                    'borderColor' => '#2196F3',
                ],
            ],
            'labels' => $labels,
        ];
    }

    protected function getType(): string
    {
        return 'pie'; // Pie chart
    }
}
