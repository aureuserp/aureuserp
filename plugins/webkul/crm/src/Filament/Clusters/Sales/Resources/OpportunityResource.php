<?php

namespace Webkul\Crm\Filament\Clusters\Sales\Resources;

use Filament\Navigation\NavigationItem;
use Filament\Resources\Pages\Page;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Webkul\Crm\Filament\Clusters\Sales;
use Webkul\Crm\Filament\Clusters\Sales\Resources\OpportunityResource\Pages\CreateOpportunity;
use Webkul\Crm\Filament\Clusters\Sales\Resources\OpportunityResource\Pages\EditOpportunity;
use Webkul\Crm\Filament\Clusters\Sales\Resources\OpportunityResource\Pages\GraphOpportunities;
use Webkul\Crm\Filament\Clusters\Sales\Resources\OpportunityResource\Pages\KanbanOpportunities;
use Webkul\Crm\Filament\Clusters\Sales\Resources\OpportunityResource\Pages\ListOpportunities;
use Webkul\Crm\Filament\Clusters\Sales\Resources\OpportunityResource\Pages\ManageActivities;
use Webkul\Crm\Filament\Clusters\Sales\Resources\OpportunityResource\Pages\ManageQuotations;
use Webkul\Crm\Filament\Clusters\Sales\Resources\OpportunityResource\Pages\SimilarOpportunities;
use Webkul\Crm\Filament\Clusters\Sales\Resources\OpportunityResource\Pages\ViewOpportunity;
use Webkul\Crm\Filament\Clusters\Sales\Resources\OpportunityResource\Schemas\OpportunityForm;
use Webkul\Crm\Filament\Clusters\Sales\Resources\OpportunityResource\Schemas\OpportunityInfolist;
use Webkul\Crm\Filament\Clusters\Sales\Resources\OpportunityResource\Tables\OpportunitiesTable;
use Webkul\Crm\Filament\Support\SimilarLeads as SimilarLeadsNavigation;
use Webkul\Crm\Models\Lead;

class OpportunityResource extends Resource
{
    protected static ?string $model = Lead::class;

    protected static ?string $slug = 'pipeline';

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-funnel';

    protected static ?string $cluster = Sales::class;

    protected static ?int $navigationSort = 1;

    protected static ?string $recordTitleAttribute = 'name';

    public static function getModelLabel(): string
    {
        return __('crm::filament/clusters/sales/resources/opportunity.model-label');
    }

    public static function getPluralModelLabel(): string
    {
        return __('crm::filament/clusters/sales/resources/opportunity.plural-model-label');
    }

    public static function getNavigationLabel(): string
    {
        return __('crm::filament/clusters/sales/resources/opportunity.navigation.title');
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->opportunities()
            ->withoutGlobalScopes([
                SoftDeletingScope::class,
            ]);
    }

    public static function getGloballySearchableAttributes(): array
    {
        return ['name', 'email', 'partner.name'];
    }

    public static function getGlobalSearchResultDetails(Model $record): array
    {
        return [
            __('crm::filament/clusters/sales/resources/opportunity.global-search.customer') => $record->partner?->name ?? '—',
            __('crm::filament/clusters/sales/resources/opportunity.global-search.stage')    => $record->stage?->name ?? '—',
        ];
    }

    public static function form(Schema $schema): Schema
    {
        return OpportunityForm::configure($schema);
    }

    public static function infolist(Schema $schema): Schema
    {
        return OpportunityInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return OpportunitiesTable::configure($table);
    }

    public static function getRecordSubNavigation(Page $page): array
    {
        return [
            ...$page->generateNavigationItems([
                ViewOpportunity::class,
                EditOpportunity::class,
            ]),
            ...ManageQuotations::navigationItems($page),
            ...ManageActivities::navigationItems($page),
            ...SimilarLeadsNavigation::navigationItems($page, SimilarOpportunities::class),
        ];
    }

    public static function getViewTabs(string $current): array
    {
        $tab = fn (string $page, string $label, string $icon): NavigationItem => NavigationItem::make($page)
            ->label($label)
            ->icon($icon)
            ->url(static::getUrl($page))
            ->isActiveWhen(fn (): bool => $page === $current);

        return [
            $tab('index', __('crm::filament/clusters/sales/resources/opportunity.views.kanban'), 'heroicon-o-view-columns'),
            $tab('list', __('crm::filament/clusters/sales/resources/opportunity.views.list'), 'heroicon-o-list-bullet'),
            $tab('graph', __('crm::filament/clusters/sales/resources/opportunity.views.graph'), 'heroicon-o-chart-bar'),
        ];
    }

    public static function getPages(): array
    {
        return [
            'index'      => KanbanOpportunities::route('/'),
            'list'       => ListOpportunities::route('/list'),
            'graph'      => GraphOpportunities::route('/graph'),
            'create'     => CreateOpportunity::route('/create'),
            'view'       => ViewOpportunity::route('/{record}'),
            'edit'       => EditOpportunity::route('/{record}/edit'),
            'similar'    => SimilarOpportunities::route('/{record}/similar'),
            'quotations' => ManageQuotations::route('/{record}/quotations'),
            'activities' => ManageActivities::route('/{record}/activities'),
        ];
    }
}
