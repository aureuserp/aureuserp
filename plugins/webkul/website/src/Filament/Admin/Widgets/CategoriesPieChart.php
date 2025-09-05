<?php

namespace Webkul\Website\Filament\Admin\Widgets;

use Filament\Widgets\ChartWidget;
use Filament\Widgets\Concerns\InteractsWithPageFilters;
use Webkul\Blog\Models\Category;

class CategoriesPieChart extends ChartWidget
{
    use InteractsWithPageFilters;

    protected static ?string $heading = 'Blogs by Category';

    protected static ?int $sort = 4;

    protected function getType(): string
    {
        return 'pie'; // Pie chart
    }

    protected function getData(): array
    {
        $filters = $this->filters;

        // 🔍 Get categories with blog counts, applying filters on posts
        $categories = Category::query()
            ->withCount([
                'posts as filtered_posts_count' => function ($query) use ($filters) {
                    if (! empty($filters['from_date'])) {
                        $query->whereDate('created_at', '>=', $filters['from_date']);
                    }

                    if (! empty($filters['to_date'])) {
                        $query->whereDate('created_at', '<=', $filters['to_date']);
                    }

                    if (! empty($filters['author_id'])) {
                        $query->where('author_id', $filters['author_id']);
                    }
                },
            ])
            ->orderByDesc('filtered_posts_count')
            ->get();

        $labels = $categories->pluck('name')->toArray();
        $data = $categories->pluck('filtered_posts_count')->toArray();

        return [
            'datasets' => [
                [
                    'label'           => 'Blogs by Category',
                    'data'            => $data,
                    'backgroundColor' => [
                        '#4CAF50', // Green
                        '#2196F3', // Blue
                        '#FFC107', // Amber
                        '#FF5722', // Orange
                        '#9C27B0', // Purple
                        '#00BCD4', // Cyan
                        '#8BC34A', // Light Green
                        '#FF9800', // Deep Orange
                        '#E91E63', // Pink
                        '#795548', // Brown
                    ],
                ],
            ],
            'labels' => $labels,
        ];
    }
}
