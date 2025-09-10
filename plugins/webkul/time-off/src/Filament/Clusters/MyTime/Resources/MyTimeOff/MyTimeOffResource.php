<?php

namespace Webkul\TimeOff\Filament\Clusters\MyTime\Resources\MyTimeOff;

use BackedEnum;
use Filament\Pages\Enums\SubNavigationPosition;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Table;
use Webkul\TimeOff\Filament\Clusters\MyTime;
use Webkul\TimeOff\Filament\Clusters\MyTime\Resources\MyTimeOff\Pages\CreateMyTimeOff;
use Webkul\TimeOff\Filament\Clusters\MyTime\Resources\MyTimeOff\Pages\EditMyTimeOff;
use Webkul\TimeOff\Filament\Clusters\MyTime\Resources\MyTimeOff\Pages\ListMyTimeOffs;
use Webkul\TimeOff\Filament\Clusters\MyTime\Resources\MyTimeOff\Pages\ViewMyTimeOff;
use Webkul\TimeOff\Filament\Clusters\MyTime\Resources\MyTimeOff\Schemas\MyTimeOffForm;
use Webkul\TimeOff\Filament\Clusters\MyTime\Resources\MyTimeOff\Schemas\MyTimeOffInfolist;
use Webkul\TimeOff\Filament\Clusters\MyTime\Resources\MyTimeOff\Tables\MyTimeOffTable;
use Webkul\TimeOff\Models\Leave;

class MyTimeOffResource extends Resource
{
    protected static ?string $model = Leave::class;

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-lifebuoy';

    protected static ?int $navigationSort = 2;

    protected static ?string $cluster = MyTime::class;

    protected static ?SubNavigationPosition $subNavigationPosition = SubNavigationPosition::Top;

    public static function getModelLabel(): string
    {
        return __('time-off::filament/clusters/my-time/resources/my-time-off.model-label');
    }

    public static function getNavigationLabel(): string
    {
        return __('time-off::filament/clusters/my-time/resources/my-time-off.navigation.title');
    }

    public static function form(Schema $schema): Schema
    {
        return MyTimeOffForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return MyTimeOffTable::configure($table);
    }

    public static function getPages(): array
    {
        return [
            'index'  => ListMyTimeOffs::route('/'),
            'create' => CreateMyTimeOff::route('/create'),
            'edit'   => EditMyTimeOff::route('/{record}/edit'),
            'view'   => ViewMyTimeOff::route('/{record}'),
        ];
    }

    public static function infolist(Schema $schema): Schema
    {
        return MyTimeOffInfolist::configure($schema);
    }
}
