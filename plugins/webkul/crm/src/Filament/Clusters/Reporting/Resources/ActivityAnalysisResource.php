<?php

namespace Webkul\Crm\Filament\Clusters\Reporting\Resources;

use Filament\Actions\BulkActionGroup;
use Filament\Resources\Resource;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Auth;
use Webkul\Crm\Enums\LeadType;
use Webkul\Crm\Filament\Actions\RecordBulkActions;
use Webkul\Crm\Filament\Clusters\Reporting;
use Webkul\Crm\Filament\Clusters\Reporting\Resources\ActivityAnalysisResource\Pages\ListActivityAnalysis;
use Webkul\Crm\Filament\Concerns\IsCRMReportResource;
use Webkul\Crm\Filament\Exports\ActivityLogExporter;
use Webkul\Crm\Filament\Support\Reports\ActivityReport;
use Webkul\Crm\Filament\Support\Reports\Tables\ActivityLogTable;
use Webkul\Crm\Models\ActivityLog;
use Webkul\Crm\Policies\ActivityAnalysisPolicy;
use Webkul\TableViews\Filament\Components\PresetView;

class ActivityAnalysisResource extends Resource
{
    use IsCRMReportResource;

    protected static ?string $model = ActivityLog::class;

    protected static ?string $slug = 'activity-analysis';

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-clipboard-document-check';

    protected static ?string $cluster = Reporting::class;

    protected static bool $isGloballySearchable = false;

    protected static ?int $navigationSort = 4;

    public static function getCRMPolicy(): string
    {
        return ActivityAnalysisPolicy::class;
    }

    public static function getReport(): string
    {
        return ActivityReport::class;
    }

    public static function getReportTitle(): string
    {
        return __('crm::filament/clusters/reporting/resources/activity-analysis.title');
    }

    public static function getNavigationLabel(): string
    {
        return __('crm::filament/clusters/reporting/resources/activity-analysis.navigation.title');
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->whereHas('lead', fn (Builder $query): Builder => $query->withTrashed())
            ->with(['lead' => fn (BelongsTo $query) => $query->withTrashed(), 'activityType', 'assignedTo', 'doneBy', 'user', 'team', 'stage']);
    }

    public static function table(Table $table): Table
    {
        return ActivityLogTable::configure($table);
    }

    public static function getReportTableViews(): array
    {
        return [
            'mine' => PresetView::make(__('crm::filament/clusters/reporting/report.tabs.my-activities'))
                ->icon('heroicon-s-user')
                ->favorite()
                ->setAsDefault()
                ->modifyQueryUsing(fn (Builder $query): Builder => $query->where(fn (Builder $query): Builder => $query->where('done_by', Auth::id())->orWhere('assigned_to', Auth::id()))),

            'leads' => PresetView::make(__('crm::filament/clusters/reporting/report.tabs.leads'))
                ->icon('heroicon-s-user-plus')
                ->favorite()
                ->modifyQueryUsing(fn (Builder $query): Builder => $query->where('lead_type', LeadType::LEAD)),

            'opportunities' => PresetView::make(__('crm::filament/clusters/reporting/report.tabs.opportunities'))
                ->icon('heroicon-s-funnel')
                ->favorite()
                ->modifyQueryUsing(fn (Builder $query): Builder => $query->where('lead_type', LeadType::OPPORTUNITY)),

            'this_month' => PresetView::make(__('crm::filament/clusters/reporting/report.tabs.this-month'))
                ->icon('heroicon-s-calendar-days')
                ->favorite()
                ->modifyQueryUsing(fn (Builder $query): Builder => $query->where('done_at', '>=', now()->startOfMonth())),
        ];
    }

    public static function getReportBulkActions(): array
    {
        return [
            BulkActionGroup::make([
                RecordBulkActions::export(ActivityLogExporter::class),
            ]),
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListActivityAnalysis::route('/'),
            'list'  => ListActivityAnalysis::route('/list'),
        ];
    }
}
