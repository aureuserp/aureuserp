<?php

namespace Webkul\Timesheet\Filament\Resources\Timesheets;

use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Table;
use Webkul\Project\Models\Timesheet;
use Webkul\Timesheet\Filament\Resources\Timesheets\Pages\ManageTimesheets;

class TimesheetResource extends Resource
{
    protected static ?string $model = Timesheet::class;

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-clock';

    public static function getNavigationLabel(): string
    {
        return __('timesheets::filament/resources/timesheet.navigation.title');
    }

    public static function getNavigationGroup(): string
    {
        return __('timesheets::filament/resources/timesheet.navigation.group');
    }

    public static function form(Schema $schema): Schema
    {
        return TimesheetForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return TimesheetsTable::configure($table);
    }

    public static function getPages(): array
    {
        return [
            'index' => ManageTimesheets::route('/'),
        ];
    }
}
