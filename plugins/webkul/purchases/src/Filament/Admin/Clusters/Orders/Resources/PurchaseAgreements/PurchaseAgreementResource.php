<?php

namespace Webkul\Purchase\Filament\Admin\Clusters\Orders\Resources\PurchaseAgreements;

use BackedEnum;
use Filament\Pages\Enums\SubNavigationPosition;
use Filament\Resources\Pages\Page;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Table;
use Webkul\Field\Filament\Traits\HasCustomFields;
use Webkul\Purchase\Filament\Admin\Clusters\Orders;
use Webkul\Purchase\Filament\Admin\Clusters\Orders\Resources\PurchaseAgreements\Pages\CreatePurchaseAgreement;
use Webkul\Purchase\Filament\Admin\Clusters\Orders\Resources\PurchaseAgreements\Pages\EditPurchaseAgreement;
use Webkul\Purchase\Filament\Admin\Clusters\Orders\Resources\PurchaseAgreements\Pages\ListPurchaseAgreements;
use Webkul\Purchase\Filament\Admin\Clusters\Orders\Resources\PurchaseAgreements\Pages\ViewPurchaseAgreement;
use Webkul\Purchase\Filament\Admin\Clusters\Orders\Resources\PurchaseAgreements\Schemas\PurchaseAgreementForm;
use Webkul\Purchase\Filament\Admin\Clusters\Orders\Resources\PurchaseAgreements\Schemas\PurchaseAgreementInfolist;
use Webkul\Purchase\Filament\Admin\Clusters\Orders\Resources\PurchaseAgreements\Tables\PurchaseAgreementsTable;
use Webkul\Purchase\Models\Requisition;
use Webkul\Purchase\Settings\OrderSettings;

class PurchaseAgreementResource extends Resource
{
    use HasCustomFields;

    protected static ?string $model = Requisition::class;

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-document-check';

    protected static bool $shouldRegisterNavigation = true;

    protected static ?string $cluster = Orders::class;

    protected static ?int $navigationSort = 3;

    protected static ?\Filament\Pages\Enums\SubNavigationPosition $subNavigationPosition = SubNavigationPosition::Top;

    protected static ?string $recordTitleAttribute = 'name';

    public static function getNavigationLabel(): string
    {
        return __('purchases::filament/admin/clusters/orders/resources/purchase-agreement.navigation.title');
    }

    public static function isDiscovered(): bool
    {
        if (app()->runningInConsole()) {
            return true;
        }

        return app(OrderSettings::class)->enable_purchase_agreements;
    }

    public static function form(Schema $schema): Schema
    {
        return PurchaseAgreementForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return PurchaseAgreementsTable::configure($table);
    }

    public static function infolist(Schema $schema): Schema
    {
        return PurchaseAgreementInfolist::configure($schema);
    }

    public static function getRecordSubNavigation(Page $page): array
    {
        return $page->generateNavigationItems([
            ViewPurchaseAgreement::class,
            EditPurchaseAgreement::class,
        ]);
    }

    public static function getPages(): array
    {
        return [
            'index'  => ListPurchaseAgreements::route('/'),
            'create' => CreatePurchaseAgreement::route('/create'),
            'edit'   => EditPurchaseAgreement::route('/{record}/edit'),
            'view'   => ViewPurchaseAgreement::route('/{record}/view'),
        ];
    }
}
