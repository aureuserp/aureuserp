<?php

namespace Webkul\Support\Filament\Resources;

use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use Webkul\Support\Enums\NavigationGroup;
use Webkul\Support\Filament\Resources\CalendarResource\Pages\CreateCalendar;
use Webkul\Support\Filament\Resources\CalendarResource\Pages\EditCalendar;
use Webkul\Support\Filament\Resources\CalendarResource\Pages\ListCalendars;
use Webkul\Support\Filament\Resources\CalendarResource\Pages\ViewCalendar;
use Webkul\Support\Filament\Resources\CalendarResource\RelationManagers\CalendarAttendance;
use Webkul\Support\Filament\Resources\CalendarResource\Schemas\CalendarForm;
use Webkul\Support\Filament\Resources\CalendarResource\Schemas\CalendarInfolist;
use Webkul\Support\Filament\Resources\CalendarResource\Tables\CalendarsTable;
use Webkul\Support\Models\Calendar;

class CalendarResource extends Resource
{
    protected static ?string $model = Calendar::class;

    protected static bool $shouldRegisterNavigation = false;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedClock;

    public static function getModelLabel(): string
    {
        return __('support::filament/resources/calendar.title');
    }

    public static function getNavigationGroup(): string|\UnitEnum
    {
        return NavigationGroup::Employee;
    }

    public static function getNavigationLabel(): string
    {
        return __('support::filament/resources/calendar.navigation.title');
    }

    public static function form(Schema $schema): Schema
    {
        return CalendarForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return CalendarsTable::configure($table);
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
