<?php

namespace Webkul\Employee\Filament\Clusters\Configurations\Resources;

use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Table;
use Webkul\Employee\Filament\Clusters\Configurations;
use Webkul\Employee\Filament\Clusters\Configurations\Resources\DepartureReasonResource\Pages\ListDepartureReasons;
use Webkul\Employee\Filament\Clusters\Configurations\Resources\DepartureReasonResource\Schemas\DepartureReasonForm;
use Webkul\Employee\Filament\Clusters\Configurations\Resources\DepartureReasonResource\Schemas\DepartureReasonInfolist;
use Webkul\Employee\Filament\Clusters\Configurations\Resources\DepartureReasonResource\Tables\DepartureReasonsTable;
use Webkul\Employee\Models\DepartureReason;

class DepartureReasonResource extends Resource
{
    protected static ?string $model = DepartureReason::class;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-fire';

    protected static ?string $cluster = Configurations::class;

    public static function getModelLabel(): string
    {
        return __('employees::filament/clusters/configurations/resources/departure-reason.title');
    }

    public static function getNavigationGroup(): string
    {
        return __('employees::filament/clusters/configurations/resources/departure-reason.navigation.group');
    }

    public static function getNavigationLabel(): string
    {
        return __('employees::filament/clusters/configurations/resources/departure-reason.navigation.title');
    }

    public static function form(Schema $schema): Schema
    {
        return DepartureReasonForm::configure($schema);
    }

    public static function infolist(Schema $schema): Schema
    {
        return DepartureReasonInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return DepartureReasonsTable::configure($table);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListDepartureReasons::route('/'),
        ];
    }
}
