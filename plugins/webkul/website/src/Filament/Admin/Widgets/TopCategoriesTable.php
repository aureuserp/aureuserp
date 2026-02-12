<?php

namespace Webkul\Website\Filament\Admin\Widgets;

use Filament\Tables;
use Filament\Widgets\Concerns\InteractsWithPageFilters;
use Filament\Widgets\TableWidget;
use Illuminate\Database\Eloquent\Builder;
use Webkul\Blog\Models\Category;

class TopCategoriesTable extends TableWidget
{
    use InteractsWithPageFilters;

    public function getHeading(): ?string
    {
        return __('website::filament/admin/widgets/blog-chart.top-5-categories-with-most-blogs');
    }

    protected static ?int $sort = 3;

    protected function getTableQuery(): Builder
    {
        $filters = $this->filters;

        return Category::query()
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
            ->limit(5);
    }

    protected function getTableColumns(): array
    {
        return [
            Tables\Columns\TextColumn::make('name')
                ->label('Category')
                ->searchable()
                ->sortable(),

            Tables\Columns\TextColumn::make('filtered_posts_count')
                ->label('Number of Blogs')
                ->sortable(),
        ];
    }
}
