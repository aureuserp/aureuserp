<?php

namespace Webkul\Purchase\Filament\Admin\Clusters\Orders\Resources;

use Filament\Actions\ActionGroup;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Resources\ParentResourceRegistration;
use Filament\Resources\Pages\Page;
use Filament\Tables\Table;
use Webkul\Inventory\Filament\Clusters\Operations\Resources\OperationResource;
use Webkul\Inventory\Filament\Clusters\Operations\Resources\ReceiptResource as BaseReceiptResource;
use Webkul\Inventory\Models\Receipt;
use Webkul\Purchase\Filament\Admin\Clusters\Orders;
use Webkul\Purchase\Filament\Admin\Clusters\Orders\Resources\QuotationReceiptResource\Pages\EditReceipt;
use Webkul\Purchase\Filament\Admin\Clusters\Orders\Resources\QuotationReceiptResource\Pages\ManageMoves;
use Webkul\Purchase\Filament\Admin\Clusters\Orders\Resources\QuotationReceiptResource\Pages\ViewReceipt;

class QuotationReceiptResource extends BaseReceiptResource
{
    protected static ?string $model = Receipt::class;

    protected static ?string $parentResource = QuotationResource::class;

    protected static ?string $slug = 'receipts';

    protected static ?string $recordTitleAttribute = 'name';

    protected static bool $shouldRegisterNavigation = false;

    protected static ?string $cluster = Orders::class;

    public static function canAccess(): bool
    {
        $parentResource = static::$parentResource;

        return $parentResource::canAccess();
    }

    public static function getParentResourceRegistration(): ?ParentResourceRegistration
    {
        return QuotationResource::asParent()
            ->relationship('operations')
            ->inverseRelationship('purchaseOrders');
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
            ViewReceipt::class,
            EditReceipt::class,
            ManageMoves::class,
        ]);
    }

    public static function getPages(): array
    {
        return [
            'view' => ViewReceipt::route('/{record}/view'),
            'edit' => EditReceipt::route('/{record}/edit'),
            'moves' => ManageMoves::route('/{record}/moves'),
        ];
    }
}
