<?php

namespace Webkul\Project\Filament\Clusters\Configurations\Resources\Milestones;

use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Table;
use Webkul\Project\Filament\Clusters\Configurations;
use Webkul\Project\Filament\Clusters\Configurations\Resources\Milestones\Schemas\MilestoneForm;
use Webkul\Project\Filament\Clusters\Configurations\Resources\Milestones\Tables\MilestonesTable;
use Webkul\Project\Models\Milestone;
use Webkul\Project\Settings\TaskSettings;

class MilestoneResource extends Resource
{
    protected static ?string $model = Milestone::class;

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-flag';

    protected static ?int $navigationSort = 3;

    protected static ?string $cluster = Configurations::class;

    public static function getNavigationLabel(): string
    {
        return __('projects::filament/clusters/configurations/resources/milestone.navigation.title');
    }

    public static function isDiscovered(): bool
    {
        if (app()->runningInConsole()) {
            return true;
        }

        return app(TaskSettings::class)->enable_milestones;
    }

    public static function form(Schema $schema): Schema
    {
        return MilestoneForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return MilestonesTable::configure($table);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ManageMilestones::route('/'),
        ];
    }
}
