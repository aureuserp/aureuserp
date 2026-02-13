<?php

namespace Webkul\Inventory\Http\Resources\V1;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Webkul\Partner\Http\Resources\V1\PartnerResource;
use Webkul\Product\Http\Resources\V1\ProductResource;
use Webkul\Security\Http\Resources\V1\UserResource;
use Webkul\Support\Http\Resources\V1\CompanyResource;
use Webkul\Support\Http\Resources\V1\UOMResource;

class MoveLineResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id'                      => $this->id,
            'lot_name'                => $this->lot_name,
            'state'                   => $this->state?->value,
            'reference'               => $this->reference,
            'picking_description'     => $this->picking_description,
            'qty'                     => (float) $this->qty,
            'uom_qty'                 => (float) $this->uom_qty,
            'is_picked'               => (bool) $this->is_picked,
            'scheduled_at'            => $this->scheduled_at,
            'move_id'                 => $this->move_id,
            'operation_id'            => $this->operation_id,
            'product_id'              => $this->product_id,
            'uom_id'                  => $this->uom_id,
            'package_id'              => $this->package_id,
            'result_package_id'       => $this->result_package_id,
            'lot_id'                  => $this->lot_id,
            'partner_id'              => $this->partner_id,
            'source_location_id'      => $this->source_location_id,
            'destination_location_id' => $this->destination_location_id,
            'company_id'              => $this->company_id,
            'creator_id'              => $this->creator_id,
            'created_at'              => $this->created_at,
            'updated_at'              => $this->updated_at,
            'move'                    => new MoveResource($this->whenLoaded('move')),
            'operation'               => new OperationResource($this->whenLoaded('operation')),
            'product'                 => new ProductResource($this->whenLoaded('product')),
            'uom'                     => new UOMResource($this->whenLoaded('uom')),
            'package'                 => new PackageResource($this->whenLoaded('package')),
            'result_package'          => new PackageResource($this->whenLoaded('resultPackage')),
            'lot'                     => new LotResource($this->whenLoaded('lot')),
            'partner'                 => new PartnerResource($this->whenLoaded('partner')),
            'source_location'         => new LocationResource($this->whenLoaded('sourceLocation')),
            'destination_location'    => new LocationResource($this->whenLoaded('destinationLocation')),
            'company'                 => new CompanyResource($this->whenLoaded('company')),
            'creator'                 => new UserResource($this->whenLoaded('creator')),
        ];
    }
}
