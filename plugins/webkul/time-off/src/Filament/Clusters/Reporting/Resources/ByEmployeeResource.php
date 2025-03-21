<?php

namespace Webkul\TimeOff\Filament\Clusters\Reporting\Resources;

use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Model;
use Webkul\TimeOff\Filament\Clusters\Management\Resources\TimeOffResource;
use Webkul\TimeOff\Filament\Clusters\Reporting;
use Webkul\TimeOff\Filament\Clusters\Reporting\Resources\ByEmployeeResource\Pages;
use Webkul\TimeOff\Models\Leave;

class ByEmployeeResource extends Resource
{
    protected static ?string $model = Leave::class;

    protected static ?string $navigationIcon = 'heroicon-o-users';

    protected static ?string $cluster = Reporting::class;

    public static function getModelLabel(): string
    {
        return __('time-off::filament/clusters/reporting/resources/by-employee.title');
    }

    public static function getNavigationLabel(): string
    {
        return __('time-off::filament/clusters/reporting/resources/by-employee.navigation.title');
    }

    public static function getGloballySearchableAttributes(): array
    {
        return [
            'employee.name',
            'department.name',
            'holidayStatus.name',
            'request_date_from',
            'request_date_to',
        ];
    }

    public static function getGlobalSearchResultDetails(Model $record): array
    {
        return [
            __('time-off::filament/clusters/reporting/resources/by-employee.global-search.employee')          => $record->name ?? '—',
            __('time-off::filament/clusters/reporting/resources/by-employee.global-search.department')        => $record->manager?->name ?? '—',
            __('time-off::filament/clusters/reporting/resources/by-employee.global-search.time-off-type')     => $record->company?->name ?? '—',
            __('time-off::filament/clusters/reporting/resources/by-employee.global-search.request-date-from') => $record->request_date_from ?? '—',
            __('time-off::filament/clusters/reporting/resources/by-employee.global-search.request-date-to')   => $record->request_date_to ?? '—',
        ];
    }

    public static function form(Form $form): Form
    {
        return TimeOffResource::form($form);
    }

    public static function table(Table $table): Table
    {
        return TimeOffResource::table($table)
            ->defaultGroup('employee.name');
    }

    public static function getPages(): array
    {
        return [
            'index'  => Pages\ListByEmployees::route('/'),
            'create' => Pages\CreateByEmployee::route('/create'),
            'edit'   => Pages\EditByEmployee::route('/{record}/edit'),
        ];
    }
}
