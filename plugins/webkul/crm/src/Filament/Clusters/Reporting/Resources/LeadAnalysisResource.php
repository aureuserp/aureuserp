<?php

namespace Webkul\Crm\Filament\Clusters\Reporting\Resources;

use Filament\Resources\Resource;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;
use Webkul\Crm\Enums\LeadType;
use Webkul\Crm\Filament\Actions\LeadReportBulkActions;
use Webkul\Crm\Filament\Clusters\Reporting;
use Webkul\Crm\Filament\Clusters\Reporting\Resources\LeadAnalysisResource\Pages\ListLeadAnalysis;
use Webkul\Crm\Filament\Concerns\IsCRMReportResource;
use Webkul\Crm\Filament\Exports\LeadExporter;
use Webkul\Crm\Filament\Support\LeadTableFilters;
use Webkul\Crm\Filament\Support\Reports\LeadsAnalysisReport;
use Webkul\Crm\Filament\Support\Reports\Tables\LeadReportTable;
use Webkul\Crm\Models\Lead;
use Webkul\Crm\Policies\LeadAnalysisPolicy;
use Webkul\TableViews\Filament\Components\PresetView;

class LeadAnalysisResource extends Resource
{
    use IsCRMReportResource;

    protected static ?string $model = Lead::class;

    protected static ?string $slug = 'lead-analysis';

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-user-plus';

    protected static ?string $cluster = Reporting::class;

    protected static bool $isGloballySearchable = false;

    protected static ?int $navigationSort = 3;

    public static function getCRMPolicy(): string
    {
        return LeadAnalysisPolicy::class;
    }

    public static function getReport(): string
    {
        return LeadsAnalysisReport::class;
    }

    public static function getReportTitle(): string
    {
        return __('crm::filament/clusters/reporting/resources/lead-analysis.title');
    }

    public static function getNavigationLabel(): string
    {
        return __('crm::filament/clusters/reporting/resources/lead-analysis.navigation.title');
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

    public static function getReportCustomFilters(): array
    {
        return [
            LeadTableFilters::leads(),
        ];
    }

    public static function getReportTableViews(): array
    {
        return [
            'leads' => PresetView::make(__('crm::filament/clusters/reporting/report.tabs.leads'))
                ->icon('heroicon-s-user-plus')
                ->favorite()
                ->setAsDefault()
                ->modifyQueryUsing(fn (Builder $query): Builder => $query->where('type', LeadType::LEAD)),

            'opportunities' => PresetView::make(__('crm::filament/clusters/reporting/report.tabs.opportunities'))
                ->icon('heroicon-s-funnel')
                ->favorite()
                ->modifyQueryUsing(fn (Builder $query): Builder => $query->where('type', LeadType::OPPORTUNITY)),

            'mine' => PresetView::make(__('crm::filament/clusters/reporting/report.tabs.mine'))
                ->icon('heroicon-s-user')
                ->favorite()
                ->modifyQueryUsing(fn (Builder $query): Builder => $query->where('user_id', Auth::id())),

            'won' => PresetView::make(__('crm::filament/clusters/reporting/report.tabs.won'))
                ->icon('heroicon-s-trophy')
                ->favorite()
                ->modifyQueryUsing(fn (Builder $query): Builder => $query->withoutTrashed()->won()),

            'lost' => PresetView::make(__('crm::filament/clusters/reporting/report.tabs.lost'))
                ->icon('heroicon-s-hand-thumb-down')
                ->favorite()
                ->modifyQueryUsing(fn (Builder $query): Builder => $query->onlyTrashed()),
        ];
    }

    public static function getReportBulkActions(): array
    {
        return [
            LeadReportBulkActions::make(LeadExporter::class),
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListLeadAnalysis::route('/'),
            'list'  => ListLeadAnalysis::route('/list'),
        ];
    }
}
