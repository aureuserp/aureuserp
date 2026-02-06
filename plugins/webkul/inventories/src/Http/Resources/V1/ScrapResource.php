<?php

namespace Webkul\Inventory\Http\Resources\V1;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Webkul\Partner\Http\Resources\V1\PartnerResource;
use Webkul\Product\Http\Resources\V1\ProductResource;
use Webkul\Security\Http\Resources\V1\UserResource;
use Webkul\Support\Http\Resources\V1\CompanyResource;
use Webkul\Support\Http\Resources\V1\UOMResource;

class ScrapResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id'                   => $this->id,
            'name'                 => $this->name,
            'origin'               => $this->origin,
            'state'                => $this->state?->value,
            'qty'                  => (float) $this->qty,
            'should_replenish'     => (bool) $this->should_replenish,
            'closed_at'            => $this->closed_at,
            'product'              => ProductResource::make($this->whenLoaded('product')),
            'uom'                  => UOMResource::make($this->whenLoaded('uom')),
            'lot'                  => LotResource::make($this->whenLoaded('lot')),
            'package'              => PackageResource::make($this->whenLoaded('package')),
            'partner'              => PartnerResource::make($this->whenLoaded('partner')),
            'operation'            => OperationResource::make($this->whenLoaded('operation')),
            'source_location'      => LocationResource::make($this->whenLoaded('sourceLocation')),
            'destination_location' => LocationResource::make($this->whenLoaded('destinationLocation')),
            'company'              => CompanyResource::make($this->whenLoaded('company')),
            'creator'              => UserResource::make($this->whenLoaded('creator')),
            'created_at'           => $this->created_at,
            'updated_at'           => $this->updated_at,
        ];
    }
}
