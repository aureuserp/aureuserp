<?php

namespace Webkul\Crm\Filament\Clusters\Sales\Resources\TeamResource\Pages;

use Filament\Actions\Action;
use Filament\Actions\ActionGroup;
use Filament\Actions\CreateAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ManageRelatedRecords;
use Filament\Schemas\Schema;
use Filament\Tables\Table;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Database\Eloquent\Model;
use Webkul\Chatter\Filament\Actions\ActivityTableAction;
use Webkul\Crm\Filament\Clusters\Sales\Resources\OpportunityResource;
use Webkul\Crm\Filament\Clusters\Sales\Resources\OpportunityResource\Actions\MarkAsLostAction;
use Webkul\Crm\Filament\Clusters\Sales\Resources\OpportunityResource\Actions\MarkAsWonAction;
use Webkul\Crm\Filament\Clusters\Sales\Resources\OpportunityResource\Schemas\OpportunityForm;
use Webkul\Crm\Filament\Clusters\Sales\Resources\OpportunityResource\Tables\OpportunitiesTable;
use Webkul\Crm\Filament\Clusters\Sales\Resources\TeamResource;
use Webkul\Crm\Filament\Concerns\HasCrmClusterNavigation;
use Webkul\Crm\Models\Lead;

class ManageTeamOpportunities extends ManageRelatedRecords
{
    use HasCrmClusterNavigation;

    protected static string $resource = TeamResource::class;

    protected static string $relationship = 'opportunities';

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-funnel';

    public static function canAccess(array $parameters = []): bool
    {
        $team = $parameters['record'] ?? null;

        return parent::canAccess($parameters)
            && ($team instanceof Model ? TeamResource::canView($team) : TeamResource::canViewAny());
    }

    public static function getNavigationLabel(): string
    {
        return __('crm::filament/clusters/sales/resources/team/pages/manage-team-opportunities.navigation.title');
    }

    public function getTitle(): string|Htmlable
    {
        return __('crm::filament/clusters/sales/resources/team/pages/manage-team-opportunities.title', ['team' => $this->getRecord()->name]);
    }

    public function form(Schema $schema): Schema
    {
        return OpportunityForm::configure($schema, withinTeam: true);
    }

    public function table(Table $table): Table
    {
        return OpportunitiesTable::configure($table, withinTeam: true)
            ->modifyQueryUsing(fn ($query) => $query->opportunities())
            ->modelLabel(OpportunityResource::getModelLabel())
            ->pluralModelLabel(OpportunityResource::getPluralModelLabel())
            ->recordUrl(fn (Lead $record): string => OpportunityResource::getUrl('view', ['record' => $record]))
            ->recordActions([
                ActivityTableAction::make()
                    ->tooltip(__('crm::filament/clusters/sales/resources/opportunity.table.actions.schedule-activity.label'))
                    ->visible(fn (Lead $record): bool => OpportunityResource::canEdit($record)),
                ActionGroup::make([
                    ViewAction::make()
                        ->url(fn (Lead $record): string => OpportunityResource::getUrl('view', ['record' => $record])),
                    EditAction::make()
                        ->url(fn (Lead $record): string => OpportunityResource::getUrl('edit', ['record' => $record])),
                    MarkAsWonAction::make(),
                    MarkAsLostAction::make(),
                ]),
            ])
            ->toolbarActions([])
            ->headerActions([
                Action::make('kanbanView')
                    ->label(__('crm::filament/clusters/sales/resources/team/pages/manage-team-opportunities.header-actions.kanban.label'))
                    ->icon('heroicon-o-view-columns')
                    ->color('gray')
                    ->url(fn (): string => OpportunityResource::getUrl('index', ['team' => $this->getRecord()->getKey()])),
                CreateAction::make()
                    ->label(__('crm::filament/clusters/sales/resources/team/pages/manage-team-opportunities.header-actions.create.label'))
                    ->icon('heroicon-o-plus-circle')
                    ->modalWidth('6xl')
                    ->successNotification(
                        Notification::make()
                            ->success()
                            ->title(__('crm::filament/clusters/sales/resources/team/pages/manage-team-opportunities.header-actions.create.notification.title'))
                            ->body(__('crm::filament/clusters/sales/resources/team/pages/manage-team-opportunities.header-actions.create.notification.body')),
                    ),
            ]);
    }
}
