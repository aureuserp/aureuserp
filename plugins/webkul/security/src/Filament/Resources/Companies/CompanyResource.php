<?php

namespace Webkul\Security\Filament\Resources\Companies;

use BackedEnum;
use Filament\Panel;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Webkul\Field\Filament\Traits\HasCustomFields;
use Webkul\Security\Filament\Resources\Companies\Pages\CreateCompany;
use Webkul\Security\Filament\Resources\Companies\Pages\EditCompany;
use Webkul\Security\Filament\Resources\Companies\Pages\ListCompanies;
use Webkul\Security\Filament\Resources\Companies\Pages\ViewCompany;
use Webkul\Security\Filament\Resources\Companies\RelationManagers\BranchesRelationManager;
use Webkul\Security\Filament\Resources\Companies\Schemas\CompanyForm;
use Webkul\Security\Filament\Resources\Companies\Schemas\CompanyInfolist;
use Webkul\Security\Filament\Resources\Companies\Tables\CompaniesTable;
use Webkul\Support\Models\Company;

class CompanyResource extends Resource
{
    use HasCustomFields;

    protected static ?string $model = Company::class;

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-building-office';

    protected static ?int $navigationSort = 2;

    protected static ?string $slug = 'companies';

    public static function getSlug(?Panel $panel = null): string
    {
        return static::$slug ?? parent::getSlug($panel);
    }

    public static function getNavigationLabel(): string
    {
        return __('security::filament/resources/company.navigation.title');
    }

    public static function getNavigationGroup(): string
    {
        return __('security::filament/resources/company.navigation.group');
    }

    public static function getGloballySearchableAttributes(): array
    {
        return ['name', 'email'];
    }

    public static function getGlobalSearchResultDetails(Model $record): array
    {
        return [
            __('security::filament/resources/company.global-search.name')  => $record->name ?? '—',
            __('security::filament/resources/company.global-search.email') => $record->email ?? '—',
        ];
    }

    public static function form(Schema $schema): Schema
    {
        return CompanyForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return CompaniesTable::configure($table);
    }

    public static function infolist(Schema $schema): Schema
    {
        return CompanyInfolist::configure($schema);
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

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->withoutGlobalScopes([
                SoftDeletingScope::class,
            ]);
    }
}
