<?php

namespace Webkul\PointOfSale\Filament\Admin\Clusters\Reporting\Pages;

use BackedEnum;
use BezhanSalleh\FilamentShield\Traits\HasPageShield;
use Filament\Pages\Page;
use Illuminate\Support\Collection;
use Webkul\PointOfSale\Filament\Admin\Clusters\Reporting;
use Webkul\PointOfSale\Models\Config;
use Webkul\PointOfSale\Services\SalesDetailsReport;

class SalesDetails extends Page
{
    use HasPageShield;

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-document-chart-bar';

    protected static ?string $slug = 'point-of-sale/sales-details';

    protected static ?int $navigationSort = 2;

    protected static ?string $cluster = Reporting::class;

    protected string $view = 'point-of-sale::filament.admin.clusters.reporting.pages.sales-details';

    public ?string $startDate = null;

    public ?string $endDate = null;

    public ?int $configId = null;

    public function mount(): void
    {
        $this->startDate = today()->startOfMonth()->toDateString();

        $this->endDate = today()->toDateString();
    }

    protected static function getPagePermission(): ?string
    {
        return 'page_point_of_sale_sales_details';
    }

    public static function getNavigationLabel(): string
    {
        return __('point-of-sale::filament/admin/clusters/reporting/pages/sales-details.navigation.title');
    }

    public function getTitle(): string
    {
        return __('point-of-sale::filament/admin/clusters/reporting/pages/sales-details.title');
    }

    public function getReport(): array
    {
        return app(SalesDetailsReport::class)->build($this->startDate, $this->endDate, $this->configId);
    }

    public function getTerminals(): Collection
    {
        return Config::query()->orderBy('name')->get();
    }
}
