<?php

namespace Webkul\TimeOff\Filament\Clusters\Reporting\Resources\ByEmployees;

use BackedEnum;
use Filament\Schemas\Schema;
use Filament\Tables\Table;
use Webkul\TimeOff\Filament\Clusters\Management\Resources\TimeOffResource as BaseByEmployeeResource;
use Webkul\TimeOff\Filament\Clusters\Reporting;
use Webkul\TimeOff\Filament\Clusters\Reporting\Resources\ByEmployees\Pages\CreateByEmployee;
use Webkul\TimeOff\Filament\Clusters\Reporting\Resources\ByEmployees\Pages\EditByEmployee;
use Webkul\TimeOff\Filament\Clusters\Reporting\Resources\ByEmployees\Pages\ListByEmployees;
use Webkul\TimeOff\Filament\Clusters\Reporting\Resources\ByEmployees\Pages\ViewByEmployee;
use Webkul\TimeOff\Filament\Clusters\Reporting\Resources\ByEmployees\Schemas\ByEmployeeForm;
use Webkul\TimeOff\Models\Leave;

class ByEmployeeResource extends BaseByEmployeeResource
{
    protected static ?string $model = Leave::class;

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-users';

    protected static ?string $cluster = Reporting::class;

    public static function getModelLabel(): string
    {
        return __('time-off::filament/clusters/reporting/resources/by-employee.title');
    }

    public static function getNavigationLabel(): string
    {
        return __('time-off::filament/clusters/reporting/resources/by-employee.navigation.title');
    }

    public static function form(Schema $schema): Schema
    {
        return ByEmployeeForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return parent::table($table)
            ->defaultGroup('employee.name');
    }

    public static function getPages(): array
    {
        return [
            'index'  => ListByEmployees::route('/'),
            'create' => CreateByEmployee::route('/create'),
            'edit'   => EditByEmployee::route('/{record}/edit'),
            'view'   => ViewByEmployee::route('/{record}'),
        ];
    }
}
