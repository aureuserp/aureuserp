<?php

namespace Webkul\Website\Filament\Admin\Widgets;

use Carbon\Carbon;
use Filament\Widgets\ChartWidget;
use Filament\Widgets\Concerns\InteractsWithPageFilters;
use Illuminate\Support\Facades\DB;
use Webkul\Blog\Models\Post;

class BlogChart extends ChartWidget
{
    use InteractsWithPageFilters;

    public function getHeading(): ?string
    {
        return __('website::filament/admin/widgets/blog-chart.published-blogs-by-month');
    }

    protected ?string $maxHeight = '250px';

    protected static ?int $sort = 1;

    protected function getData(): array
    {
        $filters = $this->filters;

        $query = Post::query()
            ->select(
                DB::raw('DATE_FORMAT(created_at, "%Y-%m") as month_key'),
                'is_published',
                DB::raw('COUNT(*) as count')
            );

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

            $publishedCount = $posts
                ->where('month_key', $monthKey)
                ->where('is_published', 1)
                ->sum('count');

            $draftCount = $posts
                ->where('month_key', $monthKey)
                ->where('is_published', 0)
                ->sum('count');

            $publishedData[] = $publishedCount;
            $draftData[] = $draftCount;
        }

        return [
            'datasets' => [
                [
                    'label'           => 'Published',
                    'data'            => $publishedData,
                    'backgroundColor' => 'rgba(76, 175, 80, 0.6)',
                    'borderColor'     => '#4CAF50',
                ],
                [
                    'label'           => 'Draft',
                    'data'            => $draftData,
                    'backgroundColor' => 'rgba(244, 67, 54, 0.6)',
                    'borderColor'     => '#F44336',
                ],
            ],
            'labels' => $labels,
        ];
    }

    protected function getType(): string
    {
        return 'bar';
    }
}
