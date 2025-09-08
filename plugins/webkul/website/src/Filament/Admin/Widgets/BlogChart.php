<?php

namespace Webkul\Website\Filament\Admin\Widgets;

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

    protected static ?string $maxHeight = '250px';

    protected static ?int $sort = 1;

    public function getColumnSpan(): string
    {
        return 'full'; // Full width
    }

    protected function getData(): array
    {
        $filters = $this->filters;

        $query = Post::query()
            ->select(
                DB::raw('DATE_FORMAT(published_at, "%Y-%m") as month'),
                DB::raw('is_published'),
                DB::raw('COUNT(*) as count')
            );

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

        $posts = $query
            ->groupBy('month', 'is_published')
            ->orderBy('month')
            ->get();

        // Collect all months
        $months = $posts->pluck('month')->unique()->values();

        // Prepare datasets
        $publishedData = [];
        $draftData = [];

        foreach ($months as $month) {
            $publishedCount = $posts
                ->where('month', $month)
                ->where('is_published', true)
                ->sum('count');

            $draftCount = $posts
                ->where('month', $month)
                ->where('is_published', false)
                ->sum('count');

            $publishedData[] = $publishedCount;
            $draftData[] = $draftCount;
        }

        return [
            'datasets' => [
                [
                    'label'           => 'Published',
                    'data'            => $publishedData,
                    'backgroundColor' => 'rgba(76, 175, 80, 0.6)', // Green
                    'borderColor'     => '#4CAF50',
                ],
                [
                    'label'           => 'Draft',
                    'data'            => $draftData,
                    'backgroundColor' => 'rgba(244, 67, 54, 0.6)', // Red
                    'borderColor'     => '#F44336',
                ],
            ],
            'labels' => $months,
        ];
    }

    protected function getType(): string
    {
        return 'bar'; // Bar chart
    }
}
