<?php

namespace Webkul\Website\Filament\Admin\Widgets;

use Filament\Widgets\ChartWidget;
use Filament\Widgets\Concerns\InteractsWithPageFilters;
use Webkul\Blog\Models\Category;

class CategoriesPieChart extends ChartWidget
{
    use InteractsWithPageFilters;

    public function getHeading(): ?string
    {
        return __('website::filament/admin/widgets/blog-chart.blogs-by-category');
    }

    protected static ?int $sort = 2;

    protected ?string $maxHeight = '250px';

    protected function getType(): string
    {
        return 'pie';
    }

    protected function getData(): array
    {
        $filters = $this->filters;

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
                        '#4CAF50', '#2196F3', '#FFC107', '#FF5722', '#9C27B0',
                        '#00BCD4', '#8BC34A', '#FF9800', '#E91E63', '#795548',
                    ],
                ],
            ],
            'labels' => $labels,
        ];
    }
}
