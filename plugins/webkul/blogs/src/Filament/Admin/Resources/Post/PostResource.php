<?php

namespace Webkul\Blog\Filament\Admin\Resources\Post;

use BackedEnum;
use Filament\Pages\Enums\SubNavigationPosition;
use Filament\Resources\Pages\Page;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Table;
use Webkul\Blog\Filament\Admin\Resources\Post\Pages\CreatePost;
use Webkul\Blog\Filament\Admin\Resources\Post\Pages\EditPost;
use Webkul\Blog\Filament\Admin\Resources\Post\Pages\ListPosts;
use Webkul\Blog\Filament\Admin\Resources\Post\Pages\ViewPost;
use Webkul\Blog\Filament\Admin\Resources\Post\Schemas\PostForm;
use Webkul\Blog\Filament\Admin\Resources\Post\Schemas\PostInfolist;
use Webkul\Blog\Filament\Admin\Resources\Post\Tables\PostTable;
use Webkul\Blog\Models\Post;

class PostResource extends Resource
{
    protected static ?string $model = Post::class;

    protected static ?string $slug = 'website/posts';

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-newspaper';

    protected static ?SubNavigationPosition $subNavigationPosition = SubNavigationPosition::Top;

    protected static ?string $recordTitleAttribute = 'title';

    public static function getNavigationLabel(): string
    {
        return __('blogs::filament/admin/resources/post.navigation.title');
    }

    public static function getNavigationGroup(): string
    {
        return __('blogs::filament/admin/resources/post.navigation.group');
    }

    public static function form(Schema $schema): Schema
    {
        return PostForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return PostTable::configure($table);
    }

    public static function infolist(Schema $schema): Schema
    {
        return PostInfolist::configure($schema);
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
