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
use Webkul\Crm\Filament\Clusters\Sales\Resources\OpportunityResource;
use Webkul\Crm\Models\Lead;
use Webkul\Crm\Models\Stage;
use Webkul\Sale\Models\Team;
use Webkul\Support\Models\Company;
use Wezlo\FilamentKanban\Concerns\HasKanbanBoard;
use Wezlo\FilamentKanban\KanbanBoard;
use Wezlo\FilamentKanban\KanbanColumn;

class KanbanOpportunities extends ListRecords
{
    use HasKanbanBoard;

    protected static string $resource = OpportunityResource::class;

    /**
     * Opens on the current user's pipeline, or on a team's pipeline when `?team=` is given.
     */
    public function mount(): void
    {
        parent::mount();

        $teamId = request()->integer('team');

        $this->kanbanFilters = [
            'user_id'  => $teamId ? null : Auth::id(),
            'team_id'  => $teamId ?: null,
            'priority' => null,
        ];
    }

    /**
     * Clears every kanban filter while keeping the keys the filter fields are bound to.
     */
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
            ->modifyQueryUsing(fn (Builder $query): Builder => $query->withoutTrashed()->with(['partner', 'user', 'tags', 'company.currency']))
            ->columnColor(fn (KanbanColumn $column): string => in_array($column->value, $wonStageIds, true) ? 'success' : 'gray')
            ->cardTitle(fn (Lead $record): string => $record->name)
            ->cardDescription(fn (Lead $record): string => collect([
                $record->partner?->name,
                (float) $record->expected_revenue > 0
                    ? Number::currency((float) $record->expected_revenue, $record->company?->currency?->code ?? $currencyCode)
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
            ->cardView('crm::filament.kanban.card');
    }

    /**
     * The team taken from the (client-side) kanban filter, accepted only when it belongs to the current company.
     */
    protected function validatedQuickCreateTeamId(): ?int
    {
        $teamId = $this->kanbanFilters['team_id'] ?? null;

        if (blank($teamId)) {
            return null;
        }

        return Team::query()->where(owned_by_company(current_company_id()))->whereKey($teamId)->value('id');
    }

    /**
     * The column's stage, accepted only when it is an existing stage.
     */
    protected function validatedQuickCreateStageId(mixed $stageId): ?int
    {
        return Stage::query()->whereKey($stageId)->value('id')
            ?? Stage::query()->orderBy('sort')->value('id');
    }
}
