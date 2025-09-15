<?php

namespace Webkul\Blog\Filament\Admin\Resources\PostResource\Pages;

use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;
use Webkul\Blog\Filament\Admin\Resources\PostResource;
use Webkul\TableViews\Filament\Components\PresetView;
use Webkul\TableViews\Filament\Concerns\HasTableViews;

class ListPosts extends ListRecords
{
    use HasTableViews;

    protected static string $resource = PostResource::class;

    public function getPresetTableViews(): array
    {
        return [
            'my_posts' => PresetView::make(__('blogs::filament/admin/resources/post/pages/list-posts.tabs.my-posts'))
                ->icon('heroicon-s-user')
                ->favorite()
                ->modifyQueryUsing(function (Builder $query) {
                    return $query->where('author_id', Auth::id());
                }),

            'archived' => PresetView::make(__('blogs::filament/admin/resources/post/pages/list-posts.tabs.archived'))
                ->icon('heroicon-s-archive-box')
                ->favorite()
                ->modifyQueryUsing(function ($query) {
                    return $query->onlyTrashed();
                }),
        ];
    }

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make()
                ->label(__('blogs::filament/admin/resources/post/pages/list-posts.header-actions.create.label'))
                ->icon('heroicon-o-plus-circle'),
        ];
    }
}
