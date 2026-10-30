<?php

namespace Webkul\Crm\Filament\Clusters\Sales\Resources\CustomerResource\Pages;

use Filament\Notifications\Notification;
use Filament\Schemas\Schema;
use Filament\Tables\Table;
use Illuminate\Contracts\Pagination\CursorPaginator;
use Illuminate\Contracts\Pagination\Paginator;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Webkul\Contact\Filament\Resources\PartnerResource\Pages\ListPartners as BaseListPartners;
use Webkul\Crm\Enums\CustomerBoardGroup;
use Webkul\Crm\Enums\ViewLayout;
use Webkul\Crm\Filament\Clusters\Sales\Resources\CustomerResource;
use Webkul\Crm\Filament\Clusters\Sales\Resources\CustomerResource\Tables\CustomersTable;
use Webkul\Crm\Filament\Concerns\HasCRMTableViews;
use Webkul\Crm\Filament\Concerns\HasGroupBoardLayout;
use Webkul\Crm\Filament\Concerns\HasViewLayouts;
use Webkul\Crm\Filament\Support\Reports\Dimension;
use Webkul\Partner\Enums\AccountType;
use Webkul\Partner\Models\Partner;
use Wezlo\FilamentKanban\Concerns\HasKanbanBoard;
use Wezlo\FilamentKanban\KanbanBoard;
use Wezlo\FilamentKanban\KanbanColumn;

class ListCustomers extends BaseListPartners
{
    use HasCRMTableViews, HasGroupBoardLayout, HasKanbanBoard, HasViewLayouts;

    protected static string $resource = CustomerResource::class;

    public function content(Schema $schema): Schema
    {
        return parent::content($schema);
    }

    public function getViewLayouts(): array
    {
        return [ViewLayout::LIST, ViewLayout::KANBAN];
    }

    public function getBreadcrumb(): string
    {
        return $this->getViewLayout()->getLabel();
    }

    public function getBoardGroup(): ?CustomerBoardGroup
    {
        return $this->isViewLayout(ViewLayout::KANBAN) ? CustomerBoardGroup::fromGrouping($this->tableGrouping) : null;
    }

    public function table(Table $table): Table
    {
        return $this->configureGroupBoardTable(CustomerResource::table($table))
            ->emptyState(fn () => $this->getBoardGroup() ? view($this->getKanbanBoard()->getBoardView()) : null);
    }

    public function getTableRecords(): Collection|Paginator|CursorPaginator
    {
        return $this->isViewLayout(ViewLayout::LIST) ? parent::getTableRecords() : new Collection;
    }

    public function getPresetTableViews(): array
    {
        return parent::getPresetTableViews();
    }

    public function kanban(KanbanBoard $kanban): KanbanBoard
    {
        return $kanban
            ->recordUrl(fn (Partner $record): string => CustomerResource::getUrl('view', ['record' => $record]))
            ->canMove(fn (): bool => (bool) $this->getBoardGroup()?->isMovable())
            ->columnWidth('300px')
            ->boardView('crm::filament.kanban.board')
            ->columnView('crm::filament.kanban.column')
            ->cardView('crm::filament.kanban.customer-card');
    }

    protected function getKanbanQuery(KanbanBoard $board): Builder
    {
        return $this->filterTableQuery($this->modifyQueryWithActiveTab(CustomerResource::getEloquentQuery()))
            ->where('account_type', '!=', AccountType::ADDRESS)
            ->with(['parent', 'country'])
            ->withCount(['crmOpportunities', 'crmSaleOrders']);
    }

    public function getKanbanColumns(): array
    {
        $group = $this->getBoardGroup();

        if (! $group) {
            return [];
        }

        $records = $this->getKanbanQuery($this->getKanbanBoard())
            ->with($group->relation() ? [$group->relation()] : [])
            ->reorder()
            ->orderBy('name')
            ->get();

        $groups = $records->groupBy(fn (Partner $record): string => $this->boardValue($record, $group));

        $labels = $groups
            ->map(fn (Collection $records, string $value): string => $value === 'none' ? Dimension::getNoneLabel() : CustomersTable::groupTitle($records->first(), $group))
            ->sort();

        $labels = $labels->has('none') ? collect(['none' => $labels->pull('none')])->union($labels) : $labels;

        return $labels
            ->map(function (string $label, string|int $value) use ($groups): KanbanColumn {
                $column = new KanbanColumn(value: (string) $value, label: $label, color: 'gray');

                $column->records = $groups->get((string) $value, new Collection)->values();
                $column->count = $column->records->count();

                return $column;
            })
            ->values()
            ->all();
    }

    protected function boardValue(Partner $record, CustomerBoardGroup $group): string
    {
        $value = $record->getAttribute($group->field());

        if ($value instanceof \BackedEnum) {
            return (string) $value->value;
        }

        return filled($value) ? (string) $value : 'none';
    }

    public function moveRecord(string $recordId, string $newColumnValue, array $orderedIds): void
    {
        $group = $this->getBoardGroup();

        $record = $group?->isMovable() ? CustomerResource::getEloquentQuery()->find($recordId) : null;

        $allowed = collect($this->getKanbanColumns())->pluck('value')->all();

        if (! $record
            || $record->trashed()
            || ! CustomerResource::canEdit($record)
            || ! in_array($newColumnValue, $allowed, true)) {
            Notification::make()
                ->danger()
                ->title(__('crm::filament/clusters/sales/resources/customer.board.unauthorized'))
                ->send();

            return;
        }

        $record->{$group->field()} = $newColumnValue === 'none' ? null : (int) $newColumnValue;
        $record->save();
    }
}
