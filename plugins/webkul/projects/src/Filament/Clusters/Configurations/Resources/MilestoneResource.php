<?php

namespace Webkul\Project\Filament\Clusters\Configurations\Resources;

use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Webkul\Field\Filament\Traits\HasCustomFields;
use Webkul\Project\Filament\Clusters\Configurations;
use Webkul\Project\Filament\Clusters\Configurations\Resources\MilestoneResource\Pages;
use Webkul\Project\Filament\Clusters\Configurations\Resources\MilestoneResource\Schemas\MilestoneForm;
use Webkul\Project\Filament\Clusters\Configurations\Resources\MilestoneResource\Tables\MilestonesTable;
use Webkul\Project\Models\Milestone;
use Webkul\Project\Settings\TaskSettings;

class MilestoneResource extends Resource
{
    use HasCustomFields;

    protected static ?string $model = Milestone::class;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-flag';

    protected static ?int $navigationSort = 3;

    protected static ?string $cluster = Configurations::class;

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()->whereHas('project');
    }

    public static function getNavigationLabel(): string
    {
        return __('projects::filament/clusters/configurations/resources/milestone.navigation.title');
    }

    public static function isDiscovered(): bool
    {
        if (app()->runningInConsole()) {
            return true;
        }

        return settings(TaskSettings::class)->enable_milestones;
    }

    public static function form(Schema $schema): Schema
    {
        return MilestoneForm::configure($schema, static::getCustomFormFields());
    }

    public static function table(Table $table): Table
    {
        return MilestonesTable::configure($table, static::getCustomTableColumns(), static::getCustomTableFilters());
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ManageMilestones::route('/'),
        ];
    }
}
