<?php

namespace Webkul\Inventory\Http\Resources\V1;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Webkul\Partner\Http\Resources\V1\PartnerResource;
use Webkul\Security\Http\Resources\V1\UserResource;
use Webkul\Support\Http\Resources\V1\CompanyResource;

class RuleResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id'                         => $this->id,
            'name'                       => $this->name,
            'sort'                       => $this->sort,
            'route_sort'                 => $this->route_sort,
            'delay'                      => $this->delay,
            'group_propagation_option'   => $this->group_propagation_option?->value,
            'action'                     => $this->action?->value,
            'procure_method'             => $this->procure_method?->value,
            'auto'                       => $this->auto?->value,
            'push_domain'                => $this->push_domain,
            'location_dest_from_rule'    => (bool) $this->location_dest_from_rule,
            'propagate_cancel'           => (bool) $this->propagate_cancel,
            'propagate_carrier'          => (bool) $this->propagate_carrier,
            'source_location'            => LocationResource::make($this->whenLoaded('sourceLocation')),
            'destination_location'       => LocationResource::make($this->whenLoaded('destinationLocation')),
            'route'                      => RouteResource::make($this->whenLoaded('route')),
            'operation_type'             => OperationTypeResource::make($this->whenLoaded('operationType')),
            'partner_address'            => PartnerResource::make($this->whenLoaded('partnerAddress')),
            'warehouse'                  => WarehouseResource::make($this->whenLoaded('warehouse')),
            'propagate_warehouse'        => WarehouseResource::make($this->whenLoaded('propagateWarehouse')),
            'company'                    => CompanyResource::make($this->whenLoaded('company')),
            'creator'                    => UserResource::make($this->whenLoaded('creator')),
            'created_at'                 => $this->created_at,
            'updated_at'                 => $this->updated_at,
            'deleted_at'                 => $this->deleted_at,
        ];
    }
}
