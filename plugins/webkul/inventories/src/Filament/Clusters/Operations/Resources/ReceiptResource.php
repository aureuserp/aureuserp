<?php

namespace Webkul\Inventory\Filament\Clusters\Operations\Resources;

use Filament\Actions\ActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\Page;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\QueryException;
use Webkul\Inventory\Enums\OperationState;
use Webkul\Inventory\Enums\OperationType;
use Webkul\Inventory\Filament\Clusters\Operations;
use Webkul\Inventory\Filament\Clusters\Operations\Resources\ReceiptResource\Pages\CreateReceipt;
use Webkul\Inventory\Filament\Clusters\Operations\Resources\ReceiptResource\Pages\EditReceipt;
use Webkul\Inventory\Filament\Clusters\Operations\Resources\ReceiptResource\Pages\ListReceipts;
use Webkul\Inventory\Filament\Clusters\Operations\Resources\ReceiptResource\Pages\ManageMoves;
use Webkul\Inventory\Filament\Clusters\Operations\Resources\ReceiptResource\Pages\ViewReceipt;
use Webkul\Inventory\Models\Receipt;

class ReceiptResource extends Resource
{
    protected static ?string $model = Receipt::class;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-arrow-down-tray';

    protected static ?int $navigationSort = 1;

    protected static ?string $recordTitleAttribute = 'name';

    protected static ?string $cluster = Operations::class;

    public static function getModelLabel(): string
    {
        return __('inventories::filament/clusters/operations/resources/receipt.navigation.title');
    }

    public static function getNavigationLabel(): string
    {
        return __('inventories::filament/clusters/operations/resources/receipt.navigation.title');
    }

    public static function getNavigationGroup(): string
    {
        return __('inventories::filament/clusters/operations/resources/receipt.navigation.group');
    }

    public static function form(Schema $schema): Schema
    {
        return OperationResource::form($schema);
    }

    public static function table(Table $table): Table
    {
        return OperationResource::table($table)
            ->recordActions([
                ActionGroup::make([
                    ViewAction::make(),
                    EditAction::make(),
                    DeleteAction::make()
                        ->hidden(fn (Receipt $record): bool => $record->state == OperationState::DONE)
                        ->action(function (Receipt $record) {
                            try {
                                $record->delete();
                            } catch (QueryException $e) {
                                Notification::make()
                                    ->danger()
                                    ->title(__('inventories::filament/clusters/operations/resources/receipt.table.actions.delete.notification.error.title'))
                                    ->body(__('inventories::filament/clusters/operations/resources/receipt.table.actions.delete.notification.error.body'))
                                    ->send();
                            }
                        })
                        ->successNotification(
                            Notification::make()
                                ->success()
                                ->title(__('inventories::filament/clusters/operations/resources/receipt.table.actions.delete.notification.success.title'))
                                ->body(__('inventories::filament/clusters/operations/resources/receipt.table.actions.delete.notification.success.body')),
                        ),
                ]),
            ])
            ->toolbarActions([
                DeleteBulkAction::make()
                    ->action(function (Collection $records) {
                        try {
                            $records->each(fn (Model $record) => $record->delete());
                        } catch (QueryException $e) {
                            Notification::make()
                                ->danger()
                                ->title(__('inventories::filament/clusters/operations/resources/receipt.table.bulk-actions.delete.notification.error.title'))
                                ->body(__('inventories::filament/clusters/operations/resources/receipt.table.bulk-actions.delete.notification.error.body'))
                                ->send();
                        }
                    })
                    ->successNotification(
                        Notification::make()
                            ->success()
                            ->title(__('inventories::filament/clusters/operations/resources/receipt.table.bulk-actions.delete.notification.success.title'))
                            ->body(__('inventories::filament/clusters/operations/resources/receipt.table.bulk-actions.delete.notification.success.body')),
                    ),
            ])
            ->modifyQueryUsing(function (Builder $query) {
                return $query->whereHas('operationType', function (Builder $query) {
                    $query->where('type', OperationType::INCOMING);
                });
            });
    }

    public static function infolist(Schema $schema): Schema
    {
        return OperationResource::infolist($schema);
    }

    public static function getRecordSubNavigation(Page $page): array
    {
        return $page->generateNavigationItems([
            ViewReceipt::class,
            EditReceipt::class,
            ManageMoves::class,
        ]);
    }

    public static function getPages(): array
    {
        return [
            'index'  => ListReceipts::route('/'),
            'create' => CreateReceipt::route('/create'),
            'edit'   => EditReceipt::route('/{record}/edit'),
            'view'   => ViewReceipt::route('/{record}/view'),
            'moves'  => ManageMoves::route('/{record}/moves'),
        ];
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->orderByDesc('id');
    }
}
