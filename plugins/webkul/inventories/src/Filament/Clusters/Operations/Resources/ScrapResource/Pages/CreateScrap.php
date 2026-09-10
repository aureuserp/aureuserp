<?php

namespace Webkul\Inventory\Filament\Clusters\Operations\Resources\ScrapResource\Pages;

use Filament\Resources\Pages\CreateRecord;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Support\Facades\Auth;
use Webkul\Inventory\Enums\ScrapState;
use Webkul\Inventory\Filament\Clusters\Operations\Resources\ScrapResource;
use Webkul\Inventory\Models\Location;
use Webkul\Inventory\Models\Product;
use Webkul\Inventory\Models\Warehouse;
use Webkul\Support\Filament\Concerns\HandlesCrossCompanyException;

class CreateScrap extends CreateRecord
{
    use HandlesCrossCompanyException;

    protected ?bool $hasDatabaseTransactions = true;

    public function getSubNavigation(): array
    {
        if (filled($cluster = static::getCluster())) {
            return $this->generateNavigationItems($cluster::getClusteredComponents());
        }

        return [];
    }

    protected static string $resource = ScrapResource::class;

    public function getTitle(): string|Htmlable
    {
        return __('inventories::filament/clusters/operations/resources/scrap/pages/create-scrap.title');
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('edit', ['record' => $this->getRecord()]);
    }

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['uom_id'] ??= Product::find($data['product_id'])->uom_id;

        $data['state'] ??= ScrapState::DRAFT;

        $data['creator_id'] = Auth::id();

        $data['source_location_id'] ??= Warehouse::where(owned_by_company(current_company_id()))->value('lot_stock_location_id');

        $sourceCompanyId = Location::find($data['source_location_id'])?->company_id;

        $data['destination_location_id'] ??= Location::where('is_scrap', true)
            ->when($sourceCompanyId, fn ($query, $companyId) => $query->where(owned_by_company($companyId)))
            ->value('id');

        $data['company_id'] ??= $sourceCompanyId ?? current_company_id();

        return $data;
    }
}
