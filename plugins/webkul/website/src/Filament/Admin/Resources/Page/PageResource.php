<?php

namespace Webkul\Website\Filament\Admin\Resources\Page;

use BackedEnum;
use Filament\Pages\Enums\SubNavigationPosition;
use Filament\Resources\Pages\Page;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Table;
use Webkul\Website\Filament\Admin\Resources\Page\Pages\CreatePage;
use Webkul\Website\Filament\Admin\Resources\Page\Pages\EditPage;
use Webkul\Website\Filament\Admin\Resources\Page\Pages\ListPages;
use Webkul\Website\Filament\Admin\Resources\Page\Pages\ViewPage;
use Webkul\Website\Filament\Admin\Resources\Page\Schemas\PageForm;
use Webkul\Website\Filament\Admin\Resources\Page\Schemas\PageInfolist;
use Webkul\Website\Filament\Admin\Resources\Page\Tables\PageTable;
use Webkul\Website\Models\Page as PageModel;

class PageResource extends Resource
{
    protected static ?string $model = PageModel::class;

    protected static ?string $slug = 'website/pages';

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-window';

    protected static ?SubNavigationPosition $subNavigationPosition = SubNavigationPosition::Top;

    protected static ?string $recordTitleAttribute = 'title';

    public static function getNavigationLabel(): string
    {
        return __('website::filament/admin/resources/page.navigation.title');
    }

    public static function getNavigationGroup(): string
    {
        return __('website::filament/admin/resources/page.navigation.group');
    }

    public static function form(Schema $schema): Schema
    {
        return PageForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return PageTable::configure($table);
    }

    public static function infolist(Schema $schema): Schema
    {
        return PageInfolist::configure($schema);
    }

    public static function getRecordSubNavigation(Page $page): array
    {
        return $page->generateNavigationItems([
            ViewPage::class,
            EditPage::class,
        ]);
    }

    public static function getPages(): array
    {
        return [
            'index'  => ListPages::route('/'),
            'create' => CreatePage::route('/create'),
            'view'   => ViewPage::route('/{record}'),
            'edit'   => EditPage::route('/{record}/edit'),
        ];
    }
}
