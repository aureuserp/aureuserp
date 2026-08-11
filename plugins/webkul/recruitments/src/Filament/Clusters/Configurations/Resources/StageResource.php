<?php

namespace Webkul\Recruitment\Filament\Clusters\Configurations\Resources;

use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Table;
use Webkul\Recruitment\Filament\Clusters\Configurations;
use Webkul\Recruitment\Filament\Clusters\Configurations\Resources\StageResource\Pages\CreateStage;
use Webkul\Recruitment\Filament\Clusters\Configurations\Resources\StageResource\Pages\EditStage;
use Webkul\Recruitment\Filament\Clusters\Configurations\Resources\StageResource\Pages\ListStages;
use Webkul\Recruitment\Filament\Clusters\Configurations\Resources\StageResource\Pages\ViewStages;
use Webkul\Recruitment\Filament\Clusters\Configurations\Resources\StageResource\Schemas\StageForm;
use Webkul\Recruitment\Filament\Clusters\Configurations\Resources\StageResource\Schemas\StageInfolist;
use Webkul\Recruitment\Filament\Clusters\Configurations\Resources\StageResource\Tables\StagesTable;
use Webkul\Recruitment\Models\Stage;

class StageResource extends Resource
{
    protected static ?string $model = Stage::class;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-cube';

    protected static ?int $navigationSort = 1;

    protected static ?string $cluster = Configurations::class;

    public static function getModelLabel(): string
    {
        return __('recruitments::filament/clusters/configurations/resources/stage.navigation.title');
    }

    public static function getNavigationGroup(): string
    {
        return __('recruitments::filament/clusters/configurations/resources/stage.navigation.group');
    }

    public static function getNavigationLabel(): string
    {
        return __('recruitments::filament/clusters/configurations/resources/stage.navigation.title');
    }

    public static function form(Schema $schema): Schema
    {
        return StageForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return StagesTable::configure($table);
    }

    public static function infolist(Schema $schema): Schema
    {
        return StageInfolist::configure($schema);
    }

    public static function getPages(): array
    {
        return [
            'index'  => ListStages::route('/'),
            'create' => CreateStage::route('/create'),
            'edit'   => EditStage::route('/{record}/edit'),
            'view'   => ViewStages::route('/{record}'),
        ];
    }
}
