<?php

namespace Webkul\Blog\Filament\Admin\Clusters\Configurations\Resources\CategoryResource\Pages;

use Filament\Actions\CreateAction;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ManageRecords;
use Filament\Schemas\Components\Tabs\Tab;
use Illuminate\Support\Facades\Auth;
use Webkul\Blog\Filament\Admin\Clusters\Configurations\Resources\CategoryResource;
use Webkul\Blog\Models\Category;

class ManageCategories extends ManageRecords
{
    protected static string $resource = CategoryResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make()
                ->label(__('blogs::filament/admin/clusters/configurations/resources/category/pages/manage-categories.header-actions.create.label'))
                ->icon('heroicon-o-plus-circle')
                ->mutateDataUsing(function (array $data): array {
                    $data['creator_id'] = Auth::id();

                    return $data;
                })
                ->after(function ($record) {
                    return redirect(
                        static::$resource::getUrl('index', ['record' => $record]),
                    );
                })
                ->successNotification(
                    Notification::make()
                        ->success()
                        ->title(__('blogs::filament/admin/clusters/configurations/resources/category/pages/manage-categories.header-actions.create.notification.title'))
                        ->body(__('blogs::filament/admin/clusters/configurations/resources/category/pages/manage-categories.header-actions.create.notification.body')),
                ),
        ];
    }

    public function getTabs(): array
    {
        return [
            'all' => Tab::make(__('blogs::filament/admin/clusters/configurations/resources/category/pages/manage-categories.tabs.all'))
                ->badge(Category::count()),
            'archived' => Tab::make(__('blogs::filament/admin/clusters/configurations/resources/category/pages/manage-categories.tabs.archived'))
                ->badge(Category::onlyTrashed()->count())
                ->modifyQueryUsing(fn ($query) => $query->onlyTrashed()),
        ];
    }
}
