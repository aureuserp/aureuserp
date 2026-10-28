<?php

namespace Webkul\Website\Filament\Admin\Widgets;

use Filament\Support\Enums\FontWeight;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Widgets\Concerns\InteractsWithPageFilters;
use Filament\Widgets\TableWidget;
use Illuminate\Database\Eloquent\Builder;
use Webkul\Blog\Models\Post;

class RecentBlogsTable extends TableWidget
{
    use InteractsWithPageFilters;

    protected static ?int $sort = 6;

    public function getHeading(): ?string
    {
        return __('website::filament/admin/widgets/blog-chart.recent-10-blogs');
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordClasses('dashboard-table-row');
    }

    protected function isTablePaginationEnabled(): bool
    {
        return false;
    }

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
            ->limit(5);
    }

    protected function getTableColumns(): array
    {
        return [
            Tables\Columns\TextColumn::make('title')
                ->label(__('website::filament/admin/widgets/blog-chart.title'))
                ->weight(FontWeight::Bold)
                ->color('primary')
                ->description(fn ($record): string => $record->author?->name ?: '—')
                ->wrap(),

            Tables\Columns\TextColumn::make('is_published')
                ->label(__('website::filament/admin/widgets/blog-chart.status'))
                ->badge()
                ->formatStateUsing(fn ($state) => $state ? __('website::filament/admin/widgets/blog-chart.published') : __('website::filament/admin/widgets/blog-chart.draft'))
                ->color(fn ($state): string => $state ? 'success' : 'danger'),

            Tables\Columns\TextColumn::make('created_at')
                ->label(__('website::filament/admin/widgets/blog-chart.created-at'))
                ->dateTime('M d, Y H:i')
                ->sortable()
                ->alignEnd(),
        ];
    }
}
