<?php

namespace Webkul\Crm\Filament\Clusters\Reporting\Resources;

use Filament\Resources\Resource;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Webkul\Crm\Filament\Clusters\Reporting;
use Webkul\Crm\Filament\Clusters\Reporting\Resources\ActivityAnalysisResource\Pages\GraphActivityAnalysis;
use Webkul\Crm\Filament\Clusters\Reporting\Resources\ActivityAnalysisResource\Pages\ListActivityAnalysis;
use Webkul\Crm\Filament\Clusters\Reporting\Resources\ActivityAnalysisResource\Pages\SummaryActivityAnalysis;
use Webkul\Crm\Filament\Concerns\IsCrmReportResource;
use Webkul\Crm\Filament\Support\Reports\ActivityReport;
use Webkul\Crm\Filament\Support\Reports\Tables\ActivityLogTable;
use Webkul\Crm\Models\ActivityLog;
use Webkul\Crm\Policies\ActivityAnalysisPolicy;

class ActivityAnalysisResource extends Resource
{
    use IsCrmReportResource;

    protected static ?string $model = ActivityLog::class;

    protected static ?string $slug = 'activity-analysis';

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-clipboard-document-check';

    protected static ?string $cluster = Reporting::class;

    protected static bool $isGloballySearchable = false;

    protected static ?int $navigationSort = 4;

    public static function getCrmPolicy(): string
    {
        return ActivityAnalysisPolicy::class;
    }

    public static function getReport(): string
    {
        return ActivityReport::class;
    }

    public static function getReportKey(): string
    {
        return 'activity-analysis';
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->with(['lead' => fn (BelongsTo $query) => $query->withTrashed(), 'activityType', 'assignedTo', 'doneBy', 'user', 'team', 'stage']);
    }

    public static function table(Table $table): Table
    {
        return ActivityLogTable::configure($table);
    }

    public static function getPages(): array
    {
        return [
            'index'   => GraphActivityAnalysis::route('/'),
            'summary' => SummaryActivityAnalysis::route('/summary'),
            'list'    => ListActivityAnalysis::route('/list'),
        ];
    }
}
