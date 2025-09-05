<?php

namespace Webkul\Website\Filament\Admin\Widgets;

use Filament\Widgets\ChartWidget;
use Filament\Widgets\Concerns\InteractsWithPageFilters;
use Webkul\Blog\Models\Post;

class BlogStatusPieChart extends ChartWidget
{
    use InteractsWithPageFilters;

    protected static ?string $heading = 'Blogs: Published vs Draft';

    protected static ?int $sort = 5;

    protected function getType(): string
    {
        return 'donut';
    }

    protected function getData(): array
    {
        $filters = $this->filters;

        $query = Post::query();

        // 🔍 Apply filters
        if (! empty($filters['from_date'])) {
            $query->whereDate('created_at', '>=', $filters['from_date']);
        }

        if (! empty($filters['to_date'])) {
            $query->whereDate('created_at', '<=', $filters['to_date']);
        }

        if (! empty($filters['author_id'])) {
            $query->where('author_id', $filters['author_id']);
        }

        // Clone queries for counts
        $publishedCount = (clone $query)->where('is_published', true)->count();
        $draftCount = (clone $query)->where('is_published', false)->count();

        return [
            'datasets' => [
                [
                    'label'           => 'Blogs',
                    'data'            => [$publishedCount, $draftCount],
                    'backgroundColor' => [
                        '#4CAF50', // Green for Published
                        '#F44336', // Red for Draft
                    ],
                ],
            ],
            'labels' => ['Published', 'Draft'],
        ];
    }
}
