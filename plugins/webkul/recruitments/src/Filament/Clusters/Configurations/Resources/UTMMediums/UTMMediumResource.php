<?php

namespace Webkul\Recruitment\Filament\Clusters\Configurations\Resources\UTMMediums;

use BackedEnum;
use Filament\Panel;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Table;
use Webkul\Recruitment\Filament\Clusters\Configurations;
use Webkul\Recruitment\Filament\Clusters\Configurations\Resources\UTMMediums\Pages\ListUTMMedia;
use Webkul\Recruitment\Filament\Clusters\Configurations\Resources\UTMMediums\Schemas\UTMMediumForm;
use Webkul\Recruitment\Filament\Clusters\Configurations\Resources\UTMMediums\Schemas\UTMMediumInfolist;
use Webkul\Recruitment\Filament\Clusters\Configurations\Resources\UTMMediums\Tables\UTMMediumsTable;
use Webkul\Recruitment\Models\UTMMedium;

class UTMMediumResource extends Resource
{
    protected static ?string $model = UTMMedium::class;

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-arrow-path-rounded-square';

    protected static ?string $cluster = Configurations::class;

    public static function getModelLabel(): string
    {
        return __('recruitments::filament/clusters/configurations/resources/utm-medium.title');
    }

    public static function getNavigationGroup(): string
    {
        return __('recruitments::filament/clusters/configurations/resources/utm-medium.navigation.group');
    }

    public static function getNavigationLabel(): string
    {
        return __('recruitments::filament/clusters/configurations/resources/utm-medium.navigation.title');
    }

    public static function form(Schema $schema): Schema
    {
        return UTMMediumForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return UTMMediumsTable::configure($table);
    }

    public static function infolist(Schema $schema): Schema
    {
        return UTMMediumInfolist::configure($schema);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListUTMMedia::route('/'),
        ];
    }

    public static function getSlug(?Panel $panel = null): string
    {
        return 'utm-mediums';
    }
}
