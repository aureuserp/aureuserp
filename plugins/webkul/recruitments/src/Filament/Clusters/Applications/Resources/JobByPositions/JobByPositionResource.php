<?php

namespace Webkul\Recruitment\Filament\Clusters\Applications\Resources\JobByPositions;

use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Table;
use Webkul\Employee\Filament\Clusters\Configurations\Resources\JobPositions\JobPositionResource;
use Webkul\Recruitment\Filament\Clusters\Applications;
use Webkul\Recruitment\Filament\Clusters\Applications\Resources\JobByPositions\Pages\ListJobByPositions;
use Webkul\Recruitment\Filament\Clusters\Applications\Resources\JobByPositions\Tables\JobByPositionsTable;
use Webkul\Recruitment\Models\JobPosition;

class JobByPositionResource extends Resource
{
    protected static ?string $model = JobPosition::class;

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-briefcase';

    protected static ?string $cluster = Applications::class;

    protected static ?int $navigationSort = 1;

    public static function form(Schema $schema): Schema
    {
        return JobPositionResource::form($schema);
    }

    public static function getModelLabel(): string
    {
        return __('recruitments::filament/clusters/applications/resources/job-by-application.title');
    }

    public static function getNavigationGroup(): string
    {
        return __('recruitments::filament/clusters/applications/resources/job-by-application.navigation.group');
    }

    public static function getNavigationLabel(): string
    {
        return __('recruitments::filament/clusters/applications/resources/job-by-application.navigation.title');
    }

    public static function table(Table $table): Table
    {
        return JobByPositionsTable::configure($table);
    }

    public static function infolist(Schema $schema): Schema
    {
        return JobPositionResource::infolist($schema);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListJobByPositions::route('/'),
        ];
    }
}
