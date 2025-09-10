<?php

namespace Webkul\Recruitment\Filament\Clusters\Configurations\Resources\Degrees;

use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Table;
use Webkul\Recruitment\Filament\Clusters\Configurations;
use Webkul\Recruitment\Filament\Clusters\Configurations\Resources\Degrees\Pages\ListDegrees;
use Webkul\Recruitment\Filament\Clusters\Configurations\Resources\Degrees\Schemas\DegreeForm;
use Webkul\Recruitment\Filament\Clusters\Configurations\Resources\Degrees\Schemas\DegreeInfolist;
use Webkul\Recruitment\Filament\Clusters\Configurations\Resources\Degrees\Tables\DegreesTable;
use Webkul\Recruitment\Models\Degree;

class DegreeResource extends Resource
{
    protected static ?string $model = Degree::class;

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-briefcase';

    protected static ?string $cluster = Configurations::class;

    public static function getModelLabel(): string
    {
        return __('recruitments::filament/clusters/configurations/resources/degree.title');
    }

    public static function getNavigationGroup(): string
    {
        return __('recruitments::filament/clusters/configurations/resources/degree.navigation.group');
    }

    public static function getNavigationLabel(): string
    {
        return __('recruitments::filament/clusters/configurations/resources/degree.navigation.title');
    }

    public static function form(Schema $schema): Schema
    {
        return DegreeForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return DegreesTable::configure($table);
    }

    public static function infolist(Schema $schema): Schema
    {
        return DegreeInfolist::configure($schema);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListDegrees::route('/'),
        ];
    }
}
