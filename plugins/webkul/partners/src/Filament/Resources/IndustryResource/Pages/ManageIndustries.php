<?php

namespace Webkul\Partner\Filament\Resources\IndustryResource\Pages;

use Filament\Actions\CreateAction;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ManageRecords;
use Filament\Schemas\Components\Tabs\Tab;
use Illuminate\Support\Facades\Auth;
use Webkul\Partner\Filament\Resources\IndustryResource;
use Webkul\Partner\Models\Industry;

class ManageIndustries extends ManageRecords
{
    protected static string $resource = IndustryResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make()
                ->label(__('partners::filament/resources/industry/pages/manage-industries.header-actions.create.label'))
                ->icon('heroicon-o-plus-circle')
                ->mutateDataUsing(function (array $data): array {
                    $data['creator_id'] = Auth::id();

                    return $data;
                })
                ->successNotification(
                    Notification::make()
                        ->success()
                        ->title(__('partners::filament/resources/industry/pages/manage-industries.header-actions.create.notification.title'))
                        ->body(__('partners::filament/resources/industry/pages/manage-industries.header-actions.create.notification.body')),
                ),
        ];
    }

    public function getTabs(): array
    {
        return [
            'all' => Tab::make(__('partners::filament/resources/industry/pages/manage-industries.tabs.all'))
                ->badge(Industry::count()),
            'archived' => Tab::make(__('partners::filament/resources/industry/pages/manage-industries.tabs.archived'))
                ->badge(Industry::onlyTrashed()->count())
                ->modifyQueryUsing(function ($query) {
                    return $query->onlyTrashed();
                }),
        ];
    }
}
