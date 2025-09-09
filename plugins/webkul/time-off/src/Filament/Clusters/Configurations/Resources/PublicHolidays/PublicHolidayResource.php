<?php

namespace Webkul\TimeOff\Filament\Clusters\Configurations\Resources\PublicHolidays;

use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Table;
use Webkul\TimeOff\Filament\Clusters\Configurations;
use Webkul\TimeOff\Filament\Clusters\Configurations\Resources\PublicHolidays\Pages\ListPublicHolidays;
use Webkul\TimeOff\Filament\Clusters\Configurations\Resources\PublicHolidays\Schemas\PublicHolidayForm;
use Webkul\TimeOff\Filament\Clusters\Configurations\Resources\PublicHolidays\Schemas\PublicHolidayInfolist;
use Webkul\TimeOff\Filament\Clusters\Configurations\Resources\PublicHolidays\Tables\PublicHolidaysTable;
use Webkul\TimeOff\Models\CalendarLeave;

class PublicHolidayResource extends Resource
{
    protected static ?string $model = CalendarLeave::class;

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-lifebuoy';

    protected static ?string $cluster = Configurations::class;

    protected static ?int $navigationSort = 3;

    protected static ?string $modelLabel = 'Public Holiday';

    public static function getModelLabel(): string
    {
        return __('time-off::filament/clusters/configurations/resources/public-holiday.title');
    }

    public static function getNavigationLabel(): string
    {
        return __('time-off::filament/clusters/configurations/resources/public-holiday.navigation.title');
    }

    public static function form(Schema $schema): Schema
    {
        return PublicHolidayForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return PublicHolidaysTable::configure($table);
    }

    public static function infolist(Schema $schema): Schema
    {
        return PublicHolidayInfolist::configure($schema);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListPublicHolidays::route('/'),
        ];
    }
}
