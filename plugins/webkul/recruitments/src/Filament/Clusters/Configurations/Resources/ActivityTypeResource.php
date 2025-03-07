<?php

namespace Webkul\Recruitment\Filament\Clusters\Configurations\Resources;

use Filament\Tables\Table;
use Webkul\Recruitment\Filament\Clusters\Configurations;
use Webkul\Recruitment\Filament\Clusters\Configurations\Resources\ActivityTypeResource\Pages;
use Webkul\Support\Filament\Resources\ActivityTypeResource as BaseActivityTypeResource;
use Webkul\Recruitment\Models\ActivityType;

class ActivityTypeResource extends BaseActivityTypeResource
{
    protected static ?string $model = ActivityType::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    protected static ?string $cluster = Configurations::class;

    protected static bool $shouldRegisterNavigation = true;

    public static function getNavigationGroup(): string
    {
        return __('recruitments::filament/clusters/configurations/resources/activity-type.navigation.group');
    }

    public static function table(Table $table): Table
    {
        return BaseActivityTypeResource::table($table)
            ->modifyQueryUsing(function ($query) {
                return $query->where('plugin', 'recruitments');
            });
    }

    public static function getPages(): array
    {
        return [
            'index'  => Pages\ListActivityTypes::route('/'),
            'create' => Pages\CreateActivityType::route('/create'),
            'edit'   => Pages\EditActivityType::route('/{record}/edit'),
            'view'   => Pages\ViewActivityType::route('/{record}'),
        ];
    }
}
