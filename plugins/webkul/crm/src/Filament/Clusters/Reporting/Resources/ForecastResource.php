<?php

namespace Webkul\Crm\Filament\Clusters\Reporting\Resources;

use Filament\Resources\Resource;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Webkul\Crm\Enums\ViewLayout;
use Webkul\Crm\Filament\Actions\LeadReportBulkActions;
use Webkul\Crm\Filament\Clusters\Reporting;
use Webkul\Crm\Filament\Clusters\Reporting\Resources\ForecastResource\Pages\ListForecast;
use Webkul\Crm\Filament\Clusters\Sales\Resources\OpportunityResource\Pages\ListOpportunities;
use Webkul\Crm\Filament\Concerns\IsCRMReportResource;
use Webkul\Crm\Filament\Exports\ForecastExporter;
use Webkul\Crm\Filament\Support\LeadTableFilters;
use Webkul\Crm\Filament\Support\Reports\ForecastReport;
use Webkul\Crm\Filament\Support\Reports\Tables\LeadReportTable;
use Webkul\Crm\Models\Lead;
use Webkul\Crm\Policies\ForecastPolicy;

class ForecastResource extends Resource
{
    use IsCRMReportResource;

    protected static ?string $model = Lead::class;

    protected static ?string $slug = 'forecast';

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-arrow-trending-up';

    protected static ?string $cluster = Reporting::class;

    protected static bool $isGloballySearchable = false;

    protected static ?int $navigationSort = 1;

    public static function getCRMPolicy(): string
    {
        return ForecastPolicy::class;
    }

    public static function getReport(): string
    {
        return ForecastReport::class;
    }

    public static function getReportTitle(): string
    {
        return __('crm::filament/clusters/reporting/resources/forecast.title');
    }

    public static function getNavigationLabel(): string
    {
        return __('crm::filament/clusters/reporting/resources/forecast.navigation.title');
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->withTrashed()
            ->opportunities()
            ->with(['partner', 'stage', 'user', 'team', 'lostReason', 'country']);
    }

    public static function table(Table $table): Table
    {
        return LeadReportTable::configure($table);
    }

    public static function getReportLayouts(): array
    {
        return [ViewLayout::KANBAN, ViewLayout::GRAPH, ViewLayout::LIST];
    }

    public static function getReportCustomFilters(): array
    {
        return [
            LeadTableFilters::opportunities(),
        ];
    }

    public static function getReportTableViews(): array
    {
        return (new ListOpportunities)->getPresetTableViews();
    }

    public static function getReportBulkActions(): array
    {
        return [
            LeadReportBulkActions::make(ForecastExporter::class),
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListForecast::route('/'),
            'graph' => ListForecast::route('/graph'),
            'list'  => ListForecast::route('/list'),
        ];
    }
}
