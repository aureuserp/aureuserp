<?php

namespace Webkul\Support\Filament\Infolists\Components;

use Closure;
use Filament\Actions\Action;
use Filament\Forms\Components\Concerns\HasExtraItemActions;
use Filament\Infolists\Components\RepeatableEntry as BaseRepeatableEntry;
use Filament\Schemas\Components\Component;
use Filament\Support\Enums\Size;
use Filament\Tables\Table\Concerns\HasColumnManager;
use Webkul\Support\Filament\Infolists\Components\Repeater\TableColumn;

class RepeatableEntry extends BaseRepeatableEntry
{
    use HasColumnManager;
    use HasExtraItemActions;

    protected ?string $columnManagerSessionKey = null;

    protected bool|Closure|null $isRepeaterHasTableView = false;

    public function table(array|Closure|null $columns): static
    {
        $this->isRepeaterHasTableView = true;

        $this->tableColumns = $columns;

        return $this;
    }

    public function hasTableView(): bool
    {
        return $this->evaluate($this->isRepeaterHasTableView) || filled($this->getTableColumns());
    }

    public function getColumnManagerSessionKey(): string
    {
        return $this->columnManagerSessionKey ??= 'repeatable_entry_'.$this->getStatePath().'_column_manager';
    }

    public function getMappedColumns(): array
    {
        $columns = $this->evaluate($this->tableColumns);

        if (! is_array($columns)) {
            $columns = [];
        }

        $savedState = session($this->getColumnManagerSessionKey(), []);

        return collect($columns)->map(
            function (TableColumn $column) use ($savedState): array {
                $columnName = $column->getName();

                $isToggled = data_get($savedState, "{$columnName}.isToggled", ! $column->isToggledHiddenByDefault());

                return [
                    'type'                     => 'column',
                    'name'                     => $columnName,
                    'label'                    => $column->getLabel(),
                    'isHidden'                 => $column->isHidden(),
                    'isToggled'                => $isToggled,
                    'isToggleable'             => $column->isToggleable(),
                    'isToggledHiddenByDefault' => $column->isToggledHiddenByDefault(),
                ];
            }
        )->toArray();
    }

    public function getColumnManagerTriggerAction(): Action
    {
        $action = Action::make('openColumnManager')
            ->label(__('filament-tables::table.actions.column_manager.label'))
            ->iconButton()
            ->icon('heroicon-s-view-columns')
            ->color('gray')
            ->livewireClickHandlerEnabled(false)
            ->authorize(true);

        if ($this->modifyColumnManagerTriggerActionUsing) {
            $action = $this->evaluate($this->modifyColumnManagerTriggerActionUsing, [
                'action' => $action,
            ]) ?? $action;
        }

        if ($action->getView() === Action::BUTTON_VIEW) {
            $action->defaultSize(Size::Small->value);
        }

        return $action;
    }

    public function getTableColumns(): array
    {
        $columns = $this->evaluate($this->tableColumns);

        if (! is_array($columns)) {
            $columns = [];
        }

        $savedState = session($this->getColumnManagerSessionKey(), []);

        $visibleColumns = collect($columns)->filter(
            function (TableColumn $column) use ($savedState): bool {
                if ($column->isHidden()) {
                    return false;
                }

                $columnName = $column->getName();

                if (data_get($savedState, $columnName)) {
                    return data_get($savedState, "{$columnName}.isToggled", false);
                }

                return ! $column->isToggledHiddenByDefault();
            }
        );

        return $visibleColumns->values()->toArray();
    }

    public function hasToggleableColumns(): bool
    {
        $columns = $this->evaluate($this->tableColumns) ?? [];

        return collect($columns)->contains(fn ($column) => $column->isToggleable());
    }

    public function hasColumnManager(): bool
    {
        return $this->hasToggleableColumns();
    }

    public function getColumnManagerApplyAction(): Action
    {
        $action = Action::make('applyTableColumnManager')
            ->label(__('filament-tables::table.column_manager.actions.apply.label'))
            ->button()
            ->visible($this->hasDeferredColumnManager())
            ->alpineClickHandler('applyTableColumnManager')
            ->authorize(true);

        if ($this->modifyColumnManagerApplyActionUsing) {
            $action = $this->evaluate($this->modifyColumnManagerApplyActionUsing, [
                'action' => $action,
            ]) ?? $action;
        }

        return $action;
    }

    public function applyTableColumnManager(?array $columns = null): void
    {
        if (blank($columns)) {
            return;
        }

        $columnState = collect($columns)
            ->filter(fn ($column) => filled(data_get($column, 'name')) && ! is_null(data_get($column, 'isToggled')))
            ->mapWithKeys(fn ($column) => [
                data_get($column, 'name') => [
                    'isToggled'    => data_get($column, 'isToggled'),
                    'isToggleable' => data_get($column, 'isToggleable', true),
                ],
            ])
            ->toArray();

        session([$this->getColumnManagerSessionKey() => $columnState]);
    }

    public function resetTableColumnManager(): void
    {
        session()->forget($this->getColumnManagerSessionKey());
    }

    public function hasDeferredColumnManager(): bool
    {
        return false;
    }

    /**
     * Public wrapper so Livewire can call applyRepeaterColumnManager on this component.
     */
    public function applyRepeaterColumnManager(string $repeaterKey, array $columns): void
    {
        if ($repeaterKey === $this->getStatePath()) {
            $this->applyTableColumnManager($columns);
        }
    }

    /**
     * Public wrapper so Livewire can call resetRepeaterColumnManager on this component.
     */
    public function resetRepeaterColumnManager(string $repeaterKey): void
    {
        if ($repeaterKey === $this->getStatePath()) {
            $this->resetTableColumnManager();
        }
    }

    public function toEmbeddedHtml(): string
    {
        if ($this->hasTableView()) {
            return $this->toEmbeddedTableHtml();
        }

        return (string) parent::toEmbeddedHtml();
    }

    protected function toEmbeddedTableHtml(): string
    {
        return $this->wrapEmbeddedHtml(
            view('support::filament.infolists.components.repeatable-entry.table', [
                'getItems'                      => fn () => $this->getItems(),
                'getTableColumns'               => fn () => $this->getTableColumns(),
                'getExtraItemActions'           => fn () => $this->getExtraItemActions(),
                'hasColumnManager'              => fn () => $this->hasColumnManager(),
                'getExtraAttributeBag'          => fn () => $this->getExtraAttributeBag(),
                'getEmptyTooltip'               => fn () => $this->getEmptyTooltip(),
                'getPlaceholder'                => fn () => $this->getPlaceholder(),
                'getColumnManagerMaxHeight'     => fn () => $this->getColumnManagerMaxHeight(),
                'getColumnManagerWidth'         => fn () => $this->getColumnManagerWidth(),
                'getId'                         => fn () => $this->getId(),
                'getStatePath'                  => fn () => $this->getStatePath(),
                'getColumnManagerTriggerAction' => fn () => $this->getColumnManagerTriggerAction(),
                'getColumnManagerApplyAction'   => fn () => $this->getColumnManagerApplyAction(),
                'getMappedColumns'              => fn () => $this->getMappedColumns(),
                'getColumnManagerColumns'       => fn () => $this->getColumnManagerColumns(),
                'hasToggleableColumns'          => fn () => $this->hasToggleableColumns(),
                'wrapEmbeddedHtml'              => fn ($html) => $this->wrapEmbeddedHtml($html),
            ])->render()
        );
    }
}
