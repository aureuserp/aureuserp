<?php

namespace Webkul\Recruitment\Filament\Clusters\Applications\Resources\Applicants;

use BackedEnum;
use Filament\Pages\Enums\SubNavigationPosition;
use Filament\Pages\Page;
use Filament\Resources\RelationManagers\RelationGroup;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\Facades\Route;
use Webkul\Recruitment\Filament\Clusters\Applications;
use Webkul\Recruitment\Filament\Clusters\Applications\Resources\Applicants\Pages\EditApplicant;
use Webkul\Recruitment\Filament\Clusters\Applications\Resources\Applicants\Pages\ListApplicants;
use Webkul\Recruitment\Filament\Clusters\Applications\Resources\Applicants\Pages\ManageSkill;
use Webkul\Recruitment\Filament\Clusters\Applications\Resources\Applicants\Pages\ViewApplicant;
use Webkul\Recruitment\Filament\Clusters\Applications\Resources\Applicants\RelationManagers\SkillsRelationManager;
use Webkul\Recruitment\Filament\Clusters\Applications\Resources\Applicants\Schemas\ApplicantForm;
use Webkul\Recruitment\Filament\Clusters\Applications\Resources\Applicants\Schemas\ApplicantInfolist;
use Webkul\Recruitment\Filament\Clusters\Applications\Resources\Applicants\Tables\ApplicantsTable;
use Webkul\Recruitment\Models\Applicant;

class ApplicantResource extends Resource
{
    protected static ?string $model = Applicant::class;

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-user-group';

    protected static ?string $cluster = Applications::class;

    protected static ?int $navigationSort = 2;

    public static function getSubNavigationPosition(): SubNavigationPosition
    {
        $currentRoute = Route::currentRouteName();

        if ($currentRoute === 'livewire.update') {
            $previousUrl = url()->previous();

            return str_contains($previousUrl, '/index') || str_contains($previousUrl, '?tableGrouping') || str_contains($previousUrl, '?tableFilters')
                ? SubNavigationPosition::Start
                : SubNavigationPosition::Top;
        }

        return str_contains($currentRoute, '.index')
            ? SubNavigationPosition::Start
            : SubNavigationPosition::Top;
    }

    public static function getModelLabel(): string
    {
        return __('recruitments::filament/clusters/applications/resources/applicant.title');
    }

    public static function getNavigationGroup(): string
    {
        return __('recruitments::filament/clusters/applications/resources/applicant.navigation.group');
    }

    public static function getNavigationLabel(): string
    {
        return __('recruitments::filament/clusters/applications/resources/applicant.navigation.title');
    }

    public static function getGloballySearchableAttributes(): array
    {
        return [
            'candidate.name',
        ];
    }

    public static function form(Schema $schema): Schema
    {
        return ApplicantForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return ApplicantsTable::configure($table);
    }

    public static function infolist(Schema $schema): Schema
    {
        return ApplicantInfolist::configure($schema);
    }

    public static function getRecordSubNavigation(Page $page): array
    {
        return $page->generateNavigationItems([
            ViewApplicant::class,
            EditApplicant::class,
            ManageSkill::class,
        ]);
    }

    public static function getRelations(): array
    {
        return [
            RelationGroup::make('Manage Skills', [
                SkillsRelationManager::class,
            ])
                ->icon('heroicon-o-bolt'),
        ];
    }

    public static function getPages(): array
    {
        return [
            'index'  => ListApplicants::route('/'),
            'view'   => ViewApplicant::route('/{record}'),
            'edit'   => EditApplicant::route('/{record}/edit'),
            'skills' => ManageSkill::route('/{record}/skills'),
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
