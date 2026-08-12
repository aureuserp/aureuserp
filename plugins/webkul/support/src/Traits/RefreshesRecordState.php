<?php

namespace Webkul\Support\Traits;

trait RefreshesRecordState
{
    protected function afterSave(): void
    {
        $this->refreshRecordState();
    }

    protected function refreshRecordState(): void
    {
        $this->record->refresh();

        $this->record->unsetRelations();

        $this->fillForm();

        $this->rememberData();
    }
}
