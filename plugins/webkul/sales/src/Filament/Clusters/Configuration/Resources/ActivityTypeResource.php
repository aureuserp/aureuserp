<?php

namespace Webkul\Sale\Filament\Clusters\Configuration\Resources;

use Webkul\Sale\Filament\Clusters\Configuration;
use Webkul\Sale\Filament\Clusters\Configuration\Resources\ActivityTypeResource\Pages\CreateActivityType;
use Webkul\Sale\Filament\Clusters\Configuration\Resources\ActivityTypeResource\Pages\EditActivityType;
use Webkul\Sale\Filament\Clusters\Configuration\Resources\ActivityTypeResource\Pages\ListActivityTypes;
use Webkul\Sale\Filament\Clusters\Configuration\Resources\ActivityTypeResource\Pages\ViewActivityType;
use Webkul\Sale\Models\ActivityType;
use Webkul\Support\Filament\Resources\ActivityTypeResource as BaseActivityTypeResource;

class ActivityTypeResource extends BaseActivityTypeResource
{
    protected static ?string $model = ActivityType::class;

    protected static bool $shouldRegisterNavigation = true;

    protected static ?string $cluster = Configuration::class;

    public static function getModelLabel(): string
    {
        return __('Activity Type');
    }

    public static function getNavigationGroup(): ?string
    {
        return __('Activities');
    }

    public static function getPages(): array
    {
        return [
            'index'  => ListActivityTypes::route('/'),
            'create' => CreateActivityType::route('/create'),
            'edit'   => EditActivityType::route('/{record}/edit'),
            'view'   => ViewActivityType::route('/{record}'),
        ];
    }
}
