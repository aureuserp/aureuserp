<?php

namespace Webkul\Timesheet\Filament\Resources;

use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Table;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Database\Eloquent\Model;
use Webkul\Field\Filament\Traits\HasCustomFields;
use Webkul\Support\Enums\NavigationGroup;
use Webkul\Timesheet\Filament\Resources\TimesheetResource\Pages\ManageTimesheets;
use Webkul\Timesheet\Filament\Resources\TimesheetResource\Schemas\TimesheetForm;
use Webkul\Timesheet\Filament\Resources\TimesheetResource\Tables\TimesheetsTable;
use Webkul\Timesheet\Models\Timesheet;

class TimesheetResource extends Resource
{
    use HasCustomFields;

    protected static ?string $model = Timesheet::class;

    public static function getNavigationLabel(): string
    {
        return __('timesheets::filament/resources/timesheet.navigation.title');
    }

    public static function getNavigationGroup(): string|\UnitEnum
    {
        return NavigationGroup::Project;
    }

    public static function getGlobalSearchResultTitle(Model $record): string|Htmlable
    {
        return $record->user->name;
    }

    public static function getGloballySearchableAttributes(): array
    {
        return ['user.name', 'project.name', 'task.title', 'date'];
    }

    public static function getGlobalSearchResultDetails(Model $record): array
    {
        return [
            __('timesheets::filament/resources/timesheet.global-search.project')  => $record->project?->name ?? '—',
            __('timesheets::filament/resources/timesheet.global-search.task')     => $record->task?->title ?? '—',
            __('timesheets::filament/resources/timesheet.global-search.date')     => $record->date ?? '—',
        ];
    }

    public static function form(Schema $schema): Schema
    {
        return TimesheetForm::configure($schema, static::getCustomFormFields());
    }

    public static function table(Table $table): Table
    {
        return TimesheetsTable::configure($table, static::getCustomTableColumns(), static::getCustomTableFilters());
    }

    public static function getPages(): array
    {
        return [
            'index' => ManageTimesheets::route('/'),
        ];
    }
}
