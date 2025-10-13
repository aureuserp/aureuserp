<?php

namespace Webkul\Purchase\Filament\Admin\Clusters\Orders\Resources;

use Filament\Resources\Pages\Page;
use Webkul\Purchase\Filament\Admin\Clusters\Orders;
use Webkul\Purchase\Filament\Admin\Clusters\Orders\Resources\QuotationResource\Pages\CreateQuotation;
use Webkul\Purchase\Filament\Admin\Clusters\Orders\Resources\QuotationResource\Pages\EditQuotation;
use Webkul\Purchase\Filament\Admin\Clusters\Orders\Resources\QuotationResource\Pages\ListQuotations;
use Webkul\Purchase\Filament\Admin\Clusters\Orders\Resources\QuotationResource\Pages\ManageBills;
use Webkul\Purchase\Filament\Admin\Clusters\Orders\Resources\QuotationResource\Pages\ManageReceipts;
use Webkul\Purchase\Filament\Admin\Clusters\Orders\Resources\QuotationResource\Pages\ViewQuotation;
use Webkul\Purchase\Models\Quotation;

class QuotationResource extends OrderResource
{
    protected static ?string $model = Quotation::class;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-document-text';

    protected static bool $shouldRegisterNavigation = true;

    protected static ?string $recordTitleAttribute = 'name';

    protected static ?int $navigationSort = 1;

    protected static ?string $cluster = Orders::class;

    public static function getNavigationLabel(): string
    {
        return __('purchases::filament/admin/clusters/orders/resources/quotation.navigation.title');
    }

    public static function getModelLabel(): string
    {
        return __('purchases::filament/admin/clusters/orders/resources/quotation.navigation.title');
    }

    public static function getRecordSubNavigation(Page $page): array
    {
        return $page->generateNavigationItems([
            ViewQuotation::class,
            EditQuotation::class,
            ManageBills::class,
            ManageReceipts::class,
        ]);
    }

    public static function getPages(): array
    {
        return [
            'index'    => ListQuotations::route('/'),
            'create'   => CreateQuotation::route('/create'),
            'view'     => ViewQuotation::route('/{record}'),
            'edit'     => EditQuotation::route('/{record}/edit'),
            'bills'    => ManageBills::route('/{record}/bills'),
            'receipts' => ManageReceipts::route('/{record}/receipts'),
        ];
    }
}
