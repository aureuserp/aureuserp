<?php

namespace Webkul\Crm\Filament\Clusters\Configurations\Resources\LostReasonResource\Pages;

use Filament\Actions\CreateAction;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ManageRecords;
use Filament\Schemas\Components\Tabs\Tab;
use Illuminate\Database\Eloquent\Builder;
use Webkul\Crm\Filament\Clusters\Configurations\Resources\LostReasonResource;
use Webkul\Crm\Models\LostReason;

class ManageLostReasons extends ManageRecords
{
    protected static string $resource = LostReasonResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make()
                ->label(__('crm::filament/clusters/configurations/resources/lost-reason/pages/manage-lost-reasons.header-actions.create.label'))
                ->icon('heroicon-o-plus-circle')
                ->successNotification(
                    Notification::make()
                        ->success()
                        ->title(__('crm::filament/clusters/configurations/resources/lost-reason/pages/manage-lost-reasons.header-actions.create.notification.title'))
                        ->body(__('crm::filament/clusters/configurations/resources/lost-reason/pages/manage-lost-reasons.header-actions.create.notification.body')),
                ),
        ];
    }

    public function getTabs(): array
    {
        return [
            'active' => Tab::make(__('crm::filament/clusters/configurations/resources/lost-reason/pages/manage-lost-reasons.tabs.active'))
                ->badge(fn (): int => LostReason::query()->count())
                ->modifyQueryUsing(fn (Builder $query): Builder => $query->withoutTrashed()),
            'archived' => Tab::make(__('crm::filament/clusters/configurations/resources/lost-reason/pages/manage-lost-reasons.tabs.archived'))
                ->badge(fn (): int => LostReason::onlyTrashed()->count())
                ->modifyQueryUsing(fn (Builder $query): Builder => $query->onlyTrashed()),
        ];
    }
}
