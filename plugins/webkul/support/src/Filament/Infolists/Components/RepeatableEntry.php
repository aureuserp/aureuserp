<?php

namespace Webkul\Support\Filament\Infolists\Components;

use Closure;
use Exception;
use Filament\Actions\Action;
use Filament\Forms\Components\Concerns\HasExtraItemActions;
use Filament\Infolists\Components\RepeatableEntry as BaseRepeatableEntry;
use Filament\Schemas\Components\Component;
use Filament\Support\Enums\Alignment;
use Filament\Support\Enums\Size;
use Filament\Tables\Table\Concerns\HasColumnManager;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Support\Arr;
use Illuminate\Support\Js;
use Webkul\Support\Filament\Infolists\Components\Repeater\TableColumn;

class RepeatableEntry extends BaseRepeatableEntry
{
    use HasColumnManager;
    use HasExtraItemActions;

    protected ?string $columnManagerSessionKey = null;

    public function table(array|Closure|null $columns): static
    {
        $this->tableColumns = $columns;

        return $this;
    }

    public function getColumnManagerSessionKey(): string
    {
        return $this->columnManagerSessionKey ??= 'repeater_'.$this->getStatePath().'_column_manager';
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
        return true;
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

    protected function toEmbeddedTableHtml(): string
    {
        $items = $this->getItems();
        $tableColumns = $this->getTableColumns();
        $extraActions = $this->getExtraItemActions();
        $hasExtraActions = ! empty($extraActions);
        $hasColumnManager = $this->hasColumnManager();

        $attributes = $this->getExtraAttributeBag()
            ->class([
                'fi-fo-table-repeater',
                'overflow-x-auto',
            ]);

        if (empty($items)) {
            $attributes = $attributes
                ->merge([
                    'x-tooltip' => filled($tooltip = $this->getEmptyTooltip())
                        ? '{
                            content: '.Js::from($tooltip).',
                            theme: $store.theme,
                            allowHTML: '.Js::from($tooltip instanceof Htmlable).',
                        }'
                        : null,
                ], escape: false);

            $placeholder = $this->getPlaceholder();

            ob_start(); ?>

            <div <?= $attributes->toHtml() ?>>
                <?php if (filled($placeholder)) { ?>
                    <p class="fi-in-placeholder">
                        <?= e($placeholder) ?>
                    </p>
                <?php } ?>
            </div>

            <?php return $this->wrapEmbeddedHtml(ob_get_clean());
        }

        ob_start(); ?>

        <div <?= $attributes->toHtml() ?>>
            <table style="width: max-content;">
                <thead>
                    <tr>
                        <?php foreach ($tableColumns as $column) { ?>
                            <th
                                class="<?= Arr::toCssClasses([
                                    'fi-wrapped' => $column->canHeaderWrap(),
                                    (($columnAlignment = $column->getAlignment()) instanceof Alignment) ? ('fi-align-'.$columnAlignment->value) : $columnAlignment,
                                ]) ?>"
                                style="<?= filled($columnWidth = $column->getWidth())
                                    ? 'width: '.$columnWidth.'; white-space: nowrap; min-width: fit-content; padding: 15px 5px;'
                                    : 'white-space: nowrap; min-width: fit-content; padding: 15px 5px;' ?>"
                            >
                                <?php if (! $column->isHeaderLabelHidden()) { ?>
                                    <?= e($column->getLabel()) ?>
                                <?php } else { ?>
                                    <span class="fi-sr-only">
                                        <?= e($column->getLabel()) ?>
                                    </span>
                                <?php } ?>
                            </th>
                        <?php } ?>

                       <?php if ($hasColumnManager) { ?>
                            <th class="text-center align-middle fi-fo-table-repeater-empty-header-cell" style="width: 75px; white-space: nowrap;">
                                <?php
                                // Render the Blade component properly here:
                                echo \Illuminate\Support\Facades\Blade::render(<<<'BLADE'
                                    <x-filament::dropdown
                                        shift
                                        placement="bottom-end"
                                        :max-height="$maxHeight"
                                        :width="$width"
                                        :wire:key="$key"
                                        class="inline-block fi-ta-col-manager-dropdown"
                                        x-data="{ open: false }"
                                        x-on:click="$dispatch('toggle-dropdown')"
                                        x-on:toggle-dropdown="open = !open"
                                    >
                                        <x-slot name="trigger">
                                            {!! $triggerAction !!}
                                        </x-slot>
                                        <x-support::column-manager
                                            heading-tag="h2"
                                            :apply-action="$applyAction"
                                            :table-columns="$mappedColumns"
                                            :columns="$columns"
                                            has-reorderable-columns="false"
                                            :has-toggleable-columns="$hasToggleableColumns"
                                            reorder-animation-duration="300"
                                            :repeater-key="$statePath"
                                        />
                                    </x-filament::dropdown>
                                BLADE, [
                                    'maxHeight'            => $this->getColumnManagerMaxHeight(),
                                    'width'                => $this->getColumnManagerWidth(),
                                    'key'                  => $this->getId().'.table.column-manager.'.$this->getStatePath(),
                                    'triggerAction'        => $this->getColumnManagerTriggerAction()->toHtml(),
                                    'applyAction'          => $this->getColumnManagerApplyAction(),
                                    'mappedColumns'        => $this->getMappedColumns(),
                                    'columns'              => $this->getColumnManagerColumns(),
                                    'hasToggleableColumns' => $this->hasToggleableColumns(),
                                    'statePath'            => $this->getStatePath(),
                                ]);
                           ?>
                            </th>
                        <?php } ?>
                    </tr>
                </thead>

                <tbody>
                    <?php foreach ($items as $index => $item) { ?>
                        <tr>
                            <?php $counter = 0 ?>
                            <?php foreach ($item->getComponents(withHidden: true) as $component) { ?>
                                <?php throw_unless(
                                    $component instanceof Component,
                                    new Exception('Table repeatable entries must only contain schema components, but ['.$component::class.'] was used.'),
                                ) ?>
                                <?php if (count($tableColumns) > $counter) { ?>
                                    <?php $counter++ ?>
                                    <?php if ($component->isVisible()) { ?>
                                        <td>
                                            <div style="min-width: max-content; padding: 6px 2px"><?= $component->toHtml() ?></div>
                                        </td>
                                    <?php } else { ?>
                                        <td class="fi-hidden"></td>
                                    <?php } ?>
                                <?php } ?>
                            <?php } ?>

                            <?php if ($hasExtraActions) { ?>
                                <td>
                                    <div style="min-width: max-content; padding: 6px 2px" class="flex items-center gap-2">
                                        <?php foreach ($extraActions as $action) { ?>
                                            <?php $action = $action(['item' => $index]); ?>
                                            <div x-on:click.stop>
                                                <?= $action->toHtml() ?>
                                            </div>
                                        <?php } ?>
                                    </div>
                                </td>
                            <?php } ?>

                            <?php if ($hasColumnManager) { ?>
                                <td style="padding: 6px 2px;"></td>
                            <?php } ?>
                        </tr>
                    <?php } ?>
                </tbody>
            </table>
        </div>

        <?php return $this->wrapEmbeddedHtml(ob_get_clean());
    }
}
