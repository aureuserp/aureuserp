<?php

namespace Webkul\Inventory\Filament\Clusters\Configurations\Resources\PutawayRuleResource\Pages;

use Filament\Actions\CreateAction;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ManageRecords;
use Filament\Schemas\Components\Tabs\Tab;
use Illuminate\Support\Facades\Auth;
use Webkul\Inventory\Filament\Clusters\Configurations\Resources\PutawayRuleResource;
use Webkul\Inventory\Models\PutawayRule;

class ManagePutawayRules extends ManageRecords
{
    protected static string $resource = PutawayRuleResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make()
                ->label(__('inventories::filament/clusters/configurations/resources/putaway-rule/pages/manage-putaway-rules.header-actions.create.label'))
                ->icon('heroicon-o-plus-circle')
                ->mutateDataUsing(function (array $data): array {
                    $user = Auth::user();

                    $data['creator_id'] = $user->id;

                    $data['company_id'] ??= $user->default_company_id;

                    return $data;
                })
                ->successNotification(
                    Notification::make()
                        ->success()
                        ->title(__('inventories::filament/clusters/configurations/resources/putaway-rule/pages/manage-putaway-rules.header-actions.create.notification.title'))
                        ->body(__('inventories::filament/clusters/configurations/resources/putaway-rule/pages/manage-putaway-rules.header-actions.create.notification.body')),
                ),
        ];
    }

    public function getTabs(): array
    {
        return [
            'all' => Tab::make(__('inventories::filament/clusters/configurations/resources/putaway-rule/pages/manage-putaway-rules.tabs.all'))
                ->badge(PutawayRule::count()),
            'archived' => Tab::make(__('inventories::filament/clusters/configurations/resources/putaway-rule/pages/manage-putaway-rules.tabs.archived'))
                ->badge(PutawayRule::onlyTrashed()->count())
                ->modifyQueryUsing(fn ($query) => $query->onlyTrashed()),
        ];
    }
}
