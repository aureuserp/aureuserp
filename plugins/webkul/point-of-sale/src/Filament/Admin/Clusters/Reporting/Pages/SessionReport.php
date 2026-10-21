<?php

namespace Webkul\PointOfSale\Filament\Admin\Clusters\Reporting\Pages;

use BackedEnum;
use BezhanSalleh\FilamentShield\Traits\HasPageShield;
use Filament\Pages\Page;
use Illuminate\Support\Collection;
use Webkul\PointOfSale\Filament\Admin\Clusters\Reporting;
use Webkul\PointOfSale\Models\Session;
use Webkul\PointOfSale\Services\SessionReportBuilder;

class SessionReport extends Page
{
    use HasPageShield;

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-clipboard-document-list';

    protected static ?string $slug = 'point-of-sale/session-report';

    protected static ?int $navigationSort = 3;

    protected static ?string $cluster = Reporting::class;

    protected string $view = 'point-of-sale::filament.admin.clusters.reporting.pages.session-report';

    public ?int $sessionId = null;

    protected static function getPagePermission(): ?string
    {
        return 'page_point_of_sale_session_report';
    }

    public static function getNavigationLabel(): string
    {
        return __('point-of-sale::filament/admin/clusters/reporting/pages/session-report.navigation.title');
    }

    public function getTitle(): string
    {
        return __('point-of-sale::filament/admin/clusters/reporting/pages/session-report.title');
    }

    public function getSessions(): Collection
    {
        return Session::query()->latest('id')->limit(50)->get();
    }

    public function getReport(): ?array
    {
        return $this->sessionId
            ? app(SessionReportBuilder::class)->build($this->sessionId)
            : null;
    }
}
