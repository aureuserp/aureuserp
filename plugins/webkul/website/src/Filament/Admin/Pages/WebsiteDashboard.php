<?php

namespace Webkul\Website\Filament\Admin\Pages;

use App\Models\User;
use BackedEnum;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Pages\Dashboard as BaseDashboard;
use Filament\Schemas\Schema;
use Filament\View\LegacyComponents\Widget;
use Webkul\Website\Filament\Admin\Widgets\BlogAuthorsChart;
use Webkul\Website\Filament\Admin\Widgets\BlogChart;
use Webkul\Website\Filament\Admin\Widgets\BlogStatusPieChart;
use Webkul\Website\Filament\Admin\Widgets\CategoriesPieChart;
use Webkul\Website\Filament\Admin\Widgets\RecentBlogsTable;
use Webkul\Website\Filament\Admin\Widgets\StatsOverview;
use Webkul\Website\Filament\Admin\Widgets\TopCategoriesTable;

class WebsiteDashboard extends BaseDashboard
{
    use BaseDashboard\Concerns\HasFiltersForm;

    protected static string $routePath = 'website';

    protected static string|BackedEnum|null $navigationIcon = null;

    public static function getNavigationIcon(): string|BackedEnum|null
    {
        return null;
    }

    public static function getNavigationLabel(): string
    {
        return 'Website';
    }

    public static function getNavigationGroup(): string
    {
        return 'Dashboard';
    }

    public function filtersForm(Schema $form): Schema
    {
        return $form
            ->schema([
                DatePicker::make('from_date')
                    ->label(__('website::filament/admin/pages/dashboard.from-date'))
                    ->native(false)
                    ->closeOnDateSelection()
                    ->default(now()->subMonth()),

                DatePicker::make('to_date')
                    ->label(__('website::filament/admin/pages/dashboard.to-date'))
                    ->native(false)
                    ->closeOnDateSelection()
                    ->default(now()),

                Select::make('author_id')
                    ->label(__('website::filament/admin/pages/dashboard.author'))
                    ->options(User::query()->pluck('name', 'id'))
                    ->searchable()
                    ->placeholder(__('website::filament/admin/pages/dashboard.all-author')),
            ])
            ->columns(1);
    }

    /**
     * @return array<class-string<Widget> | WidgetConfiguration>
     */
    public function getWidgets(): array
    {
        return [
            StatsOverview::class,
            BlogChart::class,
            CategoriesPieChart::class,
            BlogAuthorsChart::class,
            BlogStatusPieChart::class,
            TopCategoriesTable::class,
            RecentBlogsTable::class,
        ];
    }
}
