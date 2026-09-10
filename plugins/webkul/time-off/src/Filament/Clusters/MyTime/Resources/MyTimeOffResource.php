<?php

namespace Webkul\TimeOff\Filament\Clusters\MyTime\Resources;

use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Table;
use Webkul\TimeOff\Enums\State;
use Webkul\TimeOff\Filament\Clusters\Management\Resources\TimeOffResource;
use Webkul\TimeOff\Filament\Clusters\MyTime;
use Webkul\TimeOff\Filament\Clusters\MyTime\Resources\MyTimeOffResource\Pages\CreateMyTimeOff;
use Webkul\TimeOff\Filament\Clusters\MyTime\Resources\MyTimeOffResource\Pages\EditMyTimeOff;
use Webkul\TimeOff\Filament\Clusters\MyTime\Resources\MyTimeOffResource\Pages\ListMyTimeOffs;
use Webkul\TimeOff\Filament\Clusters\MyTime\Resources\MyTimeOffResource\Pages\ViewMyTimeOff;
use Webkul\TimeOff\Filament\Clusters\MyTime\Resources\MyTimeOffResource\Schemas\MyTimeOffInfolist;
use Webkul\TimeOff\Models\Leave;
use Webkul\TimeOff\Traits\TimeOffHelper;

class MyTimeOffResource extends Resource
{
    use TimeOffHelper;

    protected static ?string $model = Leave::class;

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-lifebuoy';

    protected static ?int $navigationSort = 2;

    protected static ?string $cluster = MyTime::class;

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
        return $schema->components((new self)->getFormSchema());
    }

    public static function table(Table $table): Table
    {
        return $table = TimeOffResource::table($table);
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

    public static function isEditableState(?Leave $record): bool
    {
        return ! in_array($record?->state, [
            State::REFUSE,
            State::VALIDATE_ONE,
            State::VALIDATE_TWO,
        ]);
    }

    public static function infolist(Schema $schema): Schema
    {
        return MyTimeOffInfolist::configure($schema);
    }
}
