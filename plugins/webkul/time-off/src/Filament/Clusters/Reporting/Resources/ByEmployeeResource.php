<?php

namespace Webkul\TimeOff\Filament\Clusters\Reporting\Resources;

use Filament\Forms\Form;
use Filament\Infolists\Infolist;
use Filament\Resources\Resource;
use Filament\Tables\Table;
use Webkul\TimeOff\Filament\Clusters\Management\Resources\TimeOffResource as BaseByEmployeeResource;
use Webkul\TimeOff\Filament\Clusters\Reporting;
use Webkul\TimeOff\Filament\Clusters\Reporting\Resources\ByEmployeeResource\Pages;

class ByEmployeeResource extends BaseByEmployeeResource
{
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

    public static function table(Table $table): Table
    {
        return parent::table($table)
            ->defaultGroup('employee.name');
    }

    public static function infolist(Infolist $infolist): Infolist
    {
        return parent::infolist($infolist);
    }

    public static function getPages(): array
    {
        return [
            'index'  => Pages\ListByEmployees::route('/'),
            'create' => Pages\CreateByEmployee::route('/create'),
            'edit'   => Pages\EditByEmployee::route('/{record}/edit'),
            'view'   => Pages\ViewByEmployee::route('/{record}'),
        ];
    }
}
