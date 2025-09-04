<?php

namespace Webkul\Blog\Filament\Admin\Clusters\Configurations\Resources\Tags\Pages;

use Filament\Actions\CreateAction;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ManageRecords;
use Filament\Schemas\Components\Tabs\Tab;
use Webkul\Blog\Filament\Admin\Clusters\Configurations\Resources\Tags\TagResource;
use Webkul\Blog\Models\Tag;

class ManageTags extends ManageRecords
{
    protected static string $resource = TagResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make()
                ->label('New Tag')
                ->label(__('blogs::filament/admin/clusters/configurations/resources/tag/pages/manage-tags.header-actions.create.label'))
                ->icon('heroicon-o-plus-circle')
                ->mutateDataUsing(function (array $data): array {
                    $data['creator_id'] = filament()->auth()->id();

                    return $data;
                })
                ->successNotification(
                    Notification::make()
                        ->success()
                        ->title(__('blogs::filament/admin/clusters/configurations/resources/tag/pages/manage-tags.header-actions.create.notification.title'))
                        ->body(__('blogs::filament/admin/clusters/configurations/resources/tag/pages/manage-tags.header-actions.create.notification.body')),
                ),
        ];
    }

    public function getTabs(): array
    {
        return [
            'all' => Tab::make(__('blogs::filament/admin/clusters/configurations/resources/tag/pages/manage-tags.tabs.all'))
                ->badge(Tag::count()),
            'archived' => Tab::make(__('blogs::filament/admin/clusters/configurations/resources/tag/pages/manage-tags.tabs.archived'))
                ->badge(Tag::onlyTrashed()->count())
                ->modifyQueryUsing(function ($query) {
                    return $query->onlyTrashed();
                }),
        ];
    }
}
