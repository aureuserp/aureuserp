<?php

namespace Webkul\TableViews\Filament\Concerns;

use Closure;
use Filament\Actions\Action;
use Filament\Actions\ActionGroup;
use Filament\Support\Concerns\EvaluatesClosures;
use Filament\Support\Enums\Width;
use Illuminate\Database\Eloquent\Builder;
use Livewire\Attributes\Url;
use Webkul\TableViews\Filament\Actions\CreateViewAction;
use Webkul\TableViews\Filament\Actions\EditViewAction;
use Webkul\TableViews\Filament\Components\PresetView;
use Webkul\TableViews\Filament\Components\SavedView;
use Webkul\TableViews\Models\TableView as TableViewModel;
use Webkul\TableViews\Models\TableViewFavorite as TableViewFavoriteModel;

trait HasTableViews
{
    use EvaluatesClosures;

    #[Url]
    public ?string $activeTableView = null;

    /**
     * @var array<string | int, TableView>
     */
    protected array $cachedTableViews;

    /**
     * @var array<string | int, PresetView | TableView>
     */
    protected array $cachedFavoriteTableViews;

    protected string|Closure|null $tableViewsFormMaxHeight = '500px';

    protected Width|string|Closure|null $tableViewsFormWidth = null;

    public function bootedInteractsWithTable(): void
    {
        parent::bootedInteractsWithTable();

        $this->loadDefaultActiveTableView();
    }

    protected function loadDefaultActiveTableView(): void
    {
        if (filled($this->activeTableView)) {
            $this->applyTableViewFilters();

            return;
        }

        $this->activeTableView = $this->getDefaultActiveTableView();
    }

    public function loadView($tabKey): void
    {
        $this->resetTableViews();

        $this->activeTableView = $tabKey;

        $this->applyTableViewFilters();
    }

    public function resetTableViews(): void
    {
        $this->resetTable();

        $this->resetPage();

        $this->resetTableSearch();

        $this->resetTableSort();

        $this->resetTableGrouping();

        $this->activeTableView = $this->getDefaultActiveTableView();
    }

    public function resetTableSort(): void
    {
        $this->tableSort = null;
    }

    public function resetTableGrouping(): void
    {
        $this->tableGrouping = null;

        $this->tableGroupingDirection = null;
    }

    public function applyTableViewFilters(): void
    {
        $tableViews = $this->getAllTableViews();

        if (! array_key_exists($this->activeTableView, $tableViews)) {
            return;
        }

        if (! $tableViews[$this->activeTableView] instanceof SavedView) {
            return;
        }

        foreach ($tableViews[$this->activeTableView]->getRecord()->filters as $key => $filter) {
            if (! $filter) {
                continue;
            }

            $this->{$key} = $filter;
        }
    }

    /**
     * @return array<string>
     */
    public function getPresetTableViews(): array
    {
        return [];
    }

    /**
     * @return array<string | int, Tab>
     */
    public function getSavedTableViews(): array
    {
        return TableViewModel::where('filterable_type', static::class)
            ->where(function ($query) {
                $query->where('user_id', filament()->auth()->id())
                    ->orWhere('is_public', true);
            })
            ->get()
            ->mapWithKeys(function (TableViewModel $tableView) {
                return [
                    $tableView->id => SavedView::make($tableView->getKey())
                        ->model($tableView)
                        ->label($tableView->name)
                        ->icon($tableView->icon)
                        ->color($tableView->color),
                ];
            })->all();
    }

    /**
     * @return array<string | int, Tab>
     */
    public function getFavoriteTableViews(): array
    {
        return collect($this->getAllTableViews())
            ->filter(function (PresetView $presetView, string|int $id) {
                return $presetView->isFavorite($id);
            })
            ->all();
    }

    /**
     * @return array<string | int, Tab>
     */
    public function getAllTableViews(): array
    {
        return $this->getPresetTableViews() + $this->getCachedTableViews();
    }

    /**
     * @return array<string | int, Tab>
     */
    public function getCachedFavoriteTableViews(): array
    {
        return $this->cachedFavoriteTableViews ??= (
            [
                'default' => PresetView::make('default')
                    ->label(__('table-views::filament/concerns/has-table-views.default'))
                    ->icon('heroicon-m-queue-list')
                    ->favorite(),
            ] + $this->getFavoriteTableViews()
        );
    }

    /**
     * @return array<string | int, Tab>
     */
    public function getCachedTableViews(): array
    {
        return $this->cachedTableViews ??= $this->getSavedTableViews();
    }

    public function getDefaultActiveTableView(): string|int|null
    {
        $defaultViewKey = array_key_first(array_filter($this->getCachedFavoriteTableViews(), function ($view) {
            return $view->isDefault() === true;
        }));

        return $defaultViewKey ?? array_key_first($this->getCachedFavoriteTableViews());
    }

    public function updatedActiveTableView(): void
    {
        $this->resetPage();
    }

    public function isActiveTableViewModified(): bool
    {
        $tableViews = $this->getAllTableViews();

        if (! array_key_exists($this->activeTableView, $tableViews)) {
            return false;
        }

        if (! $tableViews[$this->activeTableView] instanceof SavedView) {
            return false;
        }

        return [
            'tableFilters'        => $this->tableFilters,
            'tableGrouping'       => $this->tableGrouping,
            'tableSearch'         => $this->tableSearch,
            'tableColumnSearches' => $this->tableColumnSearches,
            'tableSort'           => $this->tableSort,
            'tableRecordsPerPage' => $this->tableRecordsPerPage,
        ] != $tableViews[$this->activeTableView]->getRecord()->filters;
    }

    protected function modifyQueryWithActiveTab(Builder $query): Builder
    {
        if (blank(filled($this->activeTableView))) {
            return $query;
        }

        $tableViews = $this->getAllTableViews();

        if (! array_key_exists($this->activeTableView, $tableViews)) {
            return $query;
        }

        return $tableViews[$this->activeTableView]->modifyQuery($query);
    }

    public function setTableViewsFormMaxHeight(string|Closure|null $height): static
    {
        $this->tableViewsFormMaxHeight = $height;

        return $this;
    }

    public function setTableViewsFormWidth(Width|string|Closure|null $width): static
    {
        $this->tableViewsFormWidth = $width;

        return $this;
    }

    public function getPresetTableViewsFormMaxHeight(): ?string
    {
        return $this->evaluate($this->tableViewsFormMaxHeight);
    }

    public function getPresetTableViewsFormWidth(): Width|string|null
    {
        return $this->evaluate($this->tableViewsFormWidth) ?? Width::ExtraSmall;
    }

    public function getActiveTableView()
    {
        return $this->activeTableView;
    }

    public function getTableViewsTriggerAction(): Action
    {
        return Action::make('openTableViews')
            ->label(__('table-views::filament/concerns/has-table-views.title'))
            ->iconButton()
            ->icon('heroicon-m-ellipsis-vertical')
            ->livewireClickHandlerEnabled(false)
            ->modalSubmitAction(false);
    }

    public function createTableViewAction(): Action
    {
        return CreateViewAction::make('createTableView')
            ->mutateDataUsing(function (array $data): array {
                $data['user_id'] = filament()->auth()->id();

                $data['filterable_type'] = static::class;

                $data['filters'] = [
                    'tableFilters'        => $this->tableFilters,
                    'tableGrouping'       => $this->tableGrouping,
                    'tableSearch'         => $this->tableSearch,
                    'tableColumnSearches' => $this->tableColumnSearches,
                    'tableSort'           => $this->tableSort,
                    'tableRecordsPerPage' => $this->tableRecordsPerPage,
                ];

                return $data;
            })
            ->after(function (TableViewModel $saveFilter): void {
                unset($this->cachedTableViews);
                unset($this->cachedFavoriteTableViews);

                $this->getCachedTableViews();
                $this->getCachedFavoriteTableViews();

                $this->dispatch('filtered-list-updated');

                $this->activeTableView = $saveFilter->id;
            });
    }

    public function resetTableViewAction(): Action
    {
        return Action::make('resetTableView')
            ->label('Reset')
            ->label(__('table-views::filament/concerns/has-table-views.reset'))
            ->color('danger')
            ->link()
            ->action(function () {
                $this->resetTableViews();
            });
    }

    public function applyTableViewAction(): Action
    {
        return Action::make('applyTableView')
            ->label(__('table-views::filament/concerns/has-table-views.apply-view'))
            ->icon('heroicon-s-arrow-small-right')
            ->action(function (array $arguments) {
                $this->resetTableViews();

                $this->activeTableView = $arguments['view_key'];

                $this->applyTableViewFilters();
            });
    }

    public function addTableViewToFavoritesAction(): Action
    {
        return Action::make('addTableViewToFavorites')
            ->label(__('table-views::filament/concerns/has-table-views.add-to-favorites'))
            ->icon('heroicon-o-star')
            ->action(function (array $arguments) {
                TableViewFavoriteModel::updateOrCreate(
                    [
                        'view_type'       => $arguments['view_type'],
                        'view_key'        => $arguments['view_key'],
                        'filterable_type' => static::class,
                        'user_id'         => filament()->auth()->id(),
                    ],
                    [
                        'is_favorite' => true,
                    ]
                );

                unset($this->cachedTableViews);
                unset($this->cachedFavoriteTableViews);
            });
    }

    public function removeTableViewFromFavoritesAction(): Action
    {
        return Action::make('removeTableViewFromFavorites')
            ->label(__('table-views::filament/concerns/has-table-views.remove-from-favorites'))
            ->icon('heroicon-o-minus-circle')
            ->action(function (array $arguments) {
                TableViewFavoriteModel::updateOrCreate(
                    [
                        'view_type'       => $arguments['view_type'],
                        'view_key'        => $arguments['view_key'],
                        'filterable_type' => static::class,
                        'user_id'         => filament()->auth()->id(),
                    ],
                    [
                        'is_favorite' => false,
                    ]
                );

                unset($this->cachedTableViews);
                unset($this->cachedFavoriteTableViews);
            });
    }

    public function editTableViewAction(): Action
    {
        return EditViewAction::make('editTableView')
            ->after(function (): void {
                unset($this->cachedTableViews);
                unset($this->cachedFavoriteTableViews);

                $this->getCachedTableViews();
                $this->getCachedFavoriteTableViews();
            });
    }

    public function deleteTableViewAction(): Action
    {
        return Action::make('deleteTableView')
            ->label(__('table-views::filament/concerns/has-table-views.delete-view'))
            ->icon('heroicon-m-trash')
            ->color('danger')
            ->requiresConfirmation()
            ->action(function (array $arguments) {
                TableViewModel::find($arguments['view_key'])->delete();

                TableViewFavoriteModel::where('view_key', $arguments['view_key'])
                    ->where('filterable_type', (string) static::class)
                    ->delete();

                unset($this->cachedTableViews);
                unset($this->cachedFavoriteTableViews);
            });
    }

    public function replaceTableViewAction(): Action
    {
        return Action::make('replaceTableView')
            ->label(__('table-views::filament/concerns/has-table-views.replace-view'))
            ->icon('heroicon-m-arrows-right-left')
            ->color('danger')
            ->requiresConfirmation()
            ->action(function (array $arguments) {
                TableViewModel::find($arguments['view_key'])->update([
                    'filters' => [
                        'tableFilters'        => $this->tableFilters,
                        'tableGrouping'       => $this->tableGrouping,
                        'tableSearch'         => $this->tableSearch,
                        'tableColumnSearches' => $this->tableColumnSearches,
                        'tableSort'           => $this->tableSort,
                        'tableRecordsPerPage' => $this->tableRecordsPerPage,
                    ],
                ]);

                unset($this->cachedTableViews);
                unset($this->cachedFavoriteTableViews);
            });
    }

    public function getTableViewActionGroup(string $key, string $type, mixed $tableView): ActionGroup
    {
        return ActionGroup::make([
            $this->applyTableViewAction()
                ->arguments([
                    'view_key'  => $key,
                    'view_type' => $type,
                ])
                ->visible(fn () => $key != $this->activeTableView),

            $this->addTableViewToFavoritesAction()
                ->arguments([
                    'view_key'  => $key,
                    'view_type' => $type,
                ])
                ->visible(fn () => ! $tableView->isFavorite($key)),

            $this->removeTableViewFromFavoritesAction()
                ->arguments([
                    'view_key'  => $key,
                    'view_type' => $type,
                ])
                ->visible(fn () => $tableView->isFavorite($key)),

            $this->editTableViewAction(['view_model' => $tableView->getModel()])
                ->arguments([
                    'view_key'  => $key,
                    'view_type' => $type,
                ])
                ->visible(fn () => $tableView->isEditable()),

            ActionGroup::make([
                $this->replaceTableViewAction()
                    ->arguments([
                        'view_key'  => $key,
                        'view_type' => $type,
                    ])
                    ->visible(fn () => $tableView->isReplaceable() && $key == $this->activeTableView && $this->isActiveTableViewModified()),

                $this->deleteTableViewAction()
                    ->arguments([
                        'view_key'  => $key,
                        'view_type' => $type,
                    ])
                    ->visible(fn () => $key == $tableView->isDeletable()),
            ])->dropdown(false),
        ])->dropdownPlacement('bottom-end');
    }
}
