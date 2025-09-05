<?php

namespace Webkul\Contact\Filament\Clusters\Configurations\Resources\Banks;

use BackedEnum;
use Webkul\Contact\Filament\Clusters\Configurations;
use Webkul\Contact\Filament\Clusters\Configurations\Resources\Banks\Pages\ManageBanks;
use Webkul\Partner\Filament\Resources\Banks\BankResource as BaseBankResource;

class BankResource extends BaseBankResource
{
    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-building-library';

    protected static bool $shouldRegisterNavigation = true;

    protected static ?int $navigationSort = 4;

    protected static ?string $cluster = Configurations::class;

    public static function getNavigationGroup(): string
    {
        return __('contacts::filament/clusters/configurations/resources/bank.navigation.group');
    }

    public static function getNavigationLabel(): string
    {
        return __('contacts::filament/clusters/configurations/resources/bank.navigation.title');
    }

    public static function getPages(): array
    {
        return [
            'index' => ManageBanks::route('/'),
        ];
    }
}
