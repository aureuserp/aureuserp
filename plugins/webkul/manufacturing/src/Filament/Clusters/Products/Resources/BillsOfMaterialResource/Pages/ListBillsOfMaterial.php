<?php

namespace Webkul\Manufacturing\Filament\Clusters\Products\Resources\BillsOfMaterialResource\Pages;

use Filament\Actions\CreateAction;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ListRecords;
use Filament\Schemas\Components\Tabs\Tab;
use Illuminate\Database\Eloquent\Builder;
use Webkul\Manufacturing\Filament\Clusters\Products\Resources\BillsOfMaterialResource;
use Webkul\Manufacturing\Models\BillOfMaterial;

class ListBillsOfMaterial extends ListRecords
{
    protected static string $resource = BillsOfMaterialResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make()
                ->label(__('manufacturing::filament/clusters/products/resources/bill-of-material/pages/list-bills-of-material.header-actions.create.label'))
                ->icon('heroicon-o-plus-circle')
                ->successNotification(
                    Notification::make()
                        ->success()
                        ->title(__('manufacturing::filament/clusters/products/resources/bill-of-material/pages/list-bills-of-material.header-actions.create.notification.title'))
                        ->body(__('manufacturing::filament/clusters/products/resources/bill-of-material/pages/list-bills-of-material.header-actions.create.notification.body')),
                ),
        ];
    }

    public function getTabs(): array
    {
        return [
            'all' => Tab::make(__('manufacturing::filament/clusters/products/resources/bill-of-material/pages/list-bills-of-material.tabs.all'))
                ->badge(BillOfMaterial::count())
                ->modifyQueryUsing(fn (Builder $query) => $query->withoutTrashed()),
            'archived' => Tab::make(__('manufacturing::filament/clusters/products/resources/bill-of-material/pages/list-bills-of-material.tabs.archived'))
                ->badge(BillOfMaterial::onlyTrashed()->count())
                ->modifyQueryUsing(fn ($query) => $query->onlyTrashed()),
        ];
    }
}
