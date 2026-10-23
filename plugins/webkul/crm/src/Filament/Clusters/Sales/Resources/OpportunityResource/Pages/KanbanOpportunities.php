<?php

namespace Webkul\Crm\Filament\Clusters\Sales\Resources\OpportunityResource\Pages;

use Filament\Actions\Action;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ListRecords;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Number;
use Webkul\Crm\Enums\OpportunityPriority;
use Webkul\Crm\Filament\Actions\CrmExportAction;
use Webkul\Crm\Filament\Actions\CrmImportAction;
use Webkul\Crm\Filament\Clusters\Configurations\Resources\RecurringPlanResource;
use Webkul\Crm\Filament\Clusters\Sales\Resources\OpportunityResource;
use Webkul\Crm\Filament\Concerns\AppliesKanbanListFilters;
use Webkul\Crm\Filament\Concerns\ManagesStageKanbanColumns;
use Webkul\Crm\Filament\Exports\OpportunityExporter;
use Webkul\Crm\Filament\Imports\OpportunityImporter;
use Webkul\Crm\Models\Lead;
use Webkul\Crm\Models\Stage;
use Webkul\Crm\Models\Tag;
use Webkul\Sale\Models\Team;
use Webkul\Support\Models\Company;
use Wezlo\FilamentKanban\Concerns\HasKanbanBoard;
use Wezlo\FilamentKanban\KanbanBoard;
use Wezlo\FilamentKanban\KanbanColumn;

class KanbanOpportunities extends ListRecords
{
    use AppliesKanbanListFilters, HasKanbanBoard, ManagesStageKanbanColumns {
        AppliesKanbanListFilters::getKanbanQuery insteadof HasKanbanBoard;
        HasKanbanBoard::getKanbanQuery as getBaseKanbanQuery;
        HasKanbanBoard::moveRecord as moveBaseRecord;
    }

    protected static string $resource = OpportunityResource::class;

    public function mount(): void
    {
        parent::mount();

        $teamId = request()->integer('team');

        $this->kanbanFilters = [
            ...static::emptyKanbanFilters(),
            'user_id' => $teamId ? null : Auth::id(),
            'team_id' => $teamId ?: null,
        ];
    }

    public function resetKanbanFilters(): void
    {
        $this->kanbanFilters = static::emptyKanbanFilters();
    }

    public static function emptyKanbanFilters(): array
    {
        return [
            'user_id'  => null,
            'team_id'  => null,
            'priority' => null,
            'status'   => null,
            'stage_id' => [],
            'tags'     => [],
        ];
    }

    protected function kanbanSimpleFilterKeys(): array
    {
        return ['user_id', 'team_id', 'priority'];
    }

    protected function applyKanbanListFilters(Builder $query, array $filters): Builder
    {
        $status = $filters['status'] ?? null;
        $stageIds = static::kanbanFilterValues($filters, 'stage_id');
        $tagIds = static::kanbanFilterValues($filters, 'tags');

        return $query
            ->when($status === 'lost', fn (Builder $query): Builder => $query->onlyTrashed(), fn (Builder $query): Builder => $query->withoutTrashed())
            ->when($status === 'open', fn (Builder $query): Builder => $query->open())
            ->when($status === 'won', fn (Builder $query): Builder => $query->won())
            ->when($status === 'unassigned', fn (Builder $query): Builder => $query->whereNull('crm_leads.user_id'))
            ->when($stageIds, fn (Builder $query): Builder => $query->whereIn('crm_leads.stage_id', $stageIds))
            ->when($tagIds, fn (Builder $query): Builder => $query->whereHas('tags', fn (Builder $query): Builder => $query->whereIn('crm_tags.id', $tagIds)));
    }

    public function getTitle(): string
    {
        return __('crm::filament/clusters/sales/resources/opportunity/pages/list-opportunities.title');
    }

    public function getBreadcrumb(): string
    {
        return __('crm::filament/clusters/sales/resources/opportunity/pages/kanban-opportunities.breadcrumb');
    }

    protected function getHeaderActions(): array
    {
        return [
            Action::make('listView')
                ->label(__('crm::filament/clusters/sales/resources/opportunity/pages/kanban-opportunities.header-actions.list.label'))
                ->icon('heroicon-o-list-bullet')
                ->color('gray')
                ->url(fn (): string => OpportunityResource::getUrl('list')),
            CrmImportAction::make()
                ->importer(OpportunityImporter::class)
                ->visible(fn (): bool => OpportunityResource::canCreate()),
            CrmExportAction::make()
                ->exporter(OpportunityExporter::class)
                ->modifyQueryUsing(fn (Builder $query): Builder => CrmExportAction::limitToVisibleRecords(
                    $query->whereIn('crm_leads.id', $this->getKanbanQuery($this->getKanbanBoard())->reorder()->pluck('crm_leads.id')),
                )),
            Action::make('create')
                ->label(__('crm::filament/clusters/sales/resources/opportunity/pages/list-opportunities.header-actions.create.label'))
                ->icon('heroicon-o-plus-circle')
                ->url(fn (): string => OpportunityResource::getUrl('create'))
                ->visible(fn (): bool => OpportunityResource::canCreate()),
        ];
    }

    public function kanban(KanbanBoard $kanban): KanbanBoard
    {
        $wonStageIds = Stage::query()->where('is_won', true)->pluck('id')->map(fn ($id): string => (string) $id)->all();

        $currencyCode = Company::query()->find(current_company_id())?->currency?->code ?? 'USD';

        return $kanban
            ->relationshipColumn('stage', 'name', Stage::class, orderAttribute: 'sort')
            ->excludeColumns(Stage::hiddenColumnsForTeam($this->kanbanFilters['team_id'] ?? null))
            ->modifyQueryUsing(fn (Builder $query): Builder => $query->with(['partner', 'user', 'tags', 'company.currency', 'recurringPlan']))
            ->columnColor(fn (KanbanColumn $column): string => in_array($column->value, $wonStageIds, true) ? 'success' : 'gray')
            ->cardTitle(fn (Lead $record): string => $record->name)
            ->cardDescription(fn (Lead $record): string => collect([
                $record->partner?->name,
                (float) $record->expected_revenue > 0
                    ? Number::currency((float) $record->expected_revenue, $record->company?->currency?->code ?? $currencyCode)
                    : null,
                RecurringPlanResource::isRecurringRevenueEnabled() && (float) $record->recurring_revenue > 0
                    ? '+ '.Number::currency((float) $record->recurring_revenue, $record->company?->currency?->code ?? $currencyCode).($record->recurringPlan ? ' '.$record->recurringPlan->name : '')
                    : null,
            ])->filter()->implode(' · '))
            ->cardBadges(fn (Lead $record): array => collect([
                $record->priority && $record->priority !== OpportunityPriority::Low
                    ? ['label' => $record->priority->getLabel(), 'color' => $record->priority->getColor()]
                    : null,
                $record->user ? ['label' => $record->user->name, 'color' => 'gray'] : null,
                ...$record->tags->map(fn ($tag): array => ['label' => $tag->name, 'color' => 'info'])->all(),
            ])->filter()->values()->all())
            ->recordUrl(fn (Lead $record): string => OpportunityResource::getUrl('view', ['record' => $record]))
            ->columnSummary(function (Collection $records) use ($currencyCode): ?string {
                $total = (float) $records->sum('expected_revenue');

                return $total > 0 ? Number::currency($total, $currencyCode) : null;
            })
            ->searchable(['name', 'email', 'partner.name'])
            ->filters([
                Select::make('user_id')
                    ->label(__('crm::filament/clusters/sales/resources/opportunity/pages/kanban-opportunities.filters.salesperson'))
                    ->relationship('user', 'name')
                    ->searchable()
                    ->preload()
                    ->placeholder(__('crm::filament/clusters/sales/resources/opportunity/pages/kanban-opportunities.filters.all')),
                Select::make('team_id')
                    ->label(__('crm::filament/clusters/sales/resources/opportunity/pages/kanban-opportunities.filters.team'))
                    ->relationship('team', 'name', fn (Builder $query): Builder => $query->where(owned_by_company(current_company_id())))
                    ->searchable()
                    ->preload()
                    ->placeholder(__('crm::filament/clusters/sales/resources/opportunity/pages/kanban-opportunities.filters.all')),
                Select::make('priority')
                    ->label(__('crm::filament/clusters/sales/resources/opportunity/pages/kanban-opportunities.filters.priority'))
                    ->options(OpportunityPriority::class)
                    ->placeholder(__('crm::filament/clusters/sales/resources/opportunity/pages/kanban-opportunities.filters.all')),
                Select::make('status')
                    ->label(__('crm::filament/clusters/sales/resources/opportunity/pages/kanban-opportunities.filters.status'))
                    ->options([
                        'open'       => __('crm::filament/clusters/sales/resources/opportunity/pages/list-opportunities.tabs.open'),
                        'unassigned' => __('crm::filament/clusters/sales/resources/opportunity/pages/list-opportunities.tabs.unassigned'),
                        'won'        => __('crm::filament/clusters/sales/resources/opportunity/pages/list-opportunities.tabs.won'),
                        'lost'       => __('crm::filament/clusters/sales/resources/opportunity/pages/list-opportunities.tabs.lost'),
                    ])
                    ->placeholder(__('crm::filament/clusters/sales/resources/opportunity/pages/kanban-opportunities.filters.all')),
                Select::make('stage_id')
                    ->label(__('crm::filament/clusters/sales/resources/opportunity.table.filters.stage'))
                    ->options(fn (): array => Stage::query()->orderBy('sort')->pluck('name', 'id')->all())
                    ->multiple()
                    ->preload()
                    ->placeholder(__('crm::filament/clusters/sales/resources/opportunity/pages/kanban-opportunities.filters.all')),
                Select::make('tags')
                    ->label(__('crm::filament/clusters/sales/resources/opportunity.table.filters.tags'))
                    ->options(fn (): array => Tag::query()->orderBy('name')->pluck('name', 'id')->all())
                    ->multiple()
                    ->searchable()
                    ->placeholder(__('crm::filament/clusters/sales/resources/opportunity/pages/kanban-opportunities.filters.all')),
            ])
            ->filtersColumns(1)
            ->columnHeaderAction(
                Action::make('quickCreate')
                    ->icon('heroicon-o-plus')
                    ->modalHeading(__('crm::filament/clusters/sales/resources/opportunity/pages/kanban-opportunities.quick-create.heading'))
                    ->modalWidth('lg')
                    ->visible(fn (): bool => OpportunityResource::canCreate())
                    ->schema([
                        Hidden::make('stage_id'),
                        TextInput::make('name')
                            ->label(__('crm::filament/clusters/sales/resources/opportunity.form.sections.general.fields.name'))
                            ->placeholder(__('crm::filament/clusters/sales/resources/opportunity.form.sections.general.fields.name-placeholder'))
                            ->required()
                            ->maxLength(255),
                        Select::make('partner_id')
                            ->label(__('crm::filament/clusters/sales/resources/opportunity.form.sections.general.fields.customer'))
                            ->relationship('partner', 'name')
                            ->searchable()
                            ->preload(),
                        TextInput::make('expected_revenue')
                            ->label(__('crm::filament/clusters/sales/resources/opportunity.form.sections.general.fields.expected-revenue'))
                            ->numeric()
                            ->minValue(0)
                            ->default(0),
                        Select::make('priority')
                            ->label(__('crm::filament/clusters/sales/resources/opportunity.form.sections.sales.fields.priority'))
                            ->options(OpportunityPriority::class)
                            ->default(OpportunityPriority::Low),
                    ])
                    ->action(function (array $data): void {
                        Lead::create([
                            ...$data,
                            'stage_id'   => $this->validatedQuickCreateStageId($data['stage_id'] ?? null),
                            'team_id'    => $this->validatedQuickCreateTeamId(),
                            'company_id' => current_company_id(),
                        ]);

                        Notification::make()
                            ->success()
                            ->title(__('crm::filament/clusters/sales/resources/opportunity/pages/create-opportunity.notification.title'))
                            ->body(__('crm::filament/clusters/sales/resources/opportunity/pages/create-opportunity.notification.body'))
                            ->send();
                    })
            )
            ->emptyState(
                __('crm::filament/clusters/sales/resources/opportunity/pages/kanban-opportunities.empty-state.heading'),
                __('crm::filament/clusters/sales/resources/opportunity/pages/kanban-opportunities.empty-state.description'),
                'heroicon-o-funnel',
            )
            ->columnWidth('300px')
            ->boardView('crm::filament.kanban.board')
            ->columnView('crm::filament.kanban.column')
            ->cardView('crm::filament.kanban.card');
    }

    public function moveRecord(string $recordId, string $newColumnValue, array $orderedIds): void
    {
        if (($this->kanbanFilters['status'] ?? null) === 'lost') {
            Notification::make()
                ->warning()
                ->title(__('crm::filament/clusters/sales/resources/opportunity/pages/kanban-opportunities.lost-move'))
                ->send();

            return;
        }

        $this->moveBaseRecord($recordId, $newColumnValue, $orderedIds);
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
        return Stage::query()->whereKey($stageId)->value('id')
            ?? Stage::query()->orderBy('sort')->value('id');
    }
}
