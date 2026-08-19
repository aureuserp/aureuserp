<?php

namespace Webkul\Product\Filament\Resources\AttributeResource\Tables;

use Closure;
use Filament\Actions\BulkAction;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\CreateAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ForceDeleteAction;
use Filament\Actions\ForceDeleteBulkAction;
use Filament\Actions\RestoreAction;
use Filament\Actions\RestoreBulkAction;
use Filament\Actions\ViewAction;
use Filament\Notifications\Notification;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Grouping\Group;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Collection;
use Throwable;
use Webkul\Product\Enums\AttributeType;
use Webkul\Product\Models\Attribute;
use Webkul\Product\Models\Product;
use Webkul\Product\Models\ProductAttribute;

class AttributesTable
{
    protected const BLOCKING_PRODUCTS_SHOWN = 3;

    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')
                    ->label(__('products::filament/resources/attribute.table.columns.name'))
                    ->searchable(),
                TextColumn::make('type')
                    ->label(__('products::filament/resources/attribute.table.columns.type'))
                    ->sortable(),
                TextColumn::make('created_at')
                    ->label(__('products::filament/resources/attribute.table.columns.created-at'))
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('updated_at')
                    ->label(__('products::filament/resources/attribute.table.columns.updated-at'))
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->groups([
                Group::make('type')
                    ->label(__('products::filament/resources/attribute.table.groups.type'))
                    ->collapsible(),
                Group::make('created_at')
                    ->label(__('products::filament/resources/attribute.table.groups.created-at'))
                    ->collapsible(),
                Group::make('updated_at')
                    ->label(__('products::filament/resources/attribute.table.groups.updated-at'))
                    ->date()
                    ->collapsible(),
            ])
            ->filters([
                SelectFilter::make('type')
                    ->label(__('products::filament/resources/attribute.table.filters.type'))
                    ->options(AttributeType::class)
                    ->searchable()
                    ->preload(),
            ])
            ->recordActions([
                ViewAction::make()
                    ->hidden(fn ($record) => $record->trashed()),
                EditAction::make()
                    ->hidden(fn ($record) => $record->trashed()),
                RestoreAction::make()
                    ->successNotification(
                        Notification::make()
                            ->success()
                            ->title(__('products::filament/resources/attribute.table.actions.restore.notification.title'))
                            ->body(__('products::filament/resources/attribute.table.actions.restore.notification.body')),
                    ),
                DeleteAction::make()
                    ->successNotification(
                        Notification::make()
                            ->success()
                            ->title(__('products::filament/resources/attribute.table.actions.delete.notification.title'))
                            ->body(__('products::filament/resources/attribute.table.actions.delete.notification.body')),
                    ),
                ForceDeleteAction::make()
                    ->before(function (ForceDeleteAction $action, Attribute $record) {
                        if ($record->productAttributes()->exists()) {
                            Notification::make()
                                ->danger()
                                ->title(__('products::filament/resources/attribute.table.actions.force-delete.notification.error.title'))
                                ->body(__('products::filament/resources/attribute.table.actions.force-delete.notification.error.body', [
                                    'products' => self::blockingProductNames($record),
                                ]))
                                ->send();

                            $action->halt();
                        }
                    })
                    ->successNotification(
                        Notification::make()
                            ->success()
                            ->title(__('products::filament/resources/attribute.table.actions.force-delete.notification.success.title'))
                            ->body(__('products::filament/resources/attribute.table.actions.force-delete.notification.success.body')),
                    ),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    RestoreBulkAction::make()
                        ->successNotification(
                            Notification::make()
                                ->success()
                                ->title(__('products::filament/resources/attribute.table.bulk-actions.restore.notification.title'))
                                ->body(__('products::filament/resources/attribute.table.bulk-actions.restore.notification.body')),
                        ),
                    DeleteBulkAction::make()
                        ->successNotification(
                            Notification::make()
                                ->success()
                                ->title(__('products::filament/resources/attribute.table.bulk-actions.delete.notification.title'))
                                ->body(__('products::filament/resources/attribute.table.bulk-actions.delete.notification.body')),
                        ),
                    ForceDeleteBulkAction::make()
                        ->successNotification(null)
                        ->using(fn (BulkAction $action, Collection $records) => self::processBulkDeletion(
                            $action,
                            $records,
                            fn (Attribute $record): bool => (bool) $record->forceDelete(),
                            'products::filament/resources/attribute.table.bulk-actions.force-delete.notification',
                        )),
                ]),
            ])
            ->emptyStateActions([
                CreateAction::make()
                    ->icon('heroicon-o-plus-circle'),
            ]);
    }

    protected static function blockingProductNames(Attribute $record): string
    {
        return self::describeBlockingProducts([$record->getKey()]);
    }

    protected static function describeBlockingProducts(array $attributeIds): string
    {
        if (empty($attributeIds)) {
            return '';
        }

        $productIds = ProductAttribute::query()
            ->whereIn('attribute_id', $attributeIds)
            ->select('product_id');

        $names = Product::withTrashed()
            ->whereIn('id', $productIds)
            ->orderBy('id')
            ->limit(self::BLOCKING_PRODUCTS_SHOWN)
            ->pluck('name')
            ->filter()
            ->values();

        $total = Product::withTrashed()->whereIn('id', $productIds)->count();

        $remaining = $total - $names->count();

        return $names->implode(', ')
            .($remaining > 0
                ? ' '.__('products::filament/resources/attribute.table.actions.force-delete.notification.error.more', [
                    'count' => $remaining,
                ])
                : '');
    }

    protected static function partitionByUsage(Collection $records): array
    {
        // Cheap existence check: only the distinct FK ids are needed to partition.
        $usedIds = ProductAttribute::whereIn('attribute_id', $records->modelKeys())
            ->distinct()
            ->pluck('attribute_id')
            ->flip();

        return $records
            ->partition(fn (Attribute $record) => $usedIds->has($record->getKey()))
            ->all();
    }

    protected static function processBulkDeletion(BulkAction $action, Collection $records, Closure $delete, string $key): void
    {
        [$blocked, $deletable] = self::partitionByUsage($records);

        $deleted = new Collection;

        $hasReportedException = false;

        foreach ($deletable as $record) {
            try {
                if ($delete($record)) {
                    $deleted->push($record);
                } else {
                    $action->reportBulkProcessingFailure();
                }
            } catch (Throwable $exception) {
                $action->reportBulkProcessingFailure();

                if (! $hasReportedException) {
                    report($exception);

                    $hasReportedException = true;
                }
            }
        }

        self::notifyBulkDeletion($deleted, $blocked, $key);
    }

    protected static function notifyBulkDeletion(Collection $deleted, Collection $blocked, string $key): void
    {
        if ($blocked->isNotEmpty()) {
            Notification::make()
                ->warning()
                ->title(__("{$key}.partial.title"))
                ->body(__("{$key}.partial.body", [
                    'attributes' => $blocked->pluck('name')->implode(', '),
                    'products'   => self::describeBlockingProducts($blocked->modelKeys()),
                ]))
                ->send();
        }

        if ($deleted->isNotEmpty()) {
            Notification::make()
                ->success()
                ->title(__("{$key}.success.title"))
                ->body(__("{$key}.success.body"))
                ->send();
        }
    }
}
