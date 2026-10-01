<?php

namespace Webkul\Attendance\Filament\Resources;

use Filament\Panel;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Table;
use Webkul\Attendance\Filament\Resources\AttendanceResource\Pages\CreateAttendance;
use Webkul\Attendance\Filament\Resources\AttendanceResource\Pages\EditAttendance;
use Webkul\Attendance\Filament\Resources\AttendanceResource\Pages\ListAttendances;
use Webkul\Attendance\Filament\Resources\AttendanceResource\Schemas\AttendanceForm;
use Webkul\Attendance\Filament\Resources\AttendanceResource\Tables\AttendancesTable;
use Webkul\Attendance\Models\Attendance;
use Webkul\Support\Enums\NavigationGroup;

class AttendanceResource extends Resource
{
    protected static ?string $model = Attendance::class;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-clock';

    public static function getModelLabel(): string
    {
        return __('attendance::filament/resources/attendance.title');
    }

    public static function getNavigationLabel(): string
    {
        return __('attendance::filament/resources/attendance.navigation.title');
    }

    public static function getNavigationGroup(): string|\UnitEnum
    {
        return NavigationGroup::Attendance;
    }

    public static function form(Schema $schema): Schema
    {
        return AttendanceForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return AttendancesTable::configure($table);
    }

    public static function getSlug(?Panel $panel = null): string
    {
        return 'attendance/attendances';
    }

    public static function getPages(): array
    {
        return [
            'index'  => ListAttendances::route('/'),
            'create' => CreateAttendance::route('/create'),
            'edit'   => EditAttendance::route('/{record}/edit'),
        ];
    }
}
