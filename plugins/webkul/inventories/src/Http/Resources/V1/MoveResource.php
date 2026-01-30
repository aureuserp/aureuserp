<?php

namespace Webkul\Inventory\Http\Resources\V1;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Webkul\Partner\Http\Resources\V1\PartnerResource;
use Webkul\Product\Http\Resources\V1\PackagingResource;
use Webkul\Product\Http\Resources\V1\ProductResource;
use Webkul\Security\Http\Resources\V1\UserResource;
use Webkul\Support\Http\Resources\V1\CompanyResource;
use Webkul\Support\Http\Resources\V1\UOMResource;

class MoveResource extends JsonResource
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
            'name'                    => $this->name,
            'state'                   => $this->state?->value,
            'origin'                  => $this->origin,
            'procure_method'          => $this->procure_method,
            'reference'               => $this->reference,
            'description_picking'     => $this->description_picking,
            'next_serial'             => $this->next_serial,
            'next_serial_count'       => $this->next_serial_count,
            'is_favorite'             => (bool) $this->is_favorite,
            'product_qty'             => (float) $this->product_qty,
            'product_uom_qty'         => (float) $this->product_uom_qty,
            'quantity'                => (float) $this->quantity,
            'is_picked'               => (bool) $this->is_picked,
            'is_scraped'              => (bool) $this->is_scraped,
            'is_inventory'            => (bool) $this->is_inventory,
            'is_refund'               => (bool) $this->is_refund,
            'deadline'                => $this->deadline,
            'reservation_date'        => $this->reservation_date,
            'scheduled_at'            => $this->scheduled_at,
            'product_id'              => $this->product_id,
            'uom_id'                  => $this->uom_id,
            'source_location_id'      => $this->source_location_id,
            'destination_location_id' => $this->destination_location_id,
            'final_location_id'       => $this->final_location_id,
            'partner_id'              => $this->partner_id,
            'operation_id'            => $this->operation_id,
            'rule_id'                 => $this->rule_id,
            'operation_type_id'       => $this->operation_type_id,
            'origin_returned_move_id' => $this->origin_returned_move_id,
            'restrict_partner_id'     => $this->restrict_partner_id,
            'warehouse_id'            => $this->warehouse_id,
            'product_packaging_id'    => $this->product_packaging_id,
            'scrap_id'                => $this->scrap_id,
            'company_id'              => $this->company_id,
            'creator_id'              => $this->creator_id,
            'created_at'              => $this->created_at,
            'updated_at'              => $this->updated_at,
            'product'                 => new ProductResource($this->whenLoaded('product')),
            'uom'                     => new UOMResource($this->whenLoaded('uom')),
            'source_location'         => new LocationResource($this->whenLoaded('sourceLocation')),
            'destination_location'    => new LocationResource($this->whenLoaded('destinationLocation')),
            'final_location'          => new LocationResource($this->whenLoaded('finalLocation')),
            'partner'                 => new PartnerResource($this->whenLoaded('partner')),
            'operation'               => new OperationResource($this->whenLoaded('operation')),
            'rule'                    => new RuleResource($this->whenLoaded('rule')),
            'operation_type'          => new OperationTypeResource($this->whenLoaded('operationType')),
            'origin_returned_move'    => new MoveResource($this->whenLoaded('originReturnedMove')),
            'restrict_partner'        => new PartnerResource($this->whenLoaded('restrictPartner')),
            'warehouse'               => new WarehouseResource($this->whenLoaded('warehouse')),
            'product_packaging'       => new PackagingResource($this->whenLoaded('productPackaging')),
            'scrap'                   => new ScrapResource($this->whenLoaded('scrap')),
            'company'                 => new CompanyResource($this->whenLoaded('company')),
            'creator'                 => new UserResource($this->whenLoaded('creator')),
        ];
    }
}
