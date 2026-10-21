<?php

namespace Webkul\PointOfSale\Filament\Admin\Clusters\Reporting\Pages;

use BackedEnum;
use BezhanSalleh\FilamentShield\Traits\HasPageShield;
use Filament\Pages\Page;
use Illuminate\Support\Collection;
use Livewire\Attributes\Url;
use Webkul\PointOfSale\Filament\Admin\Clusters\Reporting;
use Webkul\PointOfSale\Models\Config;
use Webkul\PointOfSale\Services\OrderAnalysisReport;

class OrderReport extends Page
{
    use HasPageShield;

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-chart-bar';

    protected static ?string $slug = 'point-of-sale/order-report';

    protected static ?int $navigationSort = 1;

    protected static ?string $cluster = Reporting::class;

    protected string $view = 'point-of-sale::filament.admin.clusters.reporting.pages.order-report';

    public ?string $startDate = null;

    public ?string $endDate = null;

    #[Url]
    public ?int $configId = null;

    public string $groupBy = 'day';

    public function mount(): void
    {
        $this->startDate = today()->startOfMonth()->toDateString();

        $this->endDate = today()->toDateString();
    }

    protected static function getPagePermission(): ?string
    {
        return 'page_point_of_sale_order_report';
    }

    public static function getNavigationLabel(): string
    {
        return __('point-of-sale::filament/admin/clusters/reporting/pages/order-report.navigation.title');
    }

    public function getTitle(): string
    {
        return __('point-of-sale::filament/admin/clusters/reporting/pages/order-report.title');
    }

    public function getReport(): array
    {
        return app(OrderAnalysisReport::class)->build(
            $this->startDate,
            $this->endDate,
            $this->configId,
            $this->groupBy,
        );
    }

    public function getTerminals(): Collection
    {
        return Config::query()->orderBy('name')->get();
    }
}
