<?php

namespace Webkul\Blog\Filament\Admin\Resources;

use Filament\Pages\Enums\SubNavigationPosition;
use Filament\Resources\Pages\Page;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Model;
use Webkul\Blog\Filament\Admin\Resources\PostResource\Pages\CreatePost;
use Webkul\Blog\Filament\Admin\Resources\PostResource\Pages\EditPost;
use Webkul\Blog\Filament\Admin\Resources\PostResource\Pages\ListPosts;
use Webkul\Blog\Filament\Admin\Resources\PostResource\Pages\ViewPost;
use Webkul\Blog\Filament\Admin\Resources\PostResource\Schemas\PostForm;
use Webkul\Blog\Filament\Admin\Resources\PostResource\Schemas\PostInfolist;
use Webkul\Blog\Filament\Admin\Resources\PostResource\Tables\PostsTable;
use Webkul\Blog\Models\Post;
use Webkul\Field\Filament\Traits\HasCustomFields;
use Webkul\Support\Enums\NavigationGroup;

class PostResource extends Resource
{
    use HasCustomFields;

    protected static ?string $model = Post::class;

    protected static ?string $slug = 'website/posts';

    protected static ?SubNavigationPosition $subNavigationPosition = SubNavigationPosition::Top;

    protected static ?string $recordTitleAttribute = 'title';

    public static function getNavigationLabel(): string
    {
        return __('blogs::filament/admin/resources/post.navigation.title');
    }

    public static function getNavigationGroup(): string|\UnitEnum
    {
        return NavigationGroup::Website;
    }

    public static function getGloballySearchableAttributes(): array
    {
        return ['title', 'author.name'];
    }

    public static function getGlobalSearchResultDetails(Model $record): array
    {
        return [
            __('blogs::filament/admin/resources/post.global-search.author') => $record->author?->name ?? '—',
        ];
    }

    public static function form(Schema $schema): Schema
    {
        return PostForm::configure($schema, static::getCustomFormFields());
    }

    public static function table(Table $table): Table
    {
        return PostsTable::configure($table, static::getCustomTableColumns(), static::getCustomTableFilters());
    }

    public static function infolist(Schema $schema): Schema
    {
        return PostInfolist::configure($schema, static::getCustomInfolistEntries());
    }

    public static function getRecordSubNavigation(Page $page): array
    {
        return $page->generateNavigationItems([
            ViewPost::class,
            EditPost::class,
        ]);
    }

    public static function getPages(): array
    {
        return [
            'index'  => ListPosts::route('/'),
            'create' => CreatePost::route('/create'),
            'view'   => ViewPost::route('/{record}'),
            'edit'   => EditPost::route('/{record}/edit'),
        ];
    }
}
