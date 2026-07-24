<?php

namespace Webkul\Manufacturing\Filament\Clusters\Operations\Resources;

use Filament\Actions\ActionGroup;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Resources\ParentResourceRegistration;
use Filament\Resources\Pages\Page;
use Filament\Resources\Resource;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Webkul\Inventory\Filament\Clusters\Operations\Resources\OperationResource;
use Webkul\Inventory\Models\Operation;
use Webkul\Manufacturing\Filament\Clusters\Operations;
use Webkul\Manufacturing\Filament\Clusters\Operations\Resources\ManufacturingOrderResource;
use Webkul\Manufacturing\Filament\Clusters\Operations\Resources\TransferResource\Pages\EditTransfer;
use Webkul\Manufacturing\Filament\Clusters\Operations\Resources\TransferResource\Pages\ManageMoves;
use Webkul\Manufacturing\Filament\Clusters\Operations\Resources\TransferResource\Pages\ViewTransfer;

class TransferResource extends OperationResource
{
    protected static ?string $model = Operation::class;

    protected static ?string $parentResource = ManufacturingOrderResource::class;

    protected static ?string $slug = 'transfers';

    protected static ?string $recordTitleAttribute = 'name';

    protected static bool $shouldRegisterNavigation = false;

    protected static ?string $cluster = Operations::class;

    public static function canAccess(): bool
    {
        return ManufacturingOrderResource::canAccess();
    }

    public static function getParentResourceRegistration(): ?ParentResourceRegistration
    {
        return ManufacturingOrderResource::asParent()
            ->relationship('inventoryOperations');
    }

    public static function scopeEloquentQueryToParent(Builder $query, Model $parentRecord): Builder
    {
        return $query
            ->whereNotNull('procurement_group_id')
            ->where('procurement_group_id', $parentRecord->procurement_group_id);
    }

    public static function getUrl(?string $name = 'index', array $parameters = [], bool $isAbsolute = true, ?string $panel = null, ?Model $tenant = null, bool $shouldGuessMissingParameters = false, ?string $configuration = null): string
    {
        return forward_static_call(
            [Resource::class, 'getUrl'],
            $name,
            $parameters,
            $isAbsolute,
            $panel,
            $tenant,
            $shouldGuessMissingParameters,
            $configuration,
        );
    }

    public static function table(Table $table): Table
    {
        return OperationResource::table($table)
            ->recordActions([
                ActionGroup::make([
                    ViewAction::make()
                        ->url(fn ($record): string => static::getUrl('view', ['record' => $record], shouldGuessMissingParameters: true)),
                    EditAction::make()
                        ->url(fn ($record): string => static::getUrl('edit', ['record' => $record], shouldGuessMissingParameters: true)),
                ]),
            ]);
    }

    public static function getRecordSubNavigation(Page $page): array
    {
        return $page->generateNavigationItems([
            ViewTransfer::class,
            EditTransfer::class,
            ManageMoves::class,
        ]);
    }

    public static function getPages(): array
    {
        return [
            'view' => ViewTransfer::route('/{record}/view'),
            'edit' => EditTransfer::route('/{record}/edit'),
            'moves' => ManageMoves::route('/{record}/moves'),
        ];
    }
}
