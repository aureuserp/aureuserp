<?php

namespace Webkul\Crm\Filament\Actions;

use Filament\Actions\ImportAction;

class CrmImportAction extends ImportAction
{
    protected function setUp(): void
    {
        parent::setUp();

        $this
            ->label(__('crm::filament/transfers.actions.import.label'))
            ->icon('heroicon-o-arrow-down-tray')
            ->color('gray')
            ->maxRows(5000);
    }
}
