<?php

namespace Webkul\TimeOff\Filament\Clusters\MyTime\Resources\MyAllocations;

use BackedEnum;
use Filament\Pages\Enums\SubNavigationPosition;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Table;
use Webkul\TimeOff\Filament\Clusters\MyTime;
use Webkul\TimeOff\Filament\Clusters\MyTime\Resources\MyAllocations\Pages\CreateMyAllocation;
use Webkul\TimeOff\Filament\Clusters\MyTime\Resources\MyAllocations\Pages\EditMyAllocation;
use Webkul\TimeOff\Filament\Clusters\MyTime\Resources\MyAllocations\Pages\ListMyAllocations;
use Webkul\TimeOff\Filament\Clusters\MyTime\Resources\MyAllocations\Pages\ViewMyAllocation;
use Webkul\TimeOff\Filament\Clusters\MyTime\Resources\MyAllocations\Schemas\MyAllocationForm;
use Webkul\TimeOff\Filament\Clusters\MyTime\Resources\MyAllocations\Schemas\MyAllocationInfolist;
use Webkul\TimeOff\Filament\Clusters\MyTime\Resources\MyAllocations\Tables\MyAllicationsTable;
use Webkul\TimeOff\Models\LeaveAllocation;

class MyAllocationResource extends Resource
{
    protected static ?string $model = LeaveAllocation::class;

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-calendar';

    protected static ?string $cluster = MyTime::class;

    protected static ?int $navigationSort = 3;

    protected static ?string $modelLabel = 'My Allocation';

    protected static ?SubNavigationPosition $subNavigationPosition = SubNavigationPosition::Top;

    public static function getModelLabel(): string
    {
        return __('time-off::filament/clusters/my-time/resources/my-allocation.model-label');
    }

    public static function getNavigationLabel(): string
    {
        return __('time-off::filament/clusters/my-time/resources/my-allocation.navigation.title');
    }

    public static function form(Schema $schema): Schema
    {
        return MyAllocationForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return MyAllicationsTable::configure($table);
    }

    public static function infolist(Schema $schema): Schema
    {
        return MyAllocationInfolist::configure($schema);
    }

    public static function getPages(): array
    {
        return [
            'index'  => ListMyAllocations::route('/'),
            'create' => CreateMyAllocation::route('/create'),
            'edit'   => EditMyAllocation::route('/{record}/edit'),
            'view'   => ViewMyAllocation::route('/{record}'),
        ];
    }
}
