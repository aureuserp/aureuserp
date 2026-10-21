<?php

namespace Webkul\Crm\Filament\Clusters\Reporting\Resources;

use Filament\Resources\Resource;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Webkul\Crm\Filament\Clusters\Reporting;
use Webkul\Crm\Filament\Clusters\Reporting\Resources\ForecastResource\Pages\GraphForecast;
use Webkul\Crm\Filament\Clusters\Reporting\Resources\ForecastResource\Pages\KanbanForecast;
use Webkul\Crm\Filament\Clusters\Reporting\Resources\ForecastResource\Pages\ListForecast;
use Webkul\Crm\Filament\Clusters\Reporting\Resources\ForecastResource\Pages\SummaryForecast;
use Webkul\Crm\Filament\Concerns\IsCrmReportResource;
use Webkul\Crm\Filament\Exports\ForecastExporter;
use Webkul\Crm\Filament\Support\Reports\ForecastReport;
use Webkul\Crm\Filament\Support\Reports\Tables\LeadReportTable;
use Webkul\Crm\Models\Lead;
use Webkul\Crm\Policies\ForecastPolicy;

class ForecastResource extends Resource
{
    use IsCrmReportResource;

    protected static ?string $model = Lead::class;

    protected static ?string $slug = 'forecast';

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-arrow-trending-up';

    protected static ?string $cluster = Reporting::class;

    protected static bool $isGloballySearchable = false;

    protected static ?int $navigationSort = 1;

    public static function getCrmPolicy(): string
    {
        return ForecastPolicy::class;
    }

    public static function getReport(): string
    {
        return ForecastReport::class;
    }

    public static function getExporter(): ?string
    {
        return ForecastExporter::class;
    }

    public static function getReportKey(): string
    {
        return 'forecast';
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

    public static function getReportViews(): array
    {
        return [
            'index'   => ['heroicon-o-view-columns', 'kanban'],
            'graph'   => ['heroicon-o-chart-bar', 'graph'],
            'summary' => ['heroicon-o-table-cells', 'summary'],
            'list'    => ['heroicon-o-list-bullet', 'list'],
        ];
    }

    public static function getPages(): array
    {
        return [
            'index'   => KanbanForecast::route('/'),
            'graph'   => GraphForecast::route('/graph'),
            'summary' => SummaryForecast::route('/summary'),
            'list'    => ListForecast::route('/list'),
        ];
    }
}
