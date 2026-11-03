<?php

namespace Webkul\PointOfSale\Filament\Admin\Clusters\Reporting\Pages;

use BackedEnum;
use BezhanSalleh\FilamentShield\Traits\HasPageShield;
use Filament\Forms\Components\Select;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Pages\Page;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Illuminate\Support\Carbon;
use Webkul\PointOfSale\Filament\Admin\Clusters\Reporting;
use Webkul\PointOfSale\Models\Config;
use Webkul\PointOfSale\Services\SalesDetailsReport;
use Webkul\Support\Filament\Forms\Components\DashboardDateRange;

class SalesDetails extends Page implements HasForms
{
    use HasPageShield, InteractsWithForms;

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-document-chart-bar';

    protected static ?string $slug = 'point-of-sale/sales-details';

    protected static ?int $navigationSort = 2;

    protected static ?string $cluster = Reporting::class;

    protected string $view = 'point-of-sale::filament.admin.clusters.reporting.pages.sales-details';

    public ?array $data = [];

    public function mount(): void
    {
        $start = request()->query('startDate') ?: today()->startOfMonth()->toDateString();

        $end = request()->query('endDate') ?: today()->toDateString();

        $configId = request()->query('configId');

        $this->filtersForm->fill([
            'startDate'  => Carbon::parse($start)->toDateString(),
            'endDate'    => Carbon::parse($end)->toDateString(),
            'date_range' => Carbon::parse($start)->format('d/m/Y').' - '.Carbon::parse($end)->format('d/m/Y'),
            'configId'   => filled($configId) ? (int) $configId : null,
        ]);
    }

    protected function getForms(): array
    {
        return ['filtersForm'];
    }

    public function filtersForm(Schema $schema): Schema
    {
        return $schema
            ->statePath('data')
            ->components([
                Section::make()
                    ->columns([
                        'default' => 1,
                        'sm'      => 2,
                        'lg'      => 3,
                    ])
                    ->schema([
                        ...DashboardDateRange::make(
                            __('point-of-sale::filament/admin/clusters/reporting/pages/sales-details.filters.date-range'),
                        ),

                        Select::make('configId')
                            ->label(__('point-of-sale::filament/admin/clusters/reporting/pages/sales-details.filters.terminal'))
                            ->options(fn (): array => Config::query()->orderBy('name')->pluck('name', 'id')->all())
                            ->placeholder(__('point-of-sale::filament/admin/clusters/reporting/pages/sales-details.filters.all-terminals'))
                            ->native(false)
                            ->searchable()
                            ->preload()
                            ->live(),
                    ]),
            ]);
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
        return app(SalesDetailsReport::class)->build(
            $this->data['startDate'] ?? null,
            $this->data['endDate'] ?? null,
            $this->data['configId'] ?? null,
        );
    }
}
