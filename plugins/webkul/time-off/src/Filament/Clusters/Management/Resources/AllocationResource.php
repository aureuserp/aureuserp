<?php

namespace Webkul\TimeOff\Filament\Clusters\Management\Resources;

use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Model;
use Webkul\TimeOff\Filament\Clusters\Management;
use Webkul\TimeOff\Filament\Clusters\Management\Resources\AllocationResource\Pages\CreateAllocation;
use Webkul\TimeOff\Filament\Clusters\Management\Resources\AllocationResource\Pages\EditAllocation;
use Webkul\TimeOff\Filament\Clusters\Management\Resources\AllocationResource\Pages\ListAllocations;
use Webkul\TimeOff\Filament\Clusters\Management\Resources\AllocationResource\Pages\ViewAllocation;
use Webkul\TimeOff\Filament\Clusters\Management\Resources\AllocationResource\Schemas\AllocationForm;
use Webkul\TimeOff\Filament\Clusters\Management\Resources\AllocationResource\Schemas\AllocationInfolist;
use Webkul\TimeOff\Filament\Clusters\Management\Resources\AllocationResource\Tables\AllocationsTable;
use Webkul\TimeOff\Models\LeaveAllocation;

class AllocationResource extends Resource
{
    protected static ?string $model = LeaveAllocation::class;

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-calendar-days';

    protected static ?string $cluster = Management::class;

    protected static ?int $navigationSort = 2;

    protected static ?string $recordTitleAttribute = 'name';

    public static function getModelLabel(): string
    {
        return __('time-off::filament/clusters/management/resources/allocation.model-label');
    }

    public static function getNavigationLabel(): string
    {
        return __('time-off::filament/clusters/management/resources/allocation.navigation.title');
    }

    public static function getGloballySearchableAttributes(): array
    {
        return ['employee.name', 'holidayStatus.name'];
    }

    public static function getGlobalSearchResultDetails(Model $record): array
    {
        return [
            __('time-off::filament/clusters/management/resources/allocation.global-search.employee')      => $record->employee?->name ?? '—',
            __('time-off::filament/clusters/management/resources/allocation.global-search.time-off-type') => $record->holidayStatus?->name ?? '—',
        ];
    }

    public static function form(Schema $schema): Schema
    {
        return AllocationForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return AllocationsTable::configure($table);
    }

    public static function infolist(Schema $schema): Schema
    {
        return AllocationInfolist::configure($schema);
    }

    public static function getPages(): array
    {
        return [
            'index'  => ListAllocations::route('/'),
            'create' => CreateAllocation::route('/create'),
            'edit'   => EditAllocation::route('/{record}/edit'),
            'view'   => ViewAllocation::route('/{record}'),
        ];
    }
}
