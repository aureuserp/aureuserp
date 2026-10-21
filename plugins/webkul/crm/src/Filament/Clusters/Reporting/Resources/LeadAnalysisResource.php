<?php

namespace Webkul\Crm\Filament\Clusters\Reporting\Resources;

use Filament\Resources\Resource;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Webkul\Crm\Filament\Clusters\Reporting;
use Webkul\Crm\Filament\Clusters\Reporting\Resources\LeadAnalysisResource\Pages\GraphLeadAnalysis;
use Webkul\Crm\Filament\Clusters\Reporting\Resources\LeadAnalysisResource\Pages\ListLeadAnalysis;
use Webkul\Crm\Filament\Clusters\Reporting\Resources\LeadAnalysisResource\Pages\SummaryLeadAnalysis;
use Webkul\Crm\Filament\Concerns\IsCrmReportResource;
use Webkul\Crm\Filament\Support\Reports\LeadsAnalysisReport;
use Webkul\Crm\Filament\Support\Reports\Tables\LeadReportTable;
use Webkul\Crm\Models\Lead;
use Webkul\Crm\Policies\LeadAnalysisPolicy;

class LeadAnalysisResource extends Resource
{
    use IsCrmReportResource;

    protected static ?string $model = Lead::class;

    protected static ?string $slug = 'lead-analysis';

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-user-plus';

    protected static ?string $cluster = Reporting::class;

    protected static bool $isGloballySearchable = false;

    protected static ?int $navigationSort = 3;

    public static function getCrmPolicy(): string
    {
        return LeadAnalysisPolicy::class;
    }

    public static function getReport(): string
    {
        return LeadsAnalysisReport::class;
    }

    public static function getReportKey(): string
    {
        return 'lead-analysis';
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->withTrashed()
            ->with(['partner', 'stage', 'user', 'team', 'lostReason', 'country']);
    }

    public static function table(Table $table): Table
    {
        return LeadReportTable::configure($table);
    }

    public static function getPages(): array
    {
        return [
            'index'   => GraphLeadAnalysis::route('/'),
            'summary' => SummaryLeadAnalysis::route('/summary'),
            'list'    => ListLeadAnalysis::route('/list'),
        ];
    }
}
