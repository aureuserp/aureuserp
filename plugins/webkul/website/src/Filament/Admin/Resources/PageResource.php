<?php

namespace Webkul\Website\Filament\Admin\Resources;

use Filament\Pages\Enums\SubNavigationPosition;
use Filament\Resources\Pages\Page;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Table;
use Webkul\Field\Filament\Traits\HasCustomFields;
use Webkul\Support\Enums\NavigationGroup;
use Webkul\Website\Filament\Admin\Resources\PageResource\Pages\CreatePage;
use Webkul\Website\Filament\Admin\Resources\PageResource\Pages\EditPage;
use Webkul\Website\Filament\Admin\Resources\PageResource\Pages\ListPages;
use Webkul\Website\Filament\Admin\Resources\PageResource\Pages\ViewPage;
use Webkul\Website\Filament\Admin\Resources\PageResource\Schemas\PageForm;
use Webkul\Website\Filament\Admin\Resources\PageResource\Schemas\PageInfolist;
use Webkul\Website\Filament\Admin\Resources\PageResource\Tables\PagesTable;
use Webkul\Website\Models\Page as PageModel;

class PageResource extends Resource
{
    use HasCustomFields;

    protected static ?string $model = PageModel::class;

    protected static ?string $slug = 'website/pages';

    protected static ?SubNavigationPosition $subNavigationPosition = SubNavigationPosition::Top;

    protected static ?string $recordTitleAttribute = 'title';

    public static function getNavigationLabel(): string
    {
        return __('website::filament/admin/resources/page.navigation.title');
    }

    public static function getNavigationGroup(): string|\UnitEnum
    {
        return NavigationGroup::Website;
    }

    public static function form(Schema $schema): Schema
    {
        return PageForm::configure($schema, static::getCustomFormFields());
    }

    public static function table(Table $table): Table
    {
        return PagesTable::configure($table, static::getCustomTableColumns(), static::getCustomTableFilters());
    }

    public static function infolist(Schema $schema): Schema
    {
        return PageInfolist::configure($schema, static::getCustomInfolistEntries());
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
