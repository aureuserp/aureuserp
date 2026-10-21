<?php

namespace Webkul\Crm\Filament\Clusters\Reporting\Resources;

use Filament\Resources\Resource;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Webkul\Crm\Filament\Clusters\Reporting;
use Webkul\Crm\Filament\Clusters\Reporting\Resources\PipelineAnalysisResource\Pages\GraphPipelineAnalysis;
use Webkul\Crm\Filament\Clusters\Reporting\Resources\PipelineAnalysisResource\Pages\ListPipelineAnalysis;
use Webkul\Crm\Filament\Clusters\Reporting\Resources\PipelineAnalysisResource\Pages\SummaryPipelineAnalysis;
use Webkul\Crm\Filament\Concerns\IsCrmReportResource;
use Webkul\Crm\Filament\Support\Reports\PipelineReport;
use Webkul\Crm\Filament\Support\Reports\Tables\LeadReportTable;
use Webkul\Crm\Models\Lead;
use Webkul\Crm\Policies\PipelineAnalysisPolicy;

class PipelineAnalysisResource extends Resource
{
    use IsCrmReportResource;

    protected static ?string $model = Lead::class;

    protected static ?string $slug = 'pipeline-analysis';

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-funnel';

    protected static ?string $cluster = Reporting::class;

    protected static bool $isGloballySearchable = false;

    protected static ?int $navigationSort = 2;

    public static function getCrmPolicy(): string
    {
        return PipelineAnalysisPolicy::class;
    }

    public static function getReport(): string
    {
        return PipelineReport::class;
    }

    public static function getReportKey(): string
    {
        return 'pipeline-analysis';
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

    public static function getPages(): array
    {
        return [
            'index'   => GraphPipelineAnalysis::route('/'),
            'summary' => SummaryPipelineAnalysis::route('/summary'),
            'list'    => ListPipelineAnalysis::route('/list'),
        ];
    }
}
