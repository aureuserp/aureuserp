<?php

namespace Webkul\Crm\Filament\Actions;

use Filament\Actions\Action;
use Filament\Notifications\Notification;
use Webkul\Crm\Filament\Support\LeadResourceResolver;
use Webkul\Crm\Models\Lead;

class DuplicateLeadAction extends Action
{
    public static function getDefaultName(): ?string
    {
        return 'duplicate';
    }

    protected function setUp(): void
    {
        parent::setUp();

        $this
            ->label(__('crm::filament/lead-actions.duplicate.label'))
            ->icon('heroicon-o-document-duplicate')
            ->color('gray')
            ->visible(fn (Lead $record): bool => DuplicateLeadsBulkAction::canDuplicate($record))
            ->action(function (Lead $record, Action $action): void {
                $copy = DuplicateLeadsBulkAction::duplicate($record);

                Notification::make()
                    ->success()
                    ->title(__('crm::filament/lead-actions.duplicate.notification', ['count' => 1]))
                    ->send();

                $action->redirect(LeadResourceResolver::viewUrl($copy));
            });
    }
}
