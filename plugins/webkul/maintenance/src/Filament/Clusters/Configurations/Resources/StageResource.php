<?php

namespace Webkul\Maintenance\Filament\Clusters\Configurations\Resources;

use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Table;
use Webkul\Maintenance\Filament\Clusters\Configurations;
use Webkul\Maintenance\Filament\Clusters\Configurations\Resources\StageResource\Pages\ManageStages;
use Webkul\Maintenance\Filament\Clusters\Configurations\Resources\StageResource\Schemas\StageForm;
use Webkul\Maintenance\Filament\Clusters\Configurations\Resources\StageResource\Schemas\StageInfolist;
use Webkul\Maintenance\Filament\Clusters\Configurations\Resources\StageResource\Tables\StagesTable;
use Webkul\Maintenance\Models\Stage;

class StageResource extends Resource
{
    protected static ?string $model = Stage::class;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-squares-2x2';

    protected static ?int $navigationSort = 1;

    protected static ?string $cluster = Configurations::class;

    protected static ?string $recordTitleAttribute = 'name';

    public static function getModelLabel(): string
    {
        return __('maintenance::models/stage.title');
    }

    public static function getNavigationLabel(): string
    {
        return __('maintenance::filament/clusters/configurations/resources/stage.navigation.title');
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
            'index' => ManageStages::route('/'),
        ];
    }
}
