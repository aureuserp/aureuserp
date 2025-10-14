<?php

namespace Webkul\Product\Filament\Resources\AttributeResource\Pages;

use Filament\Actions\CreateAction;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ListRecords;
use Filament\Schemas\Components\Tabs\Tab;
use Illuminate\Support\Facades\Auth;
use Webkul\Product\Filament\Resources\AttributeResource;
use Webkul\Product\Models\Attribute;

class ListAttributes extends ListRecords
{
    protected static string $resource = AttributeResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make()
                ->label(__('products::filament/resources/attribute/pages/list-attributes.header-actions.create.label'))
                ->icon('heroicon-o-plus-circle')
                ->mutateDataUsing(function ($data) {
                    $user = Auth::user();

                    $data['creator_id'] = $user->id;

                    $data['company_id'] = $user->default_company_id;

                    return $data;
                })
                ->successNotification(
                    Notification::make()
                        ->success()
                        ->title(__('products::filament/resources/attribute/pages/list-attributes.header-actions.create.notification.title'))
                        ->body(__('products::filament/resources/attribute/pages/list-attributes.header-actions.create.notification.body')),
                ),
        ];
    }

    public function getTabs(): array
    {
        return [
            'all' => Tab::make(__('products::filament/resources/attribute/pages/list-attributes.tabs.all'))
                ->badge(Attribute::count()),
            'archived' => Tab::make(__('products::filament/resources/attribute/pages/list-attributes.tabs.archived'))
                ->badge(Attribute::onlyTrashed()->count())
                ->modifyQueryUsing(function ($query) {
                    return $query->onlyTrashed();
                }),
        ];
    }
}
