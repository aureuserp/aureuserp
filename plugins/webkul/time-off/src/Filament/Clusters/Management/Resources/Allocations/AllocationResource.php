<?php

namespace Webkul\TimeOff\Filament\Clusters\Management\Resources\Allocations;

use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Table;
use Webkul\TimeOff\Filament\Clusters\Management;
use Webkul\TimeOff\Filament\Clusters\Management\Resources\Allocations\Pages\CreateAllocation;
use Webkul\TimeOff\Filament\Clusters\Management\Resources\Allocations\Pages\EditAllocation;
use Webkul\TimeOff\Filament\Clusters\Management\Resources\Allocations\Pages\ListAllocations;
use Webkul\TimeOff\Filament\Clusters\Management\Resources\Allocations\Pages\ViewAllocation;
use Webkul\TimeOff\Filament\Clusters\Management\Resources\Allocations\Schemas\AllocationForm;
use Webkul\TimeOff\Filament\Clusters\Management\Resources\Allocations\Schemas\AllocationInfolist;
use Webkul\TimeOff\Filament\Clusters\Management\Resources\Allocations\Tables\AllocationsTable;
use Webkul\TimeOff\Models\LeaveAllocation;

class AllocationResource extends Resource
{
    protected static ?string $model = LeaveAllocation::class;

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-calendar-days';

    protected static ?string $cluster = Management::class;

    protected static ?int $navigationSort = 2;

    public static function getModelLabel(): string
    {
        return __('time-off::filament/clusters/management/resources/allocation.model-label');
    }

    public static function getNavigationLabel(): string
    {
        return __('time-off::filament/clusters/management/resources/allocation.navigation.title');
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
