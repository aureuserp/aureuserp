<?php

namespace Webkul\Crm\Filament\Actions;

use Closure;
use Filament\Actions\ActionGroup;
use Filament\Actions\ForceDeleteAction;
use Filament\Actions\RestoreAction;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\Page;
use Webkul\Crm\Filament\Support\LeadResourceResolver;
use Webkul\Crm\Models\Lead;

class LeadRecordActions
{
    public static function make(Closure $refresh, Notification $deleted): ActionGroup
    {
        return ActionGroup::make([
            SendLeadEmailAction::make(),
            SendLeadSMSAction::make(),
            DuplicateLeadAction::make(),
            ArchiveLeadAction::make()
                ->after($refresh),
            RestoreAction::make()
                ->label(__('crm::filament/lead-actions.unarchive.label'))
                ->after($refresh),
            ForceDeleteAction::make()
                ->label(__('crm::filament/lead-actions.delete.label'))
                ->visible(fn (?Lead $record): bool => $record && LeadResourceResolver::resourceFor($record)::canForceDelete($record))
                ->successNotification($deleted)
                ->successRedirectUrl(fn (Page $livewire): string => $livewire::getResource()::getUrl('index')),
        ])
            ->label(__('crm::filament/lead-actions.group.label'))
            ->icon('heroicon-m-cog-6-tooth')
            ->color('gray')
            ->button();
    }
}
