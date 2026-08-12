<?php

namespace Webkul\Support\Filament\Resources;

use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Webkul\Field\Filament\Traits\HasCustomFields;
use Webkul\Support\Enums\NavigationGroup;
use Webkul\Support\Filament\Resources\CompanyResource\Pages\CreateCompany;
use Webkul\Support\Filament\Resources\CompanyResource\Pages\EditCompany;
use Webkul\Support\Filament\Resources\CompanyResource\Pages\ListCompanies;
use Webkul\Support\Filament\Resources\CompanyResource\Pages\ViewCompany;
use Webkul\Support\Filament\Resources\CompanyResource\RelationManagers\BranchesRelationManager;
use Webkul\Support\Filament\Resources\CompanyResource\Schemas\CompanyForm;
use Webkul\Support\Filament\Resources\CompanyResource\Schemas\CompanyInfolist;
use Webkul\Support\Filament\Resources\CompanyResource\Tables\CompaniesTable;
use Webkul\Support\Models\Company;
use Webkul\Support\Models\Scopes\AllowedCompanyScope;

class CompanyResource extends Resource
{
    use HasCustomFields;

    protected static ?string $model = Company::class;

    protected static ?int $navigationSort = 2;

    protected static bool $shouldRegisterNavigation = false;

    protected static ?string $recordTitleAttribute = 'name';

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->ownership()
            ->withoutGlobalScope(AllowedCompanyScope::class);
    }

    public static function getNavigationLabel(): string
    {
        return __('support::filament/resources/company.navigation.title');
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
            __('support::filament/resources/company.global-search.email') => $record->email ?? '—',
        ];
    }

    public static function form(Schema $schema): Schema
    {
        return CompanyForm::configure($schema, static::getCustomFormFields());
    }

    public static function table(Table $table): Table
    {
        return CompaniesTable::configure($table, static::getCustomTableColumns(), static::getCustomTableFilters());
    }

    public static function infolist(Schema $schema): Schema
    {
        return CompanyInfolist::configure($schema, static::getCustomInfolistEntries());
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
