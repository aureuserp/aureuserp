<?php

namespace Webkul\Recruitment\Filament\Clusters\Applications\Resources;

use Filament\Resources\Pages\Page;
use Filament\Resources\RelationManagers\RelationGroup;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Model;
use Webkul\Field\Filament\Traits\HasCustomFields;
use Webkul\Recruitment\Filament\Clusters\Applications;
use Webkul\Recruitment\Filament\Clusters\Applications\Resources\CandidateResource\Pages\CreateCandidate;
use Webkul\Recruitment\Filament\Clusters\Applications\Resources\CandidateResource\Pages\EditCandidate;
use Webkul\Recruitment\Filament\Clusters\Applications\Resources\CandidateResource\Pages\ListCandidates;
use Webkul\Recruitment\Filament\Clusters\Applications\Resources\CandidateResource\Pages\ManageSkill;
use Webkul\Recruitment\Filament\Clusters\Applications\Resources\CandidateResource\Pages\ViewCandidate;
use Webkul\Recruitment\Filament\Clusters\Applications\Resources\CandidateResource\RelationManagers\SkillsRelationManager;
use Webkul\Recruitment\Filament\Clusters\Applications\Resources\CandidateResource\Schemas\CandidateForm;
use Webkul\Recruitment\Filament\Clusters\Applications\Resources\CandidateResource\Schemas\CandidateInfolist;
use Webkul\Recruitment\Filament\Clusters\Applications\Resources\CandidateResource\Tables\CandidatesTable;
use Webkul\Recruitment\Models\Candidate;

class CandidateResource extends Resource
{
    use HasCustomFields;

    protected static ?string $model = Candidate::class;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-user-group';

    protected static ?string $cluster = Applications::class;

    protected static ?int $navigationSort = 3;

    protected static ?string $recordTitleAttribute = 'name';

    public static function getModelLabel(): string
    {
        return __('recruitments::filament/clusters/applications/resources/candidate.title');
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

    public static function getGlobalSearchResultDetails(Model $record): array
    {
        return [
            __('recruitments::filament/clusters/applications/resources/candidate.global-search.email-from') => $record?->email_from ?? '—',
            __('recruitments::filament/clusters/applications/resources/candidate.global-search.phone')      => $record?->phone ?? '—',
            __('recruitments::filament/clusters/applications/resources/candidate.global-search.company')    => $record?->company?->name ?? '—',
            __('recruitments::filament/clusters/applications/resources/candidate.global-search.degree')     => $record?->degree?->name ?? '—',
        ];
    }

    public static function form(Schema $schema): Schema
    {
        return CandidateForm::configure($schema, static::getCustomFormFields());
    }

    public static function table(Table $table): Table
    {
        return CandidatesTable::configure($table, static::getCustomTableColumns(), static::getCustomTableFilters());
    }

    public static function infolist(Schema $schema): Schema
    {
        return CandidateInfolist::configure($schema, static::getCustomInfolistEntries());
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
