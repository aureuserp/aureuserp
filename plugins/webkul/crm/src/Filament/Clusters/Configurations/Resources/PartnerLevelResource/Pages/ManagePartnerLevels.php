<?php

namespace Webkul\Crm\Filament\Clusters\Configurations\Resources\PartnerLevelResource\Pages;

use Filament\Actions\CreateAction;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ManageRecords;
use Filament\Schemas\Components\Tabs\Tab;
use Illuminate\Database\Eloquent\Builder;
use Webkul\Crm\Filament\Clusters\Configurations\Resources\PartnerLevelResource;
use Webkul\Crm\Models\PartnerLevel;

class ManagePartnerLevels extends ManageRecords
{
    protected static string $resource = PartnerLevelResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make()
                ->label(__('crm::filament/clusters/configurations/resources/partner-level.header-actions.create'))
                ->icon('heroicon-o-plus-circle')
                ->successNotification(Notification::make()->success()->title(__('crm::filament/clusters/configurations/resources/partner-level.notifications.created'))),
        ];
    }

    public function getTabs(): array
    {
        $label = fn (string $key): string => __('crm::filament/clusters/configurations/resources/partner-level.tabs.'.$key);

        return [
            'active' => Tab::make($label('active'))
                ->badge(fn (): int => PartnerLevel::query()->count())
                ->modifyQueryUsing(fn (Builder $query): Builder => $query->withoutTrashed()),
            'archived' => Tab::make($label('archived'))
                ->badge(fn (): int => PartnerLevel::onlyTrashed()->count())
                ->modifyQueryUsing(fn (Builder $query): Builder => $query->onlyTrashed()),
        ];
    }
}
