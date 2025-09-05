<?php

namespace Webkul\Website\Filament\Admin\Pages;

use App\Models\User;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Form;
use Filament\Pages\Dashboard as BaseDashboard;
use Filament\View\LegacyComponents\Widget;
use Webkul\Support\Filament\Clusters\Dashboard as DashboardCluster;
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

    protected static ?string $navigationIcon = 'heroicon-o-folder';

    protected static ?string $cluster = DashboardCluster::class;

    public static function getNavigationLabel(): string
    {
        return 'Website Dashboard';
    }

    public function filtersForm(Form $form): Form
    {
        return $form
            ->schema([
                DatePicker::make('from_date')
                    ->label('From Date')
                    ->native(false)
                    ->closeOnDateSelection()
                    ->default(now()->subMonth()),

                DatePicker::make('to_date')
                    ->label('To Date')
                    ->native(false)
                    ->closeOnDateSelection()
                    ->default(now()),

                Select::make('author_id')
                    ->label('Author')
                    ->options(User::query()->pluck('name', 'id'))
                    ->searchable()
                    ->placeholder('All Authors'),
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
            TopCategoriesTable::class,
            BlogStatusPieChart::class,
            RecentBlogsTable::class,
        ];
    }
}
