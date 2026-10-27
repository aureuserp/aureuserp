<?php

namespace Webkul\Crm\Filament\Clusters\Reporting\Resources;

use Filament\Resources\Resource;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Webkul\Crm\Filament\Actions\LeadReportBulkActions;
use Webkul\Crm\Filament\Clusters\Reporting;
use Webkul\Crm\Filament\Clusters\Reporting\Resources\PipelineAnalysisResource\Pages\GraphPipelineAnalysis;
use Webkul\Crm\Filament\Clusters\Reporting\Resources\PipelineAnalysisResource\Pages\ListPipelineAnalysis;
use Webkul\Crm\Filament\Clusters\Reporting\Resources\PipelineAnalysisResource\Pages\SummaryPipelineAnalysis;
use Webkul\Crm\Filament\Clusters\Sales\Resources\OpportunityResource\Pages\ListOpportunities;
use Webkul\Crm\Filament\Concerns\IsCRMReportResource;
use Webkul\Crm\Filament\Exports\OpportunityExporter;
use Webkul\Crm\Filament\Support\LeadTableFilters;
use Webkul\Crm\Filament\Support\Reports\PipelineReport;
use Webkul\Crm\Filament\Support\Reports\Tables\LeadReportTable;
use Webkul\Crm\Models\Lead;
use Webkul\Crm\Policies\PipelineAnalysisPolicy;
use Webkul\TableViews\Filament\Components\PresetView;

class PipelineAnalysisResource extends Resource
{
    use IsCRMReportResource;

    protected static ?string $model = Lead::class;

    protected static ?string $slug = 'pipeline-analysis';

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-funnel';

    protected static ?string $cluster = Reporting::class;

    protected static bool $isGloballySearchable = false;

    protected static ?int $navigationSort = 2;

    public static function getCRMPolicy(): string
    {
        return PipelineAnalysisPolicy::class;
    }

    public static function getReport(): string
    {
        return PipelineReport::class;
    }

    public static function getReportTitle(): string
    {
        return __('crm::filament/clusters/reporting/resources/pipeline-analysis.title');
    }

    public static function getNavigationLabel(): string
    {
        return __('crm::filament/clusters/reporting/resources/pipeline-analysis.navigation.title');
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

    public static function getReportCustomFilters(): array
    {
        return [
            LeadTableFilters::opportunities(),
        ];
    }

    public static function getReportTableViews(): array
    {
        return array_map(
            fn (PresetView $view): PresetView => $view->setAsDefault(false),
            (new ListOpportunities)->getPresetTableViews(),
        );
    }

    public static function getReportBulkActions(): array
    {
        return [
            LeadReportBulkActions::make(OpportunityExporter::class),
        ];
    }

    public static function getPages(): array
    {
        return [
            'index'   => GraphPipelineAnalysis::route('/'),
            'summary' => SummaryPipelineAnalysis::route('/summary'),
            'list'    => ListPipelineAnalysis::route('/list'),
        ];
    }
}
