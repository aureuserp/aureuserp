<?php

namespace Webkul\Crm\Filament\Clusters\Configurations\Resources;

use Webkul\Crm\Filament\Clusters\Configurations;
use Webkul\Crm\Filament\Clusters\Configurations\Resources\CompanyResource\Pages\CreateCompany;
use Webkul\Crm\Filament\Clusters\Configurations\Resources\CompanyResource\Pages\EditCompany;
use Webkul\Crm\Filament\Clusters\Configurations\Resources\CompanyResource\Pages\ListCompanies;
use Webkul\Crm\Filament\Clusters\Configurations\Resources\CompanyResource\Pages\ViewCompany;
use Webkul\Crm\Filament\Concerns\HasCrmAuthorization;
use Webkul\Crm\Policies\CompanyPolicy;
use Webkul\Security\Filament\Resources\CompanyResource as BaseCompanyResource;

class CompanyResource extends BaseCompanyResource
{
    use HasCrmAuthorization;

    protected static ?string $cluster = Configurations::class;

    protected static ?int $navigationSort = 9;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-building-office';

    protected static bool $isGloballySearchable = false;

    public static function getNavigationLabel(): string
    {
        return __('crm::filament/clusters/configurations/resources/company.navigation.title');
    }

    public static function getNavigationGroup(): string
    {
        return __('crm::filament/clusters/configurations/resources/company.navigation.group');
    }

    public static function getCrmPolicy(): string
    {
        return CompanyPolicy::class;
    }

    public static function getPages(): array
    {
        return [
            'index'  => ListCompanies::route('/'),
            'create' => CreateCompany::route('/create'),
            'view'   => ViewCompany::route('/{record}'),
            'edit'   => EditCompany::route('/{record}/edit'),
        ];
    }
}
