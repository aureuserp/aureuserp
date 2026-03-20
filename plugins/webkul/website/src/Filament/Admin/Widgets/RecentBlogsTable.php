<?php

namespace Webkul\Website\Filament\Admin\Widgets;

use Filament\Tables;
use Filament\Widgets\Concerns\InteractsWithPageFilters;
use Filament\Widgets\TableWidget;
use Illuminate\Database\Eloquent\Builder;
use Webkul\Blog\Models\Post;

class RecentBlogsTable extends TableWidget
{
    use InteractsWithPageFilters;

    public function getHeading(): ?string
    {
        return __('website::filament/admin/widgets/blog-chart.recent-10-blogs');
    }

    protected static ?int $sort = 6;

    protected function getTableQuery(): Builder
    {
        $filters = $this->filters;

        return Post::query()
            ->with('author')
            ->when(
                ! empty($filters['from_date']),
                fn ($q) => $q->whereDate('created_at', '>=', $filters['from_date'])
            )
            ->when(
                ! empty($filters['to_date']),
                fn ($q) => $q->whereDate('created_at', '<=', $filters['to_date'])
            )
            ->when(
                ! empty($filters['author_id']),
                fn ($q) => $q->where('author_id', $filters['author_id'])
            )
            ->latest()
            ->limit(10);
    }

    protected function getTableColumns(): array
    {
        return [
            Tables\Columns\TextColumn::make('title')
                ->label(__('website::filament/admin/widgets/blog-chart.title'))
                ->searchable()
                ->wrap(),

            Tables\Columns\TextColumn::make('author.name')
                ->label(__('website::filament/admin/widgets/blog-chart.author'))
                ->default('Unknown')
                ->sortable(),

            Tables\Columns\TextColumn::make('is_published')
                ->label(__('website::filament/admin/widgets/blog-chart.status'))
                ->badge()
                ->formatStateUsing(fn ($state) => $state ? __('website::filament/admin/widgets/blog-chart.published') : __('website::filament/admin/widgets/blog-chart.draft'))
                ->colors([
                    'success' => fn ($state) => $state === true,
                    'danger'  => fn ($state) => $state === false,
                ]),

            Tables\Columns\TextColumn::make('created_at')
                ->label(__('website::filament/admin/widgets/blog-chart.created-at'))
                ->dateTime('M d, Y H:i')
                ->sortable(),
        ];
    }
}
