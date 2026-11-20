<?php

namespace Webkul\Website\Filament\Admin\Widgets;

use Carbon\Carbon;
use Filament\Widgets\Concerns\InteractsWithPageFilters;
use Webkul\Blog\Models\Post;
use Webkul\Support\Filament\Widgets\DashboardChartWidget;

class BlogChart extends DashboardChartWidget
{
    use InteractsWithPageFilters;

    protected ?string $maxHeight = '250px';

    protected static ?int $sort = 1;

    public function getHeading(): ?string
    {
        return __('website::filament/admin/widgets/blog-chart.published-blogs-by-month');
    }

    protected function getData(): array
    {
        $filters = $this->filters;

        $query = Post::query()
            ->selectRaw(db_dialect()->monthBucket('created_at').' as month_key')
            ->addSelect('is_published')
            ->selectRaw('COUNT(*) as count');

        if (! empty($filters['from_date'])) {
            $query->whereDate('created_at', '>=', $filters['from_date']);
        }

        if (! empty($filters['to_date'])) {
            $query->whereDate('created_at', '<=', $filters['to_date']);
        }

        if (! empty($filters['author_id'])) {
            $query->where('author_id', $filters['author_id']);
        }

        $posts = $query
            ->groupBy('month_key', 'is_published')
            ->orderBy('month_key')
            ->get();

        $months = $posts->pluck('month_key')->unique()->sort()->values();

        $publishedData = [];
        $draftData = [];
        $labels = [];

        foreach ($months as $monthKey) {
            $labels[] = Carbon::createFromFormat('Y-m', $monthKey)->format('M Y');

            $monthPosts = $posts->where('month_key', $monthKey);

            $publishedData[] = (int) $monthPosts->where('is_published', true)->sum('count');

            $draftData[] = (int) $monthPosts->where('is_published', false)->sum('count');
        }

        if (empty($labels)) {
            $publishedData = [0];

            $draftData = [0];

            $labels = [__('website::filament/admin/widgets/blog-chart.no-data-available')];
        }

        return [
            'datasets' => [
                [
                    'label'           => __('website::filament/admin/widgets/blog-chart.published'),
                    'data'            => $publishedData,
                    'backgroundColor' => $this->brandColor('success', 500, 0.55),
                    'borderColor'     => $this->brandColor('success'),
                    'borderRadius'    => 8,
                    'borderSkipped'   => 'bottom',
                    'maxBarThickness' => 34,
                ],
                [
                    'label'           => __('website::filament/admin/widgets/blog-chart.draft'),
                    'data'            => $draftData,
                    'backgroundColor' => $this->brandColor('danger', 500, 0.55),
                    'borderColor'     => $this->brandColor('danger'),
                    'borderRadius'    => 8,
                    'borderSkipped'   => 'bottom',
                    'maxBarThickness' => 34,
                ],
            ],

            'labels' => $labels,
        ];
    }

    protected function getOptions(): array
    {
        $options = $this->barChartOptions(12);

        $options['plugins']['legend'] = [
            'display'  => true,
            'position' => 'bottom',
            'labels'   => [
                'color'         => $this->brandColor('gray', 500),
                'boxWidth'      => 10,
                'boxHeight'     => 10,
                'usePointStyle' => true,
                'padding'       => 14,
            ],
        ];

        return $options;
    }

    protected function getType(): string
    {
        return 'bar';
    }
}
