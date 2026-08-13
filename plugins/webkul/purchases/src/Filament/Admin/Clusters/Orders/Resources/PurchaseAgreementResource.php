<?php

namespace Webkul\Purchase\Filament\Admin\Clusters\Orders\Resources;

use BackedEnum;
use Filament\Resources\Pages\Page;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Webkul\Field\Filament\Traits\HasCustomFields;
use Webkul\Product\Settings\ProductSettings;
use Webkul\Purchase\Enums\OrderState;
use Webkul\Purchase\Filament\Admin\Clusters\Orders;
use Webkul\Purchase\Filament\Admin\Clusters\Orders\Resources\PurchaseAgreementResource\Pages\CreatePurchaseAgreement;
use Webkul\Purchase\Filament\Admin\Clusters\Orders\Resources\PurchaseAgreementResource\Pages\EditPurchaseAgreement;
use Webkul\Purchase\Filament\Admin\Clusters\Orders\Resources\PurchaseAgreementResource\Pages\ListPurchaseAgreements;
use Webkul\Purchase\Filament\Admin\Clusters\Orders\Resources\PurchaseAgreementResource\Pages\ManageRfqs;
use Webkul\Purchase\Filament\Admin\Clusters\Orders\Resources\PurchaseAgreementResource\Pages\ViewPurchaseAgreement;
use Webkul\Purchase\Filament\Admin\Clusters\Orders\Resources\PurchaseAgreementResource\Schemas\PurchaseAgreementForm;
use Webkul\Purchase\Filament\Admin\Clusters\Orders\Resources\PurchaseAgreementResource\Schemas\PurchaseAgreementInfolist;
use Webkul\Purchase\Filament\Admin\Clusters\Orders\Resources\PurchaseAgreementResource\Tables\PurchaseAgreementsTable;
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

    protected static ?string $recordTitleAttribute = 'name';

    public static function getGloballySearchableAttributes(): array
    {
        return ['name', 'partner.name'];
    }

    public static function getGlobalSearchResultDetails(Model $record): array
    {
        return [
            __('purchases::filament/admin/clusters/orders/resources/purchase-agreement.global-search.vendor') => $record->partner?->name ?? '—',
            __('purchases::filament/admin/clusters/orders/resources/purchase-agreement.global-search.type')   => $record->type?->getLabel() ?? '—',
        ];
    }

    public static function getNavigationLabel(): string
    {
        return __('purchases::filament/admin/clusters/orders/resources/purchase-agreement.navigation.title');
    }

    public static function getModelLabel(): string
    {
        return __('purchases::filament/admin/clusters/orders/resources/purchase-agreement.navigation.title');
    }

    public static function isDiscovered(): bool
    {
        if (app()->runningInConsole()) {
            return true;
        }

        return static::getOrderSettings()->enable_purchase_agreements;
    }

    public static function form(Schema $schema): Schema
    {
        return PurchaseAgreementForm::configure($schema, static::getCustomFormFields());
    }

    public static function table(Table $table): Table
    {
        return PurchaseAgreementsTable::configure(
            $table,
            static::getCustomTableColumns(),
            static::getTableQueryBuilderConstraints(),
        );
    }

    public static function infolist(Schema $schema): Schema
    {
        return PurchaseAgreementInfolist::configure($schema, static::getCustomInfolistEntries());
    }

    public static function canBeConfirmed(Requisition $record): bool
    {
        return $record->lines()->exists();
    }

    public static function canBeClosed(Requisition $record): bool
    {
        return ! $record->orders()
            ->whereNotIn('state', [
                OrderState::DONE->value,
                OrderState::CANCELED->value,
            ])
            ->exists();
    }

    public static function getOrderSettings(): OrderSettings
    {
        return settings(OrderSettings::class);
    }

    public static function getProductSettings(): ProductSettings
    {
        return settings(ProductSettings::class);
    }

    public static function getRecordSubNavigation(Page $page): array
    {
        return $page->generateNavigationItems([
            ViewPurchaseAgreement::class,
            EditPurchaseAgreement::class,
            ManageRfqs::class,
        ]);
    }

    public static function getPages(): array
    {
        return [
            'index'  => ListPurchaseAgreements::route('/'),
            'create' => CreatePurchaseAgreement::route('/create'),
            'edit'   => EditPurchaseAgreement::route('/{record}/edit'),
            'view'   => ViewPurchaseAgreement::route('/{record}/view'),
            'rfqs'   => ManageRfqs::route('/{record}/rfqs'),
        ];
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->orderByDesc('id');
    }
}
