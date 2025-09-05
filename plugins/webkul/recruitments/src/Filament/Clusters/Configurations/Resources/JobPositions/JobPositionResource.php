<?php

namespace Webkul\Recruitment\Filament\Clusters\Configurations\Resources\JobPositions;

use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Model;
use Webkul\Recruitment\Filament\Clusters\Configurations;
use Webkul\Recruitment\Filament\Clusters\Configurations\Resources\JobPositions\Pages\CreateJobPosition;
use Webkul\Recruitment\Filament\Clusters\Configurations\Resources\JobPositions\Pages\EditJobPosition;
use Webkul\Recruitment\Filament\Clusters\Configurations\Resources\JobPositions\Pages\ListJobPositions;
use Webkul\Recruitment\Filament\Clusters\Configurations\Resources\JobPositions\Pages\ViewJobPosition;
use Webkul\Recruitment\Filament\Clusters\Configurations\Resources\JobPositions\Schemas\JobPositionForm;
use Webkul\Recruitment\Filament\Clusters\Configurations\Resources\JobPositions\Schemas\JobPositionInfolist;
use Webkul\Recruitment\Filament\Clusters\Configurations\Resources\JobPositions\Tables\JobPositionsTable;
use Webkul\Recruitment\Models\JobPosition;

class JobPositionResource extends Resource
{
    protected static ?string $model = JobPosition::class;

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-briefcase';

    protected static ?int $navigationSort = 2;

    protected static ?string $cluster = Configurations::class;

    public static function getNavigationGroup(): string
    {
        return __('recruitments::filament/clusters/configurations/resources/job-position.navigation.group');
    }

    public static function getGlobalSearchResultDetails(Model $record): array
    {
        return [
            __('recruitments::filament/clusters/configurations/resources/job-position.global-search.name')            => $record->name ?? '—',
            __('recruitments::filament/clusters/configurations/resources/job-position.global-search.department')      => $record->department?->name ?? '—',
            __('recruitments::filament/clusters/configurations/resources/job-position.global-search.employment-type') => $record->employmentType?->name ?? '—',
            __('recruitments::filament/clusters/configurations/resources/job-position.global-search.company')         => $record->company?->name ?? '—',
            __('recruitments::filament/clusters/configurations/resources/job-position.global-search.created-by')      => $record->createdBy?->name ?? '—',
        ];
    }

    public static function form(Schema $schema): Schema
    {
        return JobPositionForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return JobPositionsTable::configure($table);
    }

    public static function infolist(Schema $schema): Schema
    {
        return JobPositionInfolist::configure($schema);
    }

    public static function getPages(): array
    {
        return [
            'index'  => ListJobPositions::route('/'),
            'create' => CreateJobPosition::route('/create'),
            'edit'   => EditJobPosition::route('/{record}/edit'),
            'view'   => ViewJobPosition::route('/{record}'),
        ];
    }
}
