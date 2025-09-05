<?php

namespace Webkul\Recruitment\Filament\Clusters\Applications\Resources\Candidates;

use BackedEnum;
use Filament\Pages\Enums\SubNavigationPosition;
use Filament\Resources\Pages\Page;
use Filament\Resources\RelationManagers\RelationGroup;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Route;
use Webkul\Recruitment\Filament\Clusters\Applications;
use Webkul\Recruitment\Filament\Clusters\Applications\Resources\Candidates\Pages\CreateCandidate;
use Webkul\Recruitment\Filament\Clusters\Applications\Resources\Candidates\Pages\EditCandidate;
use Webkul\Recruitment\Filament\Clusters\Applications\Resources\Candidates\Pages\ListCandidates;
use Webkul\Recruitment\Filament\Clusters\Applications\Resources\Candidates\Pages\ManageSkill;
use Webkul\Recruitment\Filament\Clusters\Applications\Resources\Candidates\Pages\ViewCandidate;
use Webkul\Recruitment\Filament\Clusters\Applications\Resources\Candidates\RelationManagers\SkillsRelationManager;
use Webkul\Recruitment\Filament\Clusters\Applications\Resources\Candidates\Schemas\CandidateForm;
use Webkul\Recruitment\Filament\Clusters\Applications\Resources\Candidates\Schemas\CandidateInfolist;
use Webkul\Recruitment\Filament\Clusters\Applications\Resources\Candidates\Tables\CandidatesTable;
use Webkul\Recruitment\Models\Candidate;

class CandidateResource extends Resource
{
    protected static ?string $model = Candidate::class;

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-user-group';

    protected static ?string $cluster = Applications::class;

    protected static ?int $navigationSort = 3;

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
        return __('recruitments::filament/clusters/applications/resources/candidate.title');
    }

    public static function getNavigationGroup(): string
    {
        return __('recruitments::filament/clusters/applications/resources/candidate.navigation.group');
    }

    public static function getNavigationLabel(): string
    {
        return __('recruitments::filament/clusters/applications/resources/candidate.navigation.title');
    }

    public static function getGloballySearchableAttributes(): array
    {
        return [
            'name',
            'email_from',
            'phone',
            'company.name',
            'degree.name',
        ];
    }

    public static function form(Schema $schema): Schema
    {
        return CandidateForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return CandidatesTable::configure($table);
    }

    public static function infolist(Schema $schema): Schema
    {
        return CandidateInfolist::configure($schema);
    }

    public static function getRecordSubNavigation(Page $page): array
    {
        return $page->generateNavigationItems([
            ViewCandidate::class,
            EditCandidate::class,
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
            'index'  => ListCandidates::route('/'),
            'create' => CreateCandidate::route('/create'),
            'edit'   => EditCandidate::route('/{record}/edit'),
            'view'   => ViewCandidate::route('/{record}'),
            'skills' => ManageSkill::route('/{record}/skills'),
        ];
    }
}
