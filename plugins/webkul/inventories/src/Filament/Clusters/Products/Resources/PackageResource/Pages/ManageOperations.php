<?php

namespace Webkul\Inventory\Filament\Clusters\Products\Resources\PackageResource\Pages;

use Filament\Actions\ActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ManageRelatedRecords;
use Filament\Tables\Table;
use Webkul\Inventory\Enums\OperationState;
use Webkul\Inventory\Filament\Clusters\Operations\Resources\OperationResource;
use Webkul\Inventory\Filament\Clusters\Products\Resources\PackageResource;
use Webkul\Inventory\Models\Operation;
use Webkul\Support\Traits\HasRecordNavigationTabs;
use Webkul\TableViews\Filament\Concerns\HasTableViews;

class ManageOperations extends ManageRelatedRecords
{
    use HasRecordNavigationTabs;
    use HasTableViews;

    protected static string $resource = PackageResource::class;

    protected static string $relationship = 'operations';

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-arrows-right-left';

    public static function getNavigationLabel(): string
    {
        return __('inventories::filament/clusters/products/resources/package/pages/manage-operations.title');
    }

    public function getPresetTableViews(): array
    {
        return OperationResource::getPresetTableViews();
    }

    public function table(Table $table): Table
    {
        return OperationResource::table($table)
            ->recordActions([
                ActionGroup::make([
                    ViewAction::make()
                        ->url(fn ($record): string => OperationResource::getUrl('view', ['record' => $record])),
                    EditAction::make()
                        ->url(fn ($record): string => OperationResource::getUrl('edit', ['record' => $record])),
                    DeleteAction::make()
                        ->hidden(fn (Operation $record): bool => $record->state == OperationState::DONE)
                        ->successNotification(
                            Notification::make()
                                ->success()
                                ->title(__('inventories::filament/clusters/operations/resources/receipt.table.actions.delete.notification.title'))
                                ->body(__('inventories::filament/clusters/operations/resources/receipt.table.actions.delete.notification.body')),
                        ),
                ]),
            ]);
    }
}
