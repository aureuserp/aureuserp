<?php

namespace Webkul\Website\Filament\Admin\Widgets;

use Filament\Widgets\Concerns\InteractsWithPageFilters;
use Webkul\Blog\Models\Category;
use Webkul\Support\Filament\Widgets\DashboardChartWidget;

class CategoriesPieChart extends DashboardChartWidget
{
    use InteractsWithPageFilters;

    protected static ?int $sort = 2;

    protected ?string $maxHeight = '250px';

    public function getHeading(): ?string
    {
        return __('website::filament/admin/widgets/blog-chart.blogs-by-category');
    }

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

        if (empty($labels)) {
            $labels = [__('website::filament/admin/widgets/blog-chart.no-data-available')];

            $data = [0];
        }

        $ramp = $this->chartRamp();

        return [
            'datasets' => [
                [
                    'label'           => __('website::filament/admin/widgets/blog-chart.blogs-by-category'),
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
}
