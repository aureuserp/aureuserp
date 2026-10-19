<?php

namespace Webkul\Crm\Filament\Clusters\Configurations\Resources;

use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Table;
use Webkul\Crm\Filament\Clusters\Configurations;
use Webkul\Crm\Filament\Clusters\Configurations\Resources\StageResource\Pages\ManageStages;
use Webkul\Crm\Filament\Clusters\Configurations\Resources\StageResource\Schemas\StageForm;
use Webkul\Crm\Filament\Clusters\Configurations\Resources\StageResource\Tables\StagesTable;
use Webkul\Crm\Models\Stage;

class StageResource extends Resource
{
    protected static ?string $model = Stage::class;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-squares-2x2';

    protected static ?string $cluster = Configurations::class;

    protected static ?int $navigationSort = 2;

    protected static ?string $recordTitleAttribute = 'name';

    public static function getModelLabel(): string
    {
        return __('crm::filament/clusters/configurations/resources/stage.model-label');
    }

    public static function getNavigationLabel(): string
    {
        return __('crm::filament/clusters/configurations/resources/stage.navigation.title');
    }

    public static function getNavigationGroup(): string
    {
        return __('crm::filament/clusters/configurations/resources/stage.navigation.group');
    }

    public static function form(Schema $schema): Schema
    {
        return StageForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return StagesTable::configure($table);
    }

    public static function getPages(): array
    {
        return [
            'index' => ManageStages::route('/'),
        ];
    }
}
