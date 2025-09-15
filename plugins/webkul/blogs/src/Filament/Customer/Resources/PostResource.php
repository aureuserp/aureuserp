<?php

namespace Webkul\Blog\Filament\Customer\Resources;

use Filament\Resources\Resource;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Webkul\Blog\Filament\Customer\Resources\PostResource\Pages\ViewPost;
use Webkul\Blog\Models\Post;

class PostResource extends Resource
{
    public static ?string $parentResource = CategoryResource::class;

    protected static ?string $model = Post::class;

    protected static ?string $recordRouteKeyName = 'slug';

    protected static ?string $recordTitleAttribute = 'title';

    protected static bool $shouldRegisterNavigation = false;

    protected static bool $shouldSkipAuthorization = true;

    public static function getGloballySearchableAttributes(): array
    {
        return ['title', 'category.name'];
    }

    public static function getGlobalSearchResultDetails(Model $record): array
    {
        return [
            __('blogs::filament/customer/resources/post.global-search.category') => $record->category?->name ?? '—',
        ];
    }

    public static function getGlobalSearchResultUrl(Model $record): string
    {
        return CategoryResource::getUrl('posts.view', ['parent' => $record->category->slug, 'record' => $record->slug]);
    }

    public static function getGlobalSearchEloquentQuery(): Builder
    {
        return parent::getGlobalSearchEloquentQuery()->with(['category'])->where('is_published', true);
    }

    public static function getPages(): array
    {
        return [
            'view' => ViewPost::route('/{record}'),
        ];
    }
}
