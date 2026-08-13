<?php

namespace Webkul\TimeOff\Filament\Clusters\Management\Resources;

use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Model;
use Webkul\Field\Filament\Traits\HasCustomFields;
use Webkul\TimeOff\Filament\Clusters\Management;
use Webkul\TimeOff\Filament\Clusters\Management\Resources\TimeOffResource\Pages\CreateTimeOff;
use Webkul\TimeOff\Filament\Clusters\Management\Resources\TimeOffResource\Pages\EditTimeOff;
use Webkul\TimeOff\Filament\Clusters\Management\Resources\TimeOffResource\Pages\ListTimeOff;
use Webkul\TimeOff\Filament\Clusters\Management\Resources\TimeOffResource\Pages\ViewTimeOff;
use Webkul\TimeOff\Filament\Clusters\Management\Resources\TimeOffResource\Schemas\TimeOffForm;
use Webkul\TimeOff\Filament\Clusters\Management\Resources\TimeOffResource\Schemas\TimeOffInfolist;
use Webkul\TimeOff\Filament\Clusters\Management\Resources\TimeOffResource\Tables\TimeOffsTable;
use Webkul\TimeOff\Models\Leave;
use Webkul\TimeOff\Traits\TimeOffHelper;

class TimeOffResource extends Resource
{
    use HasCustomFields, TimeOffHelper;

    protected static ?string $model = Leave::class;

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-rectangle-stack';

    protected static ?string $cluster = Management::class;

    protected static ?int $navigationSort = 1;

    public static function getModelLabel(): string
    {
        return __('time-off::filament/clusters/management/resources/time-off.model-label');
    }

    public static function getNavigationLabel(): string
    {
        return __('time-off::filament/clusters/management/resources/time-off.navigation.title');
    }

    public static function getGloballySearchableAttributes(): array
    {
        return ['employee.name', 'holidayStatus.name', 'date_from', 'date_to'];
    }

    public static function getGlobalSearchResultDetails(Model $record): array
    {
        return [
            __('time-off::filament/clusters/management/resources/time-off.global-search.employee')      => $record->employee?->name ?? '—',
            __('time-off::filament/clusters/management/resources/time-off.global-search.time-off-type') => $record->holidayStatus?->name ?? '—',
            __('time-off::filament/clusters/management/resources/time-off.global-search.date-from')     => $record->date_from ?? '—',
            __('time-off::filament/clusters/management/resources/time-off.global-search.date-to')       => $record->date_to ?? '—',
        ];
    }

    public static function form(Schema $schema): Schema
    {
        return TimeOffForm::configure($schema, static::getCustomFormFields());
    }

    public static function table(Table $table): Table
    {
        return TimeOffsTable::configure($table, static::getCustomTableColumns());
    }

    public static function infolist(Schema $schema): Schema
    {
        return TimeOffInfolist::configure($schema, static::getCustomInfolistEntries());
    }

    public static function getPages(): array
    {
        return [
            'index'  => ListTimeOff::route('/'),
            'create' => CreateTimeOff::route('/create'),
            'edit'   => EditTimeOff::route('/{record}/edit'),
            'view'   => ViewTimeOff::route('/{record}'),
        ];
    }
}
