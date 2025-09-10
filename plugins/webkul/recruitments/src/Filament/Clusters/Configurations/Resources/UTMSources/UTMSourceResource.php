<?php

namespace Webkul\Recruitment\Filament\Clusters\Configurations\Resources\UTMSources;

use BackedEnum;
use Filament\Panel;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Table;
use Webkul\Recruitment\Filament\Clusters\Configurations;
use Webkul\Recruitment\Filament\Clusters\Configurations\Resources\UTMSources\Pages\ListUTMSources;
use Webkul\Recruitment\Filament\Clusters\Configurations\Resources\UTMSources\Schemas\UTMSourceForm;
use Webkul\Recruitment\Filament\Clusters\Configurations\Resources\UTMSources\Schemas\UTMSourceInfolist;
use Webkul\Recruitment\Filament\Clusters\Configurations\Resources\UTMSources\Tables\UTMSourcesTable;
use Webkul\Recruitment\Models\UTMSource;

class UTMSourceResource extends Resource
{
    protected static ?string $model = UTMSource::class;

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-globe-americas';

    protected static ?string $cluster = Configurations::class;

    public static function getModelLabel(): string
    {
        return __('recruitments::filament/clusters/configurations/resources/utm-source.title');
    }

    public static function getNavigationGroup(): string
    {
        return __('recruitments::filament/clusters/configurations/resources/utm-source.navigation.group');
    }

    public static function getNavigationLabel(): string
    {
        return __('recruitments::filament/clusters/configurations/resources/utm-source.navigation.title');
    }

    public static function form(Schema $schema): Schema
    {
        return UTMSourceForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return UTMSourcesTable::configure($table);
    }

    public static function infolist(Schema $schema): Schema
    {
        return UTMSourceInfolist::configure($schema);
    }

    public static function getSlug(?Panel $panel = null): string
    {
        return 'utm-source';
    }

    public static function getPages(): array
    {
        return [
            'index' => ListUTMSources::route('/'),
        ];
    }
}
