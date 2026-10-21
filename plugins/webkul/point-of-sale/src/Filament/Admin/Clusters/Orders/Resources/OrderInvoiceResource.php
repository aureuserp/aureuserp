<?php

namespace Webkul\PointOfSale\Filament\Admin\Clusters\Orders\Resources;

use Filament\Facades\Filament;
use Filament\Resources\Pages\Page;
use Filament\Resources\ParentResourceRegistration;
use Illuminate\Database\Eloquent\Builder;
use Webkul\Account\Enums\MoveType;
use Webkul\Invoice\Filament\Clusters\Customers\Resources\InvoiceResource as BaseInvoiceResource;
use Webkul\PointOfSale\Filament\Admin\Clusters\Orders;
use Webkul\PointOfSale\Filament\Admin\Clusters\Orders\Resources\OrderInvoiceResource\Pages\EditInvoice;
use Webkul\PointOfSale\Filament\Admin\Clusters\Orders\Resources\OrderInvoiceResource\Pages\ManagePayments;
use Webkul\PointOfSale\Filament\Admin\Clusters\Orders\Resources\OrderInvoiceResource\Pages\ViewInvoice;
use Webkul\PointOfSale\Models\Invoice;

class OrderInvoiceResource extends BaseInvoiceResource
{
    protected static ?string $model = Invoice::class;

    protected static ?string $parentResource = OrderResource::class;

    protected static ?string $slug = 'invoices';

    protected static ?string $recordTitleAttribute = 'name';

    protected static bool $shouldRegisterNavigation = false;

    protected static ?string $cluster = Orders::class;

    public static function canAccess(): bool
    {
        $parentResource = static::$parentResource;

        return $parentResource::canAccess();
    }

    public static function getEloquentQuery(): Builder
    {
        $query = static::getModel()::query();

        if (! static::isScopedToTenant()) {
            $panel = Filament::getCurrentOrDefaultPanel();

            if ($panel?->hasTenancy()) {
                $query->withoutGlobalScope($panel->getTenancyScopeName());
            }
        }

        return $query
            ->whereIn('move_type', [MoveType::OUT_INVOICE, MoveType::OUT_REFUND])
            ->orderByDesc('id');
    }

    public static function getParentResourceRegistration(): ?ParentResourceRegistration
    {
        return OrderResource::asParent()
            ->relationship('invoices')
            ->inverseRelationship('posOrder');
    }

    public static function getRecordSubNavigation(Page $page): array
    {
        return $page->generateNavigationItems([
            ViewInvoice::class,
            EditInvoice::class,
            ManagePayments::class,
        ]);
    }

    public static function getPages(): array
    {
        return [
            'view'     => ViewInvoice::route('/{record}/view'),
            'edit'     => EditInvoice::route('/{record}/edit'),
            'payments' => ManagePayments::route('/{record}/payments'),
        ];
    }
}
