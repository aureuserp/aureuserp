<?php

namespace Webkul\Security\Filament\Resources;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Webkul\Security\Filament\Resources\CompanyResource\Pages\CreateCompany;
use Webkul\Security\Filament\Resources\CompanyResource\Pages\EditCompany;
use Webkul\Security\Filament\Resources\CompanyResource\Pages\ListCompanies;
use Webkul\Security\Filament\Resources\CompanyResource\Pages\ViewCompany;
use Webkul\Security\Filament\Resources\CompanyResource\RelationManagers\BranchesRelationManager;
use Webkul\Security\Models\Company;
use Webkul\Support\Enums\NavigationGroup;
use Webkul\Support\Filament\Resources\CompanyResource as BaseCompanyResource;
use Webkul\Support\Models\Scopes\AllowedCompanyScope;

class CompanyResource extends BaseCompanyResource
{
    protected static ?string $model = Company::class;

    protected static ?int $navigationSort = 2;

    protected static bool $shouldRegisterNavigation = true;

    protected static ?string $recordTitleAttribute = 'name';

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->ownership()
            ->withoutGlobalScope(AllowedCompanyScope::class);
    }

    public static function getNavigationLabel(): string
    {
        return __('security::filament/resources/company.navigation.title');
    }

    public static function getNavigationGroup(): string|\UnitEnum
    {
        return NavigationGroup::Setting;
    }

    public static function getGloballySearchableAttributes(): array
    {
        return ['name', 'email'];
    }

    public static function getGlobalSearchResultDetails(Model $record): array
    {
        return [
            __('security::filament/resources/company.global-search.email') => $record->email ?? '—',
        ];
    }

    public static function getRelations(): array
    {
        return [
            BranchesRelationManager::class,
        ];
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
