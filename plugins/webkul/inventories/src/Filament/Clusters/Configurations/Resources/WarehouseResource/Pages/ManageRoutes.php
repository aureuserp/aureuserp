<?php

namespace Webkul\Inventory\Filament\Clusters\Configurations\Resources\WarehouseResource\Pages;

use Filament\Actions\CreateAction;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ManageRelatedRecords;
use Filament\Schemas\Schema;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Auth;
use Webkul\Inventory\Filament\Clusters\Configurations\Resources\RouteResource;
use Webkul\Inventory\Filament\Clusters\Configurations\Resources\WarehouseResource;
use Webkul\Support\Traits\HasRecordNavigationTabs;

class ManageRoutes extends ManageRelatedRecords
{
    use HasRecordNavigationTabs;

    protected static string $resource = WarehouseResource::class;

    protected static string $relationship = 'routes';

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-arrow-path';

    public static function getNavigationLabel(): string
    {
        return __('inventories::filament/clusters/configurations/resources/warehouse/pages/manage-routes.title');
    }

    public function form(Schema $schema): Schema
    {
        return RouteResource::form($schema);
    }

    public function table(Table $table): Table
    {
        return RouteResource::table($table)
            ->headerActions([
                CreateAction::make()
                    ->label(__('inventories::filament/clusters/configurations/resources/warehouse/pages/manage-routes.table.header-actions.create.label'))
                    ->icon('heroicon-o-plus-circle')
                    ->fillForm(function (array $arguments): array {
                        return [
                            'warehouse_selectable' => true,
                            'warehouses'           => [$this->getOwnerRecord()->id],
                        ];
                    })
                    ->mutateDataUsing(function (array $data): array {
                        $data['warehouse_selectable'] = true;

                        $data['creator_id'] = Auth::id();

                        $data['company_id'] = $data['company_id'] ?? Auth::user()->default_company_id;

                        return $data;
                    })
                    ->successNotification(
                        Notification::make()
                            ->success()
                            ->title(__('inventories::filament/clusters/configurations/resources/warehouse/pages/manage-routes.table.header-actions.create.notification.title'))
                            ->body(__('inventories::filament/clusters/configurations/resources/warehouse/pages/manage-routes.table.header-actions.create.notification.body')),
                    ),
            ]);
    }
}
