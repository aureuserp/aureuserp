<?php

namespace Webkul\Sale\Filament\Clusters\Orders\Resources\Concerns;

use Webkul\Inventory\Filament\Clusters\Operations\Actions\NextTransferAction as BaseNextTransferAction;
use Webkul\Sale\Filament\Clusters\Orders\Resources\Actions\NextTransferAction;

trait ReplacesNextTransferAction
{
    protected function getHeaderActions(): array
    {
        return array_map(
            fn ($action) => $action instanceof BaseNextTransferAction ? NextTransferAction::make() : $action,
            parent::getHeaderActions(),
        );
    }
}
