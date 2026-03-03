<?php

namespace Webkul\Purchase\Filament\Admin\Clusters\Orders\Resources\PurchaseAgreementResource\Pages;

use Filament\Actions\ViewAction;
use Filament\Resources\Pages\ManageRelatedRecords;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Webkul\Purchase\Enums\OrderState;
use Webkul\Purchase\Filament\Admin\Clusters\Orders\Resources\PurchaseAgreementResource;
use Webkul\Purchase\Filament\Admin\Clusters\Orders\Resources\QuotationResource;
use Webkul\Support\Traits\HasRecordNavigationTabs;

class ManageRfqs extends ManageRelatedRecords
{
    use HasRecordNavigationTabs;

    protected static string $resource = PurchaseAgreementResource::class;

    protected static string $relationship = 'orders';

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-document-text';

    public static function getNavigationLabel(): string
    {
        return __('purchases::filament/admin/clusters/orders/resources/purchase-agreement/pages/manage-frqs.navigation.title');
    }

    public function table(Table $table): Table
    {
        return QuotationResource::table($table)
            ->modifyQueryUsing(fn (Builder $query) => $query
                ->where('requisition_id', $this->record->getKey()))
            ->recordUrl(fn ($record) => QuotationResource::getUrl('view', ['record' => $record]))
            ->recordActions([
                ViewAction::make()
                    ->url(fn ($record) => QuotationResource::getUrl('view', ['record' => $record]))
                    ->openUrlInNewTab(false),
            ]);
    }
}
