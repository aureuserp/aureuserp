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

    public function getColumnSpan(): string
    {
        return 'full'; // Full width
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
            ->limit(10);
    }

    protected function getTableColumns(): array
    {
        return [
            Tables\Columns\TextColumn::make('title')
                ->label('Title')
                ->searchable()
                ->wrap(),

            Tables\Columns\TextColumn::make('author.name')
                ->label('Author')
                ->default('Unknown')
                ->sortable(),

            Tables\Columns\BadgeColumn::make('is_published')
                ->label('Status')
                ->formatStateUsing(fn ($state) => $state ? 'Published' : 'Draft')
                ->colors([
                    'success' => fn ($state) => $state === true,
                    'danger'  => fn ($state) => $state === false,
                ]),

            Tables\Columns\TextColumn::make('created_at')
                ->label('Created At')
                ->dateTime('M d, Y H:i')
                ->sortable(),
        ];
    }
}
