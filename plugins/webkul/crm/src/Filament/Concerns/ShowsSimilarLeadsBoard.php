<?php

namespace Webkul\Crm\Filament\Concerns;

use Filament\Actions\Action;
use Filament\Resources\Pages\Concerns\InteractsWithRecord;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection as EloquentCollection;
use Illuminate\Support\Collection;
use Illuminate\Support\Number;
use Webkul\Crm\Filament\Actions\MergeLeadsAction;
use Webkul\Crm\Filament\Support\LeadResourceResolver;
use Webkul\Crm\Filament\Support\Reports\Report;
use Webkul\Crm\Filament\Support\SimilarLeads;
use Webkul\Crm\Models\Lead;
use Webkul\Crm\Models\Stage;
use Wezlo\FilamentKanban\Concerns\HasKanbanBoard;
use Wezlo\FilamentKanban\KanbanBoard;
use Wezlo\FilamentKanban\KanbanColumn;

trait ShowsSimilarLeadsBoard
{
    use HasKanbanBoard, InteractsWithRecord {
        HasKanbanBoard::getKanbanColumns as getAllKanbanColumns;
    }

    public function mount(int|string $record): void
    {
        $this->record = $this->resolveRecord($record);

        abort_unless(static::getResource()::canView($this->getRecord()), 403);

        $this->kanbanFilters = [];
    }

    public function getTitle(): string|Htmlable
    {
        return __('crm::filament/similar-leads.page.title', ['name' => $this->getRecord()->name]);
    }

    public function getBreadcrumb(): string
    {
        return __('crm::filament/similar-leads.navigation.label');
    }

    public function getSimilarGroup(): Collection
    {
        /** @var Lead $record */
        $record = $this->getRecord();

        $record->loadMissing(['stage', 'user', 'partner', 'tags', 'company.currency']);

        return new EloquentCollection([$record, ...SimilarLeads::visibleRecords($record)]);
    }

    protected function getHeaderActions(): array
    {
        return [
            Action::make('backToRecord')
                ->label(__('crm::filament/similar-leads.page.back'))
                ->icon('heroicon-o-arrow-left')
                ->color('gray')
                ->url(fn (): string => static::getResource()::getUrl('view', ['record' => $this->getRecord()])),
            MergeLeadsAction::make()
                ->candidates(fn (): Collection => $this->getSimilarGroup()),
        ];
    }

    public function getSubNavigation(): array
    {
        return [];
    }

    protected function getKanbanQuery(KanbanBoard $board): Builder
    {
        $query = Lead::query()
            ->withTrashed()
            ->with(['stage', 'user', 'partner', 'tags', 'company.currency'])
            ->whereKey($this->getSimilarGroup()->modelKeys());

        if (filled($search = $this->kanbanSearch)) {
            $query->where(fn (Builder $query): Builder => $query
                ->where('name', 'like', "%{$search}%")
                ->orWhere('email', 'like', "%{$search}%")
                ->orWhere('contact_name', 'like', "%{$search}%"));
        }

        return $query;
    }

    public function getKanbanColumns(): array
    {
        return array_values(array_filter($this->getAllKanbanColumns(), fn (KanbanColumn $column): bool => $column->count > 0));
    }

    public function moveRecord(string $recordId, string $newColumnValue, array $orderedIds): void {}

    public function kanban(KanbanBoard $kanban): KanbanBoard
    {
        $currentId = $this->getRecord()->getKey();

        $currency = Report::getCurrencyCode();

        return $kanban
            ->relationshipColumn('stage', 'name', Stage::class, orderAttribute: 'sort')
            ->canMove(fn (): bool => false)
            ->cardTitle(fn (Lead $lead): string => $lead->name)
            ->cardDescription(fn (Lead $lead): string => collect([
                $lead->partner?->name ?? $lead->contact_name,
                $lead->email,
                $lead->phone,
                (float) $lead->expected_revenue > 0 ? Number::currency((float) $lead->expected_revenue, $currency) : null,
            ])->filter()->implode(' · '))
            ->cardBadges(fn (Lead $lead): array => collect([
                $lead->getKey() === $currentId ? ['label' => __('crm::filament/similar-leads.badges.current'), 'color' => 'primary'] : null,
                ['label' => $lead->isLead() ? __('crm::models/lead.title') : __('crm::models/opportunity.title'), 'color' => $lead->isLead() ? 'gray' : 'info'],
                $lead->trashed() ? ['label' => __('crm::filament/similar-leads.badges.lost'), 'color' => 'danger'] : null,
                $lead->user ? ['label' => $lead->user->name, 'color' => 'gray'] : null,
            ])->filter()->values()->all())
            ->recordUrl(fn (Lead $lead): ?string => LeadResourceResolver::viewUrl($lead))
            ->searchable(['name', 'email', 'contact_name'])
            ->emptyState(
                __('crm::filament/similar-leads.page.empty.heading'),
                __('crm::filament/similar-leads.page.empty.description'),
                'heroicon-o-user-group',
            )
            ->columnWidth('300px')
            ->boardView('crm::filament.kanban.board')
            ->columnView('crm::filament.kanban.column')
            ->cardView('crm::filament.kanban.card');
    }
}
