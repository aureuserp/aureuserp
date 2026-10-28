<?php

namespace Webkul\Crm\Filament\Clusters\Sales\Resources\ContactResource\Pages;

use Filament\Navigation\NavigationItem;
use Filament\Notifications\Notification;
use Filament\Schemas\Schema;
use Filament\Tables\Table;
use Illuminate\Contracts\Pagination\CursorPaginator;
use Illuminate\Contracts\Pagination\Paginator;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Livewire\Attributes\Url;
use Webkul\Contact\Filament\Resources\PartnerResource\Pages\ListPartners as BaseListPartners;
use Webkul\Crm\Enums\ContactBoardGroup;
use Webkul\Crm\Enums\GridLayout;
use Webkul\Crm\Filament\Clusters\Sales\Resources\ContactResource;
use Webkul\Crm\Filament\Clusters\Sales\Resources\ContactResource\Tables\ContactsTable;
use Webkul\Crm\Filament\Concerns\HasCRMTableViews;
use Webkul\Crm\Filament\Support\Reports\Dimension;
use Webkul\Partner\Enums\AccountType;
use Webkul\Partner\Models\Partner;
use Wezlo\FilamentKanban\Concerns\HasKanbanBoard;
use Wezlo\FilamentKanban\KanbanBoard;
use Wezlo\FilamentKanban\KanbanColumn;

class ListContacts extends BaseListPartners
{
    use HasCRMTableViews, HasKanbanBoard;

    protected static string $resource = ContactResource::class;

    #[Url(as: 'layout')]
    public string $contactLayout = GridLayout::GRID->value;

    public function mount(): void
    {
        $this->contactLayout = $this->getContactLayout()->value;

        parent::mount();
    }

    public function content(Schema $schema): Schema
    {
        return parent::content($schema);
    }

    public function getContactLayout(): GridLayout
    {
        return GridLayout::tryFrom($this->contactLayout) ?? GridLayout::GRID;
    }

    public function getBreadcrumb(): string
    {
        return $this->getContactLayout()->getLabel();
    }

    public function getRecordNavigationTabs(): array
    {
        return array_map(
            fn (GridLayout $layout): NavigationItem => NavigationItem::make($layout->value)
                ->label($layout->getLabel())
                ->icon($layout->getIcon())
                ->url(ContactResource::getUrl('index', $layout === GridLayout::GRID ? [] : ['layout' => $layout->value]))
                ->isActiveWhen(fn (): bool => $this->getContactLayout() === $layout),
            GridLayout::cases(),
        );
    }

    public function getBoardGroup(): ?ContactBoardGroup
    {
        return ContactBoardGroup::fromGrouping($this->tableGrouping);
    }

    public function table(Table $table): Table
    {
        return ContactsTable::configure(ContactResource::table($table), grid: $this->getContactLayout() === GridLayout::GRID)
            ->paginated(fn (): bool => ! $this->getBoardGroup())
            ->emptyState(fn () => $this->getBoardGroup() ? view($this->getKanbanBoard()->getBoardView()) : null);
    }

    public function getTableRecords(): Collection|Paginator|CursorPaginator
    {
        return $this->getBoardGroup() ? new Collection : parent::getTableRecords();
    }

    public function getPresetTableViews(): array
    {
        return parent::getPresetTableViews();
    }

    public function kanban(KanbanBoard $kanban): KanbanBoard
    {
        return $kanban
            ->recordUrl(fn (Partner $record): string => ContactResource::getUrl('view', ['record' => $record]))
            ->canMove(fn (): bool => (bool) $this->getBoardGroup()?->isMovable())
            ->columnWidth('300px')
            ->boardView('crm::filament.kanban.board')
            ->columnView('crm::filament.kanban.column')
            ->cardView('crm::filament.kanban.contact-card');
    }

    protected function getKanbanQuery(KanbanBoard $board): Builder
    {
        return $this->filterTableQuery($this->modifyQueryWithActiveTab(ContactResource::getEloquentQuery()))
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
            ->map(fn (Collection $records, string $value): string => $value === 'none' ? Dimension::getNoneLabel() : ContactsTable::groupTitle($records->first(), $group))
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

    protected function boardValue(Partner $record, ContactBoardGroup $group): string
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

        $record = $group?->isMovable() ? ContactResource::getEloquentQuery()->find($recordId) : null;

        $allowed = collect($this->getKanbanColumns())->pluck('value')->all();

        if (! $record
            || $record->trashed()
            || ! ContactResource::canEdit($record)
            || ! in_array($newColumnValue, $allowed, true)) {
            Notification::make()
                ->danger()
                ->title(__('crm::filament/clusters/sales/resources/contact.board.unauthorized'))
                ->send();

            return;
        }

        $record->{$group->field()} = $newColumnValue === 'none' ? null : (int) $newColumnValue;
        $record->save();
    }
}
