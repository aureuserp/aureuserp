<?php

namespace Webkul\Inventory\Filament\Clusters\Operations\Resources\Scraps;

use BackedEnum;
use Filament\Pages\Enums\SubNavigationPosition;
use Filament\Resources\Pages\Page;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Table;
use Webkul\Inventory\Filament\Clusters\Operations;
use Webkul\Inventory\Filament\Clusters\Operations\Resources\Scraps\Pages\CreateScrap;
use Webkul\Inventory\Filament\Clusters\Operations\Resources\Scraps\Pages\EditScrap;
use Webkul\Inventory\Filament\Clusters\Operations\Resources\Scraps\Pages\ListScraps;
use Webkul\Inventory\Filament\Clusters\Operations\Resources\Scraps\Pages\ManageMoves;
use Webkul\Inventory\Filament\Clusters\Operations\Resources\Scraps\Pages\ViewScrap;
use Webkul\Inventory\Filament\Clusters\Operations\Resources\Scraps\Schemas\ScrapForm;
use Webkul\Inventory\Filament\Clusters\Operations\Resources\Scraps\Schemas\ScrapInfolist;
use Webkul\Inventory\Filament\Clusters\Operations\Resources\Scraps\Tables\ScrapsTable;
use Webkul\Inventory\Models\Scrap;

class ScrapResource extends Resource
{
    protected static ?string $model = Scrap::class;

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-trash';

    protected static ?int $navigationSort = 5;

    protected static ?string $cluster = Operations::class;

    protected static ?\Filament\Pages\Enums\SubNavigationPosition $subNavigationPosition = SubNavigationPosition::Top;

    public static function getNavigationLabel(): string
    {
        return __('inventories::filament/clusters/operations/resources/scrap.navigation.title');
    }

    public static function getNavigationGroup(): string
    {
        return __('inventories::filament/clusters/operations/resources/scrap.navigation.group');
    }

    public static function form(Schema $schema): Schema
    {
        return ScrapForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return ScrapsTable::configure($table);
    }

    public static function infolist(Schema $schema): Schema
    {
        return ScrapInfolist::configure($schema);
    }

    public static function getRecordSubNavigation(Page $page): array
    {
        return $page->generateNavigationItems([
            ViewScrap::class,
            EditScrap::class,
            ManageMoves::class,
        ]);
    }

    public static function getPages(): array
    {
        return [
            'index'  => ListScraps::route('/'),
            'create' => CreateScrap::route('/create'),
            'view'   => ViewScrap::route('/{record}/view'),
            'edit'   => EditScrap::route('/{record}/edit'),
            'moves'  => ManageMoves::route('/{record}/moves'),
        ];
    }
}
