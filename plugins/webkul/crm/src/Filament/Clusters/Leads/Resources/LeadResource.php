<?php

namespace Webkul\Crm\Filament\Clusters\Leads\Resources;

use Filament\Resources\Pages\Page;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Webkul\Crm\Filament\Clusters\Leads;
use Webkul\Crm\Filament\Clusters\Leads\Resources\LeadResource\Pages\CreateLead;
use Webkul\Crm\Filament\Clusters\Leads\Resources\LeadResource\Pages\EditLead;
use Webkul\Crm\Filament\Clusters\Leads\Resources\LeadResource\Pages\ListLeads;
use Webkul\Crm\Filament\Clusters\Leads\Resources\LeadResource\Pages\ManageActivities;
use Webkul\Crm\Filament\Clusters\Leads\Resources\LeadResource\Pages\SimilarLeads;
use Webkul\Crm\Filament\Clusters\Leads\Resources\LeadResource\Pages\ViewLead;
use Webkul\Crm\Filament\Clusters\Leads\Resources\LeadResource\Schemas\LeadForm;
use Webkul\Crm\Filament\Clusters\Leads\Resources\LeadResource\Schemas\LeadInfolist;
use Webkul\Crm\Filament\Clusters\Leads\Resources\LeadResource\Tables\LeadsTable;
use Webkul\Crm\Filament\Concerns\HasCRMAuthorization;
use Webkul\Crm\Filament\Support\SimilarLeads as SimilarLeadsNavigation;
use Webkul\Crm\Models\Lead;
use Webkul\Crm\Policies\LeadRecordPolicy;
use Webkul\Crm\Settings\CRMSettings;

class LeadResource extends Resource
{
    use HasCRMAuthorization;

    protected static ?string $model = Lead::class;

    protected static ?string $slug = 'leads';

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-inbox-arrow-down';

    protected static ?string $cluster = Leads::class;

    protected static ?int $navigationSort = 1;

    protected static ?string $recordTitleAttribute = 'name';

    public static function getCRMPolicy(): string
    {
        return LeadRecordPolicy::class;
    }

    public static function isQualificationStepEnabled(): bool
    {
        return (bool) app(CRMSettings::class)->use_leads;
    }

    public static function canViewAny(): bool
    {
        return static::isQualificationStepEnabled() && parent::canViewAny();
    }

    public static function canCreate(): bool
    {
        return static::isQualificationStepEnabled() && parent::canCreate();
    }

    public static function getModelLabel(): string
    {
        return __('crm::filament/clusters/leads/resources/lead.model-label');
    }

    public static function getPluralModelLabel(): string
    {
        return __('crm::filament/clusters/leads/resources/lead.plural-model-label');
    }

    public static function getNavigationLabel(): string
    {
        return __('crm::filament/clusters/leads/resources/lead.navigation.title');
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->leads()
            ->withoutGlobalScopes([
                SoftDeletingScope::class,
            ]);
    }

    public static function getGloballySearchableAttributes(): array
    {
        return ['name', 'contact_name', 'company_name', 'email'];
    }

    public static function getGlobalSearchResultDetails(Model $record): array
    {
        return [
            __('crm::filament/clusters/leads/resources/lead.global-search.contact') => $record->contact_name ?? '—',
            __('crm::filament/clusters/leads/resources/lead.global-search.company') => $record->company_name ?? '—',
        ];
    }

    public static function form(Schema $schema): Schema
    {
        return LeadForm::configure($schema);
    }

    public static function infolist(Schema $schema): Schema
    {
        return LeadInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return LeadsTable::configure($table);
    }

    public static function getRecordSubNavigation(Page $page): array
    {
        return [
            ...$page->generateNavigationItems([
                ViewLead::class,
                EditLead::class,
            ]),
            ...ManageActivities::navigationItems($page),
            ...SimilarLeadsNavigation::navigationItems($page, SimilarLeads::class),
        ];
    }

    public static function getPages(): array
    {
        return [
            'index'      => ListLeads::route('/'),
            'create'     => CreateLead::route('/create'),
            'view'       => ViewLead::route('/{record}'),
            'edit'       => EditLead::route('/{record}/edit'),
            'similar'    => SimilarLeads::route('/{record}/similar'),
            'activities' => ManageActivities::route('/{record}/activities'),
        ];
    }
}
