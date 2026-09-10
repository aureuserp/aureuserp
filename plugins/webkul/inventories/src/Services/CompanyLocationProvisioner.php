<?php

namespace Webkul\Inventory\Services;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Schema;
use Webkul\Inventory\Enums\LocationType;
use Webkul\Inventory\Models\Location;
use Webkul\Support\Models\Company;
use Webkul\Support\Models\Scopes\CompanyScope;

class CompanyLocationProvisioner
{
    public function provision(Company $company): void
    {
        if (! Schema::hasTable('inventories_locations')) {
            return;
        }

        $parentId = $this->virtualLocationsParentId();

        if ($parentId === null) {
            return;
        }

        foreach ($this->definitions() as $definition) {
            Location::withoutGlobalScope(CompanyScope::class)->firstOrCreate(
                [
                    'type'       => $definition['type'],
                    'is_scrap'   => $definition['is_scrap'],
                    'company_id' => $company->id,
                ],
                [
                    'name'       => $definition['name'],
                    'parent_id'  => $parentId,
                    'creator_id' => $company->creator_id ?? Auth::id(),
                ]
            );
        }
    }

    /**
     * @return array<int, array<string, mixed>>
     */
    protected function definitions(): array
    {
        return [
            ['type' => LocationType::INVENTORY, 'is_scrap' => false, 'name' => 'Inventory Adjustment'],
            ['type' => LocationType::PRODUCTION, 'is_scrap' => false, 'name' => 'Production'],
            ['type' => LocationType::INVENTORY, 'is_scrap' => true, 'name' => 'Scrap'],
        ];
    }

    protected function virtualLocationsParentId(): ?int
    {
        return Location::withoutGlobalScope(CompanyScope::class)
            ->whereIn('type', [LocationType::PRODUCTION, LocationType::INVENTORY])
            ->whereNotNull('parent_id')
            ->orderBy('id')
            ->value('parent_id');
    }
}
