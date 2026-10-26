<?php

namespace Webkul\Crm\Filament\Concerns;

use Webkul\TableViews\Filament\Components\PresetView;

trait ShowsTableViewCounts
{
    protected array $tableViewCounts = [];

    public function getCachedFavoriteTableViews(): array
    {
        $views = $this->getBaseCachedFavoriteTableViews();

        foreach ($views as $key => $view) {
            $view->badge(fn (): int => $this->tableViewCounts[$key] ??= $this->countTableViewRecords($view));
        }

        return $views;
    }

    protected function countTableViewRecords(PresetView $view): int
    {
        return $view->modifyQuery($this->getTableQuery())->count();
    }
}
