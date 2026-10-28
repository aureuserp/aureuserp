<?php

namespace Webkul\Contact\Filament\Resources;

use Filament\Resources\Pages\Page;
use Filament\Resources\ParentResourceRegistration;
use Webkul\Account\Filament\Resources\InvoiceResource as BaseInvoiceResource;
use Webkul\Account\Models\Invoice;
use Webkul\Contact\Filament\Resources\ContactInvoiceResource\Pages\EditInvoice;
use Webkul\Contact\Filament\Resources\ContactInvoiceResource\Pages\ManagePayments;
use Webkul\Contact\Filament\Resources\ContactInvoiceResource\Pages\ViewInvoice;

class ContactInvoiceResource extends BaseInvoiceResource
{
    protected static ?string $model = Invoice::class;

    protected static ?string $parentResource = PartnerResource::class;

    protected static ?string $slug = 'invoices';

    protected static ?string $recordTitleAttribute = 'name';

    protected static bool $shouldRegisterNavigation = false;

    public static function canAccess(): bool
    {
        return static::$parentResource::canAccess();
    }

    public static function getParentResourceRegistration(): ?ParentResourceRegistration
    {
        return PartnerResource::asParent()
            ->relationship('invoices')
            ->inverseRelationship('partner');
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
