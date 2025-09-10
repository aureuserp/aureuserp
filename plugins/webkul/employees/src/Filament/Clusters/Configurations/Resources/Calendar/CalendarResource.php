<?php

namespace Webkul\Employee\Filament\Clusters\Configurations\Resources\Calendar;

use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Table;
use Webkul\Employee\Filament\Clusters\Configurations;
use Webkul\Employee\Filament\Clusters\Configurations\Resources\Calendar\Pages\CreateCalendar;
use Webkul\Employee\Filament\Clusters\Configurations\Resources\Calendar\Pages\EditCalendar;
use Webkul\Employee\Filament\Clusters\Configurations\Resources\Calendar\Pages\ListCalendars;
use Webkul\Employee\Filament\Clusters\Configurations\Resources\Calendar\Pages\ViewCalendar;
use Webkul\Employee\Filament\Clusters\Configurations\Resources\Calendar\RelationManagers\CalendarAttendance;
use Webkul\Employee\Filament\Clusters\Configurations\Resources\Calendar\Schemas\CalendarForm;
use Webkul\Employee\Filament\Clusters\Configurations\Resources\Calendar\Schemas\CalendarInfolist;
use Webkul\Employee\Filament\Clusters\Configurations\Resources\Calendar\Tables\CalendarTable;
use Webkul\Employee\Models\Calendar;

class CalendarResource extends Resource
{
    protected static ?string $model = Calendar::class;

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-clock';

    protected static ?string $cluster = Configurations::class;

    public static function getModelLabel(): string
    {
        return __('employees::filament/clusters/configurations/resources/calendar.title');
    }

    public static function getNavigationGroup(): string
    {
        return __('employees::filament/clusters/configurations/resources/calendar.navigation.group');
    }

    public static function getNavigationLabel(): string
    {
        return __('employees::filament/clusters/configurations/resources/calendar.navigation.title');
    }

    public static function form(Schema $schema): Schema
    {
        return CalendarForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return CalendarTable::configure($table);
    }

    public static function infolist(Schema $schema): Schema
    {
        return CalendarInfolist::configure($schema);
    }

    public static function getRelations(): array
    {
        return [
            CalendarAttendance::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index'  => ListCalendars::route('/'),
            'create' => CreateCalendar::route('/create'),
            'view'   => ViewCalendar::route('/{record}'),
            'edit'   => EditCalendar::route('/{record}/edit'),
        ];
    }
}
