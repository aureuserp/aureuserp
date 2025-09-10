<?php

namespace Webkul\TimeOff\Filament\Clusters\Configurations\Resources\MandatoryDays;

use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Table;
use Webkul\TimeOff\Filament\Clusters\Configurations;
use Webkul\TimeOff\Filament\Clusters\Configurations\Resources\MandatoryDays\Pages\ListMandatoryDays;
use Webkul\TimeOff\Filament\Clusters\Configurations\Resources\MandatoryDays\Schemas\MandatoryDayForm;
use Webkul\TimeOff\Filament\Clusters\Configurations\Resources\MandatoryDays\Schemas\MandatoryDayInfolist;
use Webkul\TimeOff\Filament\Clusters\Configurations\Resources\MandatoryDays\Tables\MandatoryDaysTable;
use Webkul\TimeOff\Models\LeaveMandatoryDay;

class MandatoryDayResource extends Resource
{
    protected static ?string $model = LeaveMandatoryDay::class;

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-arrow-path-rounded-square';

    protected static ?string $cluster = Configurations::class;

    protected static ?int $navigationSort = 4;

    public static function getModelLabel(): string
    {
        return __('time-off::filament/clusters/configurations/resources/mandatory-days.title');
    }

    public static function getNavigationLabel(): string
    {
        return __('time-off::filament/clusters/configurations/resources/mandatory-days.navigation.title');
    }

    public static function form(Schema $schema): Schema
    {
        return MandatoryDayForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return MandatoryDaysTable::configure($table);
    }

    public static function infolist(Schema $schema): Schema
    {
        return MandatoryDayInfolist::configure($schema);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListMandatoryDays::route('/'),
        ];
    }
}
