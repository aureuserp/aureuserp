<?php

namespace Webkul\Crm\Filament\Clusters\Configurations\Resources;

use Webkul\Crm\Filament\Clusters\Configurations;
use Webkul\Crm\Filament\Clusters\Configurations\Resources\ActivityTypeResource\Pages\CreateActivityType;
use Webkul\Crm\Filament\Clusters\Configurations\Resources\ActivityTypeResource\Pages\EditActivityType;
use Webkul\Crm\Filament\Clusters\Configurations\Resources\ActivityTypeResource\Pages\ListActivityTypes;
use Webkul\Crm\Filament\Clusters\Configurations\Resources\ActivityTypeResource\Pages\ViewActivityType;
use Webkul\Crm\Models\ActivityType;
use Webkul\Support\Filament\Resources\ActivityTypeResource as BaseActivityTypeResource;

class ActivityTypeResource extends BaseActivityTypeResource
{
    protected static ?string $model = ActivityType::class;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-rectangle-stack';

    protected static ?string $cluster = Configurations::class;

    protected static bool $shouldRegisterNavigation = true;

    protected static ?int $navigationSort = 5;

    protected static ?string $pluginName = 'crm';

    public static function getModelLabel(): string
    {
        return __('crm::filament/clusters/configurations/resources/activity-type.model-label');
    }

    public static function getNavigationLabel(): string
    {
        return __('crm::filament/clusters/configurations/resources/activity-type.navigation.title');
    }

    public static function getNavigationGroup(): string
    {
        return __('crm::filament/clusters/configurations/resources/activity-type.navigation.group');
    }

    public static function getPages(): array
    {
        return [
            'index'  => ListActivityTypes::route('/'),
            'create' => CreateActivityType::route('/create'),
            'view'   => ViewActivityType::route('/{record}'),
            'edit'   => EditActivityType::route('/{record}/edit'),
        ];
    }
}
