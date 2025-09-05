<?php

namespace Webkul\Employee\Filament\Clusters\Configurations\Resources\JobPositions;

use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Table;
use Webkul\Employee\Filament\Clusters\Configurations;
use Webkul\Employee\Filament\Clusters\Configurations\Resources\JobPositions\Pages\CreateJobPosition;
use Webkul\Employee\Filament\Clusters\Configurations\Resources\JobPositions\Pages\EditJobPosition;
use Webkul\Employee\Filament\Clusters\Configurations\Resources\JobPositions\Pages\ListJobPositions;
use Webkul\Employee\Filament\Clusters\Configurations\Resources\JobPositions\Pages\ViewJobPosition;
use Webkul\Employee\Filament\Clusters\Configurations\Resources\JobPositions\Schemas\JobPositionForm;
use Webkul\Employee\Filament\Clusters\Configurations\Resources\JobPositions\Schemas\JobPositionInfolist;
use Webkul\Employee\Filament\Clusters\Configurations\Resources\JobPositions\Tables\JobPositionsTable;
use Webkul\Employee\Models\EmployeeJobPosition;

class JobPositionResource extends Resource
{
    protected static ?string $model = EmployeeJobPosition::class;

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-briefcase';

    protected static ?string $cluster = Configurations::class;

    public static function getModelLabel(): string
    {
        return __('employees::filament/clusters/configurations/resources/job-position.title');
    }

    public static function getNavigationGroup(): string
    {
        return __('employees::filament/clusters/configurations/resources/job-position.navigation.group');
    }

    public static function getNavigationLabel(): string
    {
        return __('employees::filament/clusters/configurations/resources/job-position.navigation.title');
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
            'view'   => ViewJobPosition::route('/{record}'),
            'edit'   => EditJobPosition::route('/{record}/edit'),
        ];
    }
}
