<?php

namespace Webkul\Invoice\Filament\Clusters\Customer\Resources;

use Filament\Pages\SubNavigationPosition;
use Filament\Resources\Pages\Page;
use Webkul\Account\Filament\Resources\CreditNoteResource as BaseCreditNoteResource;
use Webkul\Invoice\Filament\Clusters\Customer;
use Webkul\Invoice\Filament\Clusters\Customer\Resources\CreditNotesResource\Pages;
use Webkul\Invoice\Models\CreditNote;
use Webkul\Security\Traits\HasResourcePermissionQuery;

class CreditNotesResource extends BaseCreditNoteResource
{
    use HasResourcePermissionQuery;

    protected static ?string $model = CreditNote::class;

    protected static bool $shouldRegisterNavigation = true;

    protected static ?string $cluster = Customer::class;

    protected static ?int $navigationSort = 2;

    protected static SubNavigationPosition $subNavigationPosition = SubNavigationPosition::Top;

    public static function getModelLabel(): string
    {
        return __('invoices::filament/clusters/customers/resources/credit-note.title');
    }

    public static function getNavigationLabel(): string
    {
        return __('invoices::filament/clusters/customers/resources/credit-note.navigation.title');
    }

    public static function getRecordSubNavigation(Page $page): array
    {
        return $page->generateNavigationItems([
            Pages\ViewCreditNote::class,
            Pages\EditCreditNotes::class,
        ]);
    }

    public static function getPages(): array
    {
        return [
            'index'  => Pages\ListCreditNotes::route('/'),
            'create' => Pages\CreateCreditNotes::route('/create'),
            'edit'   => Pages\EditCreditNotes::route('/{record}/edit'),
            'view'   => Pages\ViewCreditNote::route('/{record}'),
        ];
    }
}
