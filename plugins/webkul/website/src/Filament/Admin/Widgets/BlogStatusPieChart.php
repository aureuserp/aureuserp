<?php

namespace Webkul\Website\Filament\Admin\Widgets;

use Filament\Widgets\Concerns\InteractsWithPageFilters;
use Webkul\Blog\Models\Post;
use Webkul\Support\Filament\Widgets\DashboardChartWidget;

class BlogStatusPieChart extends DashboardChartWidget
{
    use InteractsWithPageFilters;

    protected static ?int $sort = 4;

    protected ?string $maxHeight = '250px';

    public function getHeading(): ?string
    {
        return __('website::filament/admin/widgets/blog-chart.blogs-published-vs-draft');
    }

    protected function getType(): string
    {
        return 'pie';
    }

    protected function getData(): array
    {
        $filters = $this->filters;

        $query = Post::query();

        if (! empty($filters['from_date'])) {
            $query->whereDate('created_at', '>=', $filters['from_date']);
        }

        if (! empty($filters['to_date'])) {
            $query->whereDate('created_at', '<=', $filters['to_date']);
        }

        if (! empty($filters['author_id'])) {
            $query->where('author_id', $filters['author_id']);
        }

        $publishedCount = (clone $query)->where('is_published', true)->count();

        $draftCount = (clone $query)->where('is_published', false)->count();

        if ($publishedCount === 0 && $draftCount === 0) {
            return [
                'datasets' => [
                    [
                        'label'           => __('website::filament/admin/widgets/blog-chart.status'),
                        'data'            => [0],
                        'backgroundColor' => [$this->brandColor('gray')],
                        'borderWidth'     => 0,
                    ],
                ],

                'labels' => [__('website::filament/admin/widgets/blog-chart.no-data-available')],
            ];
        }

        return [
            'datasets' => [
                [
                    'label'           => __('website::filament/admin/widgets/blog-chart.status'),
                    'data'            => [$publishedCount, $draftCount],
                    'backgroundColor' => [
                        $this->brandColor('success'),
                        $this->brandColor('danger'),
                    ],
                    'borderWidth'     => 0,
                ],
            ],

            'labels' => [
                __('website::filament/admin/widgets/blog-chart.published'),
                __('website::filament/admin/widgets/blog-chart.draft'),
            ],
        ];
    }

    protected function getOptions(): array
    {
        return $this->doughnutChartOptions();
    }
}
