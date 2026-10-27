<?php

namespace Webkul\Crm\Filament\Actions;

use Filament\Actions\Action;
use Filament\Notifications\Notification;
use Webkul\Crm\Models\Lead;

class ArchiveLeadAction extends Action
{
    public static function getDefaultName(): ?string
    {
        return 'archive';
    }

    protected function setUp(): void
    {
        parent::setUp();

        $this
            ->label(__('crm::filament/lead-actions.archive.label'))
            ->icon('heroicon-o-archive-box')
            ->color('gray')
            ->requiresConfirmation()
            ->modalDescription(__('crm::filament/lead-actions.archive.description'))
            ->visible(fn (Lead $record): bool => ArchiveLeadsBulkAction::canArchive($record))
            ->action(function (Lead $record): void {
                $record->delete();

                Notification::make()
                    ->success()
                    ->title(__('crm::filament/lead-actions.archive.notification', ['count' => 1]))
                    ->send();
            });
    }
}
