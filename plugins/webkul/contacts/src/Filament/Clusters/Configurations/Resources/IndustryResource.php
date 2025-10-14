<?php

namespace Webkul\Contact\Filament\Clusters\Configurations\Resources;

use Webkul\Contact\Filament\Clusters\Configurations;
use Webkul\Contact\Filament\Clusters\Configurations\Resources\IndustryResource\Pages\ManageIndustries;
use Webkul\Partner\Filament\Resources\IndustryResource as BaseIndustryResource;

class IndustryResource extends BaseIndustryResource
{
    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-building-office';

    protected static bool $shouldRegisterNavigation = true;

    protected static ?int $navigationSort = 3;

    protected static ?string $cluster = Configurations::class;

    public static function getNavigationLabel(): string
    {
        return __('contacts::filament/clusters/configurations/resources/industry.navigation.title');
    }

    public static function getPages(): array
    {
        return [
            'index' => ManageIndustries::route('/'),
        ];
    }
}
