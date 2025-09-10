<?php

namespace Webkul\TimeOff\Filament\Clusters\Management\Resources\TimeOffs;

use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Table;
use Webkul\TimeOff\Filament\Clusters\Management;
use Webkul\TimeOff\Filament\Clusters\Management\Resources\TimeOffs\Pages\CreateTimeOff;
use Webkul\TimeOff\Filament\Clusters\Management\Resources\TimeOffs\Pages\EditTimeOff;
use Webkul\TimeOff\Filament\Clusters\Management\Resources\TimeOffs\Pages\ListTimeOff;
use Webkul\TimeOff\Filament\Clusters\Management\Resources\TimeOffs\Pages\ViewTimeOff;
use Webkul\TimeOff\Filament\Clusters\Management\Resources\TimeOffs\Schemas\TimeOffForm;
use Webkul\TimeOff\Filament\Clusters\Management\Resources\TimeOffs\Schemas\TimeOffInfolist;
use Webkul\TimeOff\Filament\Clusters\Management\Resources\TimeOffs\Tables\TimeOffsTable;
use Webkul\TimeOff\Models\Leave;

class TimeOffResource extends Resource
{
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

    public static function form(Schema $schema): Schema
    {
        return TimeOffForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return TimeOffsTable::configure($table);
    }

    public static function infolist(Schema $schema): Schema
    {
        return TimeOffInfolist::configure($schema);
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
