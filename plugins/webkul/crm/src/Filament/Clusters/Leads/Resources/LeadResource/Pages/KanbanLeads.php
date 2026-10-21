<?php

namespace Webkul\Crm\Filament\Clusters\Leads\Resources\LeadResource\Pages;

use Filament\Actions\Action;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ListRecords;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;
use Webkul\Crm\Enums\OpportunityPriority;
use Webkul\Crm\Filament\Actions\CrmExportAction;
use Webkul\Crm\Filament\Actions\CrmImportAction;
use Webkul\Crm\Filament\Clusters\Leads\Resources\LeadResource;
use Webkul\Crm\Filament\Concerns\HasCrmClusterNavigation;
use Webkul\Crm\Filament\Concerns\ManagesStageKanbanColumns;
use Webkul\Crm\Filament\Exports\LeadExporter;
use Webkul\Crm\Filament\Imports\LeadImporter;
use Webkul\Crm\Models\Lead;
use Webkul\Crm\Models\Stage;
use Webkul\Sale\Models\Team;
use Wezlo\FilamentKanban\Concerns\HasKanbanBoard;
use Wezlo\FilamentKanban\KanbanBoard;

class KanbanLeads extends ListRecords
{
    use HasCrmClusterNavigation, HasKanbanBoard, ManagesStageKanbanColumns;

    protected static string $resource = LeadResource::class;

    public function mount(): void
    {
        parent::mount();

        $this->resetKanbanFilters();

        $this->kanbanFilters['user_id'] = Auth::id();
    }

    public function resetKanbanFilters(): void
    {
        $this->kanbanFilters = [
            'user_id'  => null,
            'team_id'  => null,
            'priority' => null,
        ];
    }

    public function getTitle(): string
    {
        return __('crm::filament/clusters/leads/resources/lead.navigation.title');
    }

    public function getBreadcrumb(): string
    {
        return __('crm::filament/clusters/leads/resources/lead.views.kanban');
    }

    protected function getHeaderActions(): array
    {
        return [
            ...LeadResource::getViewSwitcherActions('index'),
            CrmImportAction::make()
                ->importer(LeadImporter::class)
                ->visible(fn (): bool => LeadResource::canCreate()),
            CrmExportAction::make()
                ->exporter(LeadExporter::class)
                ->modifyQueryUsing(fn (Builder $query): Builder => CrmExportAction::limitToVisibleRecords(
                    $query->whereIn('crm_leads.id', $this->getKanbanQuery($this->getKanbanBoard())->reorder()->pluck('crm_leads.id')),
                )),
            Action::make('create')
                ->label(__('crm::filament/clusters/leads/resources/lead.actions.create.label'))
                ->icon('heroicon-o-plus-circle')
                ->url(fn (): string => LeadResource::getUrl('create'))
                ->visible(fn (): bool => LeadResource::canCreate()),
        ];
    }

    public function kanban(KanbanBoard $kanban): KanbanBoard
    {
        return $kanban
            ->relationshipColumn('stage', 'name', Stage::class, orderAttribute: 'sort')
            ->excludeColumns(Stage::query()->where('is_won', true)->pluck('id')->map(fn ($id): string => (string) $id)->all())
            ->modifyQueryUsing(fn (Builder $query): Builder => $query->withoutTrashed()->with(['user', 'tags'])->latest())
            ->cardTitle(fn (Lead $record): string => $record->name)
            ->cardDescription(fn (Lead $record): string => collect([
                $record->company_name ?: $record->contact_name,
                $record->email,
            ])->filter()->implode(' · '))
            ->cardBadges(fn (Lead $record): array => collect([
                $record->priority && $record->priority !== OpportunityPriority::Low
                    ? ['label' => $record->priority->getLabel(), 'color' => $record->priority->getColor()]
                    : null,
                $record->user ? ['label' => $record->user->name, 'color' => 'gray'] : null,
                ...$record->tags->map(fn ($tag): array => ['label' => $tag->name, 'color' => 'info'])->all(),
            ])->filter()->values()->all())
            ->recordUrl(fn (Lead $record): string => LeadResource::getUrl('view', ['record' => $record]))
            ->cardFooterActions([
                Action::make('open')
                    ->label(__('crm::filament/clusters/leads/resources/lead.actions.open.label'))
                    ->icon('heroicon-o-arrow-top-right-on-square')
                    ->color('gray')
                    ->url(fn (Lead $record): string => LeadResource::getUrl('view', ['record' => $record])),
            ])
            ->searchable(['name', 'contact_name', 'company_name', 'email'])
            ->filters([
                Select::make('user_id')
                    ->label(__('crm::filament/clusters/leads/resources/lead.table.columns.salesperson'))
                    ->relationship('user', 'name')
                    ->searchable()
                    ->preload()
                    ->placeholder(__('crm::filament/clusters/leads/resources/lead/pages/kanban-leads.filters.all')),
                Select::make('team_id')
                    ->label(__('crm::filament/clusters/leads/resources/lead.table.columns.team'))
                    ->relationship('team', 'name', fn (Builder $query): Builder => $query->where(owned_by_company(current_company_id())))
                    ->searchable()
                    ->preload()
                    ->placeholder(__('crm::filament/clusters/leads/resources/lead/pages/kanban-leads.filters.all')),
                Select::make('priority')
                    ->label(__('crm::filament/clusters/leads/resources/lead.table.columns.priority'))
                    ->options(OpportunityPriority::class)
                    ->placeholder(__('crm::filament/clusters/leads/resources/lead/pages/kanban-leads.filters.all')),
            ])
            ->filtersColumns(1)
            ->columnHeaderAction(
                Action::make('quickCreate')
                    ->icon('heroicon-o-plus')
                    ->modalHeading(__('crm::filament/clusters/leads/resources/lead/pages/kanban-leads.quick-create.heading'))
                    ->modalWidth('lg')
                    ->visible(fn (): bool => LeadResource::canCreate())
                    ->schema([
                        Hidden::make('stage_id'),
                        TextInput::make('name')
                            ->label(__('crm::filament/clusters/leads/resources/lead.table.columns.name'))
                            ->placeholder(__('crm::filament/clusters/leads/resources/lead.form.fields.name-placeholder'))
                            ->required()
                            ->maxLength(255),
                        TextInput::make('contact_name')
                            ->label(__('crm::filament/clusters/leads/resources/lead.form.fields.contact-name'))
                            ->maxLength(255),
                        TextInput::make('company_name')
                            ->label(__('crm::filament/clusters/leads/resources/lead.form.fields.company-name'))
                            ->maxLength(255),
                        TextInput::make('email')
                            ->label(__('crm::filament/clusters/leads/resources/lead.form.fields.email'))
                            ->email()
                            ->maxLength(255),
                        TextInput::make('phone')
                            ->label(__('crm::filament/clusters/leads/resources/lead.form.fields.phone'))
                            ->tel()
                            ->maxLength(255),
                    ])
                    ->action(function (array $data): void {
                        Lead::create([
                            ...$data,
                            'stage_id'   => $this->validatedQuickCreateStageId($data['stage_id'] ?? null),
                            'type'       => Lead::TYPE_LEAD,
                            'team_id'    => $this->validatedQuickCreateTeamId(),
                            'company_id' => current_company_id(),
                        ]);

                        Notification::make()
                            ->success()
                            ->title(__('crm::filament/clusters/leads/resources/lead/pages/create-lead.notification.title'))
                            ->body(__('crm::filament/clusters/leads/resources/lead/pages/create-lead.notification.body'))
                            ->send();
                    })
            )
            ->emptyState(
                __('crm::filament/clusters/leads/resources/lead/pages/kanban-leads.empty-state.heading'),
                __('crm::filament/clusters/leads/resources/lead/pages/kanban-leads.empty-state.description'),
                'heroicon-o-inbox-arrow-down',
            )
            ->columnWidth('300px')
            ->boardView('crm::filament.kanban.board')
            ->columnView('crm::filament.kanban.column')
            ->cardView('crm::filament.kanban.card');
    }

    protected function validatedQuickCreateTeamId(): ?int
    {
        $teamId = $this->kanbanFilters['team_id'] ?? null;

        if (blank($teamId)) {
            return null;
        }

        return Team::query()->where(owned_by_company(current_company_id()))->whereKey($teamId)->value('id');
    }

    protected function validatedQuickCreateStageId(mixed $stageId): ?int
    {
        return Stage::query()->where('is_won', false)->whereKey($stageId)->value('id')
            ?? Stage::query()->where('is_won', false)->orderBy('sort')->value('id');
    }
}
