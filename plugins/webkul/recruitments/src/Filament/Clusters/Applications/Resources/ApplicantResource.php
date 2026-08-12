<?php

namespace Webkul\Recruitment\Filament\Clusters\Applications\Resources;

use Filament\Pages\Page;
use Filament\Resources\RelationManagers\RelationGroup;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Table;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Database\Eloquent\Model;
use Webkul\Field\Filament\Traits\HasCustomFields;
use Webkul\Recruitment\Filament\Clusters\Applications;
use Webkul\Recruitment\Filament\Clusters\Applications\Resources\ApplicantResource\Pages\EditApplicant;
use Webkul\Recruitment\Filament\Clusters\Applications\Resources\ApplicantResource\Pages\ListApplicants;
use Webkul\Recruitment\Filament\Clusters\Applications\Resources\ApplicantResource\Pages\ManageSkill;
use Webkul\Recruitment\Filament\Clusters\Applications\Resources\ApplicantResource\Pages\ViewApplicant;
use Webkul\Recruitment\Filament\Clusters\Applications\Resources\ApplicantResource\RelationManagers\SkillsRelationManager;
use Webkul\Recruitment\Filament\Clusters\Applications\Resources\ApplicantResource\Schemas\ApplicantForm;
use Webkul\Recruitment\Filament\Clusters\Applications\Resources\ApplicantResource\Schemas\ApplicantInfolist;
use Webkul\Recruitment\Filament\Clusters\Applications\Resources\ApplicantResource\Tables\ApplicantsTable;
use Webkul\Recruitment\Models\Applicant;

class ApplicantResource extends Resource
{
    use HasCustomFields;

    protected static ?string $model = Applicant::class;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-user-group';

    protected static ?string $cluster = Applications::class;

    protected static ?int $navigationSort = 2;

    public static function getModelLabel(): string
    {
        return __('recruitments::filament/clusters/applications/resources/applicant.title');
    }

    public static function getNavigationLabel(): string
    {
        return __('recruitments::filament/clusters/applications/resources/applicant.navigation.title');
    }

    public static function getGlobalSearchResultTitle(Model $record): string|Htmlable
    {
        return $record->candidate->name;
    }

    public static function getGloballySearchableAttributes(): array
    {
        return [
            'candidate.name',
        ];
    }

    public static function form(Schema $schema): Schema
    {
        return ApplicantForm::configure($schema, static::getCustomFormFields());
    }

    public static function table(Table $table): Table
    {
        return ApplicantsTable::configure($table, static::getCustomTableColumns(), static::getCustomTableFilters());
    }

    public static function infolist(Schema $schema): Schema
    {
        return ApplicantInfolist::configure($schema, static::getCustomInfolistEntries());
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
}
